<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function profile()
    {
        return view('admin.profile');
    }

    public function profileUpdate(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update([
                'username'=>$request->username,
                'name'=>$request->name,
                'email'=>$request->email
            ]);

        return redirect()->back()->withErrors(['msg'=>'Berhasil diperbarui']);
    }

    public function resetPassword()
    {
        return view('admin.reset-password');
    }

    public function resetPasswordUpdate(Request $request)
    {
        if($request->password != $request->re_password)
        {
            return redirect()->back()->withErrors(['msg' => 'Password tidak sama']);
        }
        User::where('id', Auth::user()->id)
            ->update([
                'password'=>Hash::make($request->password)
            ]);

        return redirect()->back()->withErrors(['msg'=>'Berhasil diperbarui']);
    }
}
