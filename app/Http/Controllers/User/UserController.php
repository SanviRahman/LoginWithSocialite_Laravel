<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\Websitemail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function dashboard()
    {
        // Check if user is not verified
        if (Auth::guard('web')->user()->status == 0) {
            Auth::guard('web')->logout();
            return redirect()->route('user_login')
                ->with('error', 'Your email is not verified. Please verify your email first.');
        }

        // Check if user is logged in
        if (! Auth::guard('web')->check()) {
            return redirect()->route('user_login')->with('error', 'Please login first.');
        }

        return view('user.dashboard');
    }

    //User Registration
    public function registration()
    {
        return view('user.registration');
    }

    public function registration_submit(Request $request)
    {
        $request->validate([
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        //token generate
        $token = hash('sha256', time());

        //user create with token
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'token'    => $token,
            'status'   => 0,
        ]);

        //verification link
        $verification_link = route('registration_verify', [
            'token' => $token,
            'email' => $request->email,
        ]);

        $subject = 'Registration Verification';
        $body    = 'Click on the following button to verify your email';
        $body .= '<a href="' . $verification_link . '">Verify Email</a>';

        //Mail send
        Mail::to($request->email)->send(new Websitemail($subject, $body, $verification_link));
        return redirect()->back()->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    //User Verification
    public function registration_verify($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (! $user) {
            return redirect()->route('user_login')->with('error', 'Invalid token or email');
        }
        $user->token  = '';
        $user->status = 1;
        $user->save();

        return redirect()->route('user_login')->with('success', 'Email verification successful. You can login now.');
    }

    //User Login
    public function login()
    {
        return view('user.login');
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // First check if user exists
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid credentials.');
        }

        // Check if user is not verified (including social login users)
        if ($user->status == 0) {
            // If user has social ID but not verified
            if ($user->google_id || $user->facebook_id) {
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->with('warning', 'Your email is not verified. Please check your email for verification link.');
            } else {
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Your email is not verified. Please check your email for verification link.');
            }
        }

        if (Auth::guard('web')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Login successful');
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('user_login')->with('success', 'Logout Successfully');
    }

    //Forget password
    public function forget_password()
    {
        return view('user.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return redirect()->back()->with('error', 'Email not found.');
        }

        // Check if user is verified
        if ($user->status == 0) {
            return redirect()->back()->with('error', 'Your email is not verified. Please verify your email first.');
        }

        $token       = hash('sha256', time());
        $user->token = $token;
        $user->save();

        $reset_link = route('reset_password', ['token' => $token, 'email' => $request->email]);
        $subject    = 'Reset Password';
        $body       = 'Please click the button below to reset your password:';

        Mail::to($request->email)->send(new Websitemail($subject, $body, $reset_link));

        return redirect()->back()->with('success', 'Reset password link has been sent to your email.');
    }

    //Reset Password
    public function reset_password($token, $email)
    {
        $user = User::where('email', $email)->where('token', $token)->first();
        if (! $user) {
            return redirect()->route('user_login')->with('error', 'Invalid token or email');
        }
        return view('user.reset_password', compact('token', 'email'));
    }

    public function reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::where('email', $email)->where('token', $token)->first();

        if (! $user) {
            return redirect()->route('user_login')->with('error', 'Invalid or expired reset link.');
        }

        $user->password = Hash::make($request->password);
        $user->token    = '';
        $user->save();

        return redirect()->route('user_login')->with('success', 'Password reset successfully. Please login.');
    }

    //User Profile
    public function profile()
    {
        // Check if user is verified
        if (Auth::guard('web')->user()->status == 0) {
            Auth::guard('web')->logout();
            return redirect()->route('user_login')
                ->with('error', 'Your email is not verified. Please verify your email first.');
        }

        return view('user.profile');
    }

    public function profile_submit(Request $request)
    {
        //Get current user
        $user = Auth::guard('web')->user();

        // Check if user is verified
        if ($user->status == 0) {
            Auth::guard('web')->logout();
            return redirect()->route('user_login')
                ->with('error', 'Your email is not verified. Please verify your email first.');
        }

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        //Handle photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,gif,svg|max:5120',
            ]);

            $final_name = 'user_' . time() . '.' . $request->photo->extension();

            // Delete old photo if exists
            if ($user->photo != '' && file_exists(public_path('uploads/' . $user->photo))) {
                unlink(public_path('uploads/' . $user->photo));
            }

            $request->photo->move(public_path('uploads'), $final_name);
            $user->photo = $final_name;
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:6|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        // Update other fields
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->city    = $request->city;
        $user->country = $request->country;
        $user->state   = $request->state;
        $user->zip     = $request->zip;

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
