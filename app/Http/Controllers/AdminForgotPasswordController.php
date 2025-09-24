<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user by email only (no user_role filter)
        $admin = User::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'Admin not found.']);
        }

        $newPassword = '12345';
        $admin->password = Hash::make($newPassword);
        $admin->save();

        return back()->with('success', 'Password reset successfully. New Password: ' . $newPassword);
    }
}
