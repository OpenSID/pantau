<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class NotifikasiController extends Controller
{
    public function getNotifikasi()
    {
        $data = Notifikasi::orderBy('id', 'desc');
        return DataTables::of($data)
            ->editColumn('isi', function($data){
                return htmlspecialchars_decode(stripslashes($data->isi));
            })
            ->addColumn('action', function ($data){
                $edit = '<a class="btn btn-warning" href="'.route('notifikasi.edit', ['id'=>$data->id]).'">Ubah</a>';
                $delete = '<a class="btn btn-danger" data-toggle="modal" data-target="#delete-modal" data-submit="'.url('notifikasi/'.$data->id).'" data-judul="'.$data->judul.'">Hapus</a>';
                return '<div class="btn btn-group">'.$edit.$delete.'</div>';
            })
            ->rawColumns(['isi','action'])
            ->make(true);
    }

    public function index()
    {
        return view('notifikasi.index');
    }

    public function create()
    {
        return view('notifikasi.add');
    }

    public function edit($id)
    {
        $data = Notifikasi::find($id);
        return view('notifikasi.edit', compact('data'));
    }

    public function destroy($id)
    {
        Notifikasi::where('id', $id)->delete();
        return redirect()->back()->withAlert('Notifikasi berhasil dihapus');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|max:100',
            'judul' => 'required|max:100',
            'jenis' => 'required',
            'isi' => 'required',
            'input_server' => 'required',
            'frekuensi' => 'required',
            'aktif' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = '';
            foreach ($validator->getMessageBag()->toArray() as $key => $value)
            {
                $messages .= $value[0] .' ';
            }
            return redirect()->back()->withDanger($messages);
        }

        $simpan = new Notifikasi;
        $simpan->aktif = $request->aktif;
        $simpan->frekuensi = $request->frekuensi;
        $simpan->kode = $request->kode;
        $simpan->judul = $request->judul;
        $simpan->jenis = $request->jenis;
        $simpan->server = $request->input_server;
        $simpan->isi = $request->isi;
        $simpan->save();
        return redirect()->route('notifikasi.index')->withAlert('Pesan berhasil terkirim');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|max:100',
            'judul' => 'required|max:100',
            'jenis' => 'required',
            'isi' => 'required',
            'input_server' => 'required',
            'frekuensi' => 'required',
            'aktif' => 'required',
        ]);

        if ($validator->fails())
        {
            $messages = '';
            foreach ($validator->getMessageBag()->toArray() as $key => $value)
            {
                $messages .= $value[0] .' ';
            }
            return redirect()->back()->withDanger($messages);
        }

        Notifikasi::where('id', $id)
            ->update([
                'aktif' => $request->aktif,
                'frekuensi' => $request->frekuensi,
                'kode' => $request->kode,
                'judul' => $request->judul,
                'jenis' => $request->jenis,
                'server' => $request->input_server,
                'isi' => $request->isi,
            ]);
        return redirect()->route('notifikasi.index')->withAlert('Pesan berhasil diperbarui');
    }
}
