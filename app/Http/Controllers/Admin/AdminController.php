<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Websitemail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function admin_dashboard()
    {
        return view('admin.dashboard');
    }

    //User Login
    public function admin_login()
    {
        return view('admin.login');
    }
    public function admin_login_submit(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin_dashboard')->with('success', 'Login successful');
        }

        return redirect()->back()->with('error', 'Invalid credentials.');
    }
    public function admin_logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin_login')->with('Logout Successfully');
    }

    //Forget password
    public function admin_forget_password()
    {
        return view('admin.forget_password');
    }

    public function admin_forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin) {
            return redirect()->back()->with('error', 'Email not found.');
        }

        $token        = hash('sha256', time());
        $admin->token = $token;
        $admin->save();

        $reset_link = route('admin_reset_password', ['token' => $token, 'email' => $request->email]);
        $subject    = 'Reset Password';
        $body       = 'Please click the button below to reset your password:';

        Mail::to($request->email)->send(new Websitemail($subject, $body, $reset_link));

        return redirect()->back()->with('success', 'Reset password link has been sent to your email.');
    }

    //Reset Passwaord
    public function admin_reset_password($token, $email)
    {
        $admin = Admin::where('email', $email)->where('token', $token)->first();
        if (! $admin) {
            return redirect()->route('admin_login', 'Invalid token or email');
        }
        return view('admin.reset_password', compact('token', 'email'));
    }

    public function admin_reset_password_submit(Request $request, $token, $email)
    {
        $request->validate([
            'password'              => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        $admin = Admin::where('email', $email)->where('token', $token)->first();

        if (! $admin) {
            return redirect()->route('admin_login')->with('error', 'Invalid or expired reset link.');
        }

        // শুধুমাত্র ভ্যালিড হলে পাসওয়ার্ড আপডেট করো
        $admin->password = Hash::make($request->password);
        $admin->token    = '';
        $admin->update();

        return redirect()->route('admin_login')->with('success', 'Password reset successfully. Please login.');
    }

    //User Profile
    public function admin_profile()
    {
        return view('admin.profile');
    }

    public function admin_profile_submit(Request $request)
    {
        //Get current user
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'email' => 'required',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        //Handle photo upload
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,gif,svg|max:5120',
            ]);

            $final_name = 'user_' . time() . '.' . $request->photo->extension();

            // Delete old photo if exists
            if ($admin->photo != '' && file_exists(public_path('uploads/' . $admin->photo))) {
                unlink(public_path('uploads/' . $admin->photo));
            }

            $request->photo->move(public_path('uploads'), $final_name);
            $admin->photo = $final_name;
        }
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|confirmed',
            ]);
            $admin->password = Hash::make($request->password);
        }

        // Update other fields
        $admin->name  = $request->name;
        $admin->email = $request->email;

        $admin->save();

        return redirect()->back()->with('success', 'Profile updated successfully');

    }
}