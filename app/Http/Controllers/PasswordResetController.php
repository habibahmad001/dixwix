<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email does not exist.']);
        }

        // Generate a token and store it in the remember_token field
        $token = Str::random(60);
        $user->remember_token = $token;
        $user->save();

        // Send the reset link via email
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
        Mail::send('emails.password-reset', ['resetLink' => $resetLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset');
        });

        return back()->with('status', 'We have e-mailed your password reset link!');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('reset-password')->with(['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        // Validate the token using the remember_token field
        $user = User::where('email', $request->email)->where('remember_token', $request->token)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }

        // Reset password and clear the token
        $user->password = Hash::make($request->password);
        $user->remember_token = null;  // Clear the token after resetting the password
        $user->save();

        return redirect()->route('login')->with('status', 'Password has been reset!');
    }
}
