<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenggunaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class PenggunaController extends Controller
{
    public function show()
    {
        return DataTables::of(User::get())
            ->addColumn('action', function ($data) {
                $edit = '<a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-modal" data-submit="'.url('akun-pengguna/'.$data->id).'" data-username="'.$data->username.'" data-name="'.$data->name.'" data-email="'.$data->email.'" data-id_grup="'.$data->id_grup.'"><i class="fas fa-pencil-alt"></i></a>';
                $delete = '<a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-submit="'.url('akun-pengguna/'.$data->id).'" data-name="'.$data->name.'"><i class="fas fa-trash"></i></a>';

                return '<div class="btn btn-group">'.$edit.$delete.'</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index()
    {
        return view('pengguna.index');
    }

    public function create()
    {
        return view('pengguna.form');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();

        return redirect()->back()->withAlert('Pengguna berhasil dihapus');
    }

    public function store(PenggunaRequest $request)
    {
        $simpan = new User;
        $simpan->id_grup = $request->id_grup;
        $simpan->name = $request->name;
        $simpan->username = $request->username;
        $simpan->email = $request->email;
        $simpan->password = Hash::make($request->password);
        $simpan->save();

        return redirect()->route('akun-pengguna.index')->withAlert('Pengguna baru berhasil tersimpan');
    }

    public function update(PenggunaRequest $request, $id)
    {
        User::where('id', $id)
            ->update([
                'id_grup' => $request->id_grup,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ]);

        return redirect()->route('akun-pengguna.index')->withAlert('Data pengguna berhasil diperbarui');
    }
}
