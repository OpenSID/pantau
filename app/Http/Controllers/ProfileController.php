<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|max:255',
            'name' => 'required|max:255',
            'email' => 'required|max:255',
        ]);

        User::where('id', Auth::user()->id)
            ->update([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
            ]);

        return redirect()->back()->withAlert('Berhasil diperbarui');
    }

    public function resetPassword()
    {
        return view('profile.reset-password');
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        User::where('id', Auth::user()->id)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return redirect()->back()->withAlert('Berhasil diperbarui');
    }
}
