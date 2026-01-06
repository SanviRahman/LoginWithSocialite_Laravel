<?php
namespace App\Http\Controllers\Google;

use App\Http\Controllers\Controller;
use App\Mail\Websitemail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            // Check if user exists with this email
            $existingUser = User::where('email', $socialUser->email)->first();

            if ($existingUser) {
                // Check if user has Google ID
                if ($existingUser->google_id == null) {
                    $existingUser->google_id = $socialUser->id;
                    $existingUser->save();
                }

                // Check if account is verified
                if ($existingUser->status == 0) {
                    // Send verification email but don't login yet
                    $this->sendVerificationEmail($existingUser);
                    
                    return redirect()->route('user_login')
                        ->with('warning', 'Please verify your email address. A verification link has been sent to your email.');
                }

                // User is verified, login normally and go to dashboard
                Auth::login($existingUser);
                return redirect()->route('dashboard')->with('success', 'Login successful');
            }

            // Create new user with Google
            $token = hash('sha256', time());

            $newUser = User::create([
                'name'      => $socialUser->name,
                'email'     => $socialUser->email,
                'google_id' => $socialUser->id,
                'token'     => $token,
                'status'    => 0, // Set status to 0 (not verified)
                'password'  => bcrypt('123456dummy'),
            ]);

            // Send verification email
            $this->sendVerificationEmail($newUser);
            
            // Show success message but don't login
            return redirect()->route('user_login')
                ->with('warning', 'Please verify your email address. A verification link has been sent to your email.');

        } catch (\Exception $e) {
            return redirect()->route('user_login')
                ->with('error', 'Google login failed. Please try again.');
        }
    }

    private function sendVerificationEmail($user)
    {
        $verification_link = route('registration_verify', [
            'token' => $user->token,
            'email' => $user->email,
        ]);

        $subject = 'Email Verification Required';
        $body    = 'You registered using Google. Please verify your email by clicking the link below: <br><br>';
        $body   .= '<a href="' . $verification_link . '">Verify Email</a>';
        $body   .= '<br><br><strong>Note:</strong> After verification, you can login using Google or use the following temporary password: <strong>123456dummy</strong>';

        Mail::to($user->email)->send(new Websitemail($subject, $body, $verification_link));
    }

}