<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class PenggunaController extends Controller
{
    public function show()
    {
        $data = User::orderBy('id', 'desc');
        return DataTables::of($data)
            ->addColumn('action', function ($data){
                $edit = '<a class="btn btn-warning" data-toggle="modal" data-target="#edit-modal" data-submit="'.url('akun-pengguna/'.$data->id).'" data-username="'.$data->username.'" data-name="'.$data->name.'" data-email="'.$data->email.'" data-id_grup="'.$data->id_grup.'">Ubah</a>';
                $delete = '<a class="btn btn-danger" data-toggle="modal" data-target="#delete-modal" data-submit="'.url('akun-pengguna/'.$data->id).'" data-name="'.$data->name.'">Hapus</a>';
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
        return view('pengguna.add');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->back()->withAlert('Pengguna berhasil dihapus');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grup' => 'required',
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'email' => 'required|max:255',
        ]);

        $simpan = new User;
        $simpan->id_grup = $request->id_grup;
        $simpan->name = $request->name;
        $simpan->username = $request->username;
        $simpan->email = $request->email;
        $simpan->password = Hash::make($request->password);
        $simpan->save();
        return redirect()->route('akun-pengguna.index')->withAlert('Pengguna baru berhasil tersimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_grup' => 'required',
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'email' => 'required|max:255',
        ]);

        User::where('id', $id)
            ->update([
                'id_grup' => $request->id_grup,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        return redirect()->route('akun-pengguna.index')->withAlert('Data pengguna berhasil diperbarui');
    }
}
