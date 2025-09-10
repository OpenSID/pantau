<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenggunaRequest;
use App\Models\User;
use App\Models\UserGrup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class PenggunaController extends Controller
{
    public function show()
    {
        return DataTables::of(User::with(['grup', 'userRegionAccess' => static fn($q) => $q->with(['kabupaten'])])->get())
            ->addColumn('action', function ($data) {
                $defaultAccess = (object)['kode_provinsi' => ['id' => '', 'text' => ''], 'kode_kabupaten' => ['id' => '', 'text' => '']];
                if($data->userRegionAccess) {
                    $kabupaten = $data->userRegionAccess->kabupaten;
                    if($kabupaten) {
                        $defaultAccess->kode_provinsi = (object)['id' => $kabupaten->kode_prov, 'text' => $kabupaten->nama_prov];
                        $defaultAccess->kode_kabupaten = (object)['id' => $kabupaten->kode_kab, 'text' => $kabupaten->nama_kab];
                    }
                }
                $edit = '<a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit-modal" data-kode_provinsi=\''.json_encode($defaultAccess->kode_provinsi).'\' data-kode_kabupaten=\''.json_encode($defaultAccess->kode_kabupaten).'\' data-submit="'.url('akun-pengguna/'.$data->id).'" data-username="'.$data->username.'" data-name="'.$data->name.'" data-email="'.$data->email.'" data-id_grup="'.$data->id_grup.'"><i class="fas fa-pencil-alt"></i></a>';
                $delete = '<a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-submit="'.url('akun-pengguna/'.$data->id).'" data-name="'.$data->name.'"><i class="fas fa-trash"></i></a>';

                return '<div class="btn btn-group">'.$edit.$delete.'</div>';
            })->editColumn('id_grup', function($data) {
                return $data->grup ? $data->grup->nama : '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index()
    {
        $groups = UserGrup::pluck('nama', 'id');
        return view('pengguna.index', compact('groups'));
    }

    public function create()
    {
        $groups = UserGrup::pluck('nama', 'id');
        return view('pengguna.form', compact('groups'));
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

        // Simpan akses wilayah
        $provinsiAkses = $request->input('provinsi_akses');
        $kabupatenAkses = $request->input('kabupaten_akses');
        $userId = $simpan->id;
        // Jika tidak ada kabupaten, simpan akses provinsi saja

        $simpan->userRegionAccess()->create([
                    'kode_provinsi' => $provinsiAkses,
                    'kode_kabupaten' => $kabupatenAkses,
                ]);

        return redirect()->route('akun-pengguna.index')->withAlert('Pengguna baru berhasil tersimpan');
    }

    public function update(PenggunaRequest $request, $id)
    {
        Log::error('Update user called for ID: ', $request->all());
        $user = User::find($id);
        $user->update([
                'id_grup' => $request->id_grup,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ]);
        // Simpan akses wilayah
        $provinsiAkses = $request->input('provinsi_akses');
        $kabupatenAkses = $request->input('kabupaten_akses');
        if($provinsiAkses === null && $kabupatenAkses === null) {
            // Jika tidak ada akses wilayah, hapus data akses wilayah yang ada
            $user->userRegionAccess()->delete();
            return redirect()->route('akun-pengguna.index')->withAlert('Data pengguna berhasil diperbarui');
        }
        $user->userRegionAccess()->updateOrCreate(
            [
                'user_id' => $id
            ],
            [
                'kode_provinsi' => $provinsiAkses,
                'kode_kabupaten' => $kabupatenAkses,
            ]
        );
        return redirect()->route('akun-pengguna.index')->withAlert('Data pengguna berhasil diperbarui');
    }
}
