<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Akses;

use Illuminate\Http\Request;

class AksesController extends Controller
{
    public function bersihkan()
    {
        Akses::bersihkan();
        Desa::hapus_nonaktif_tdkterdaftar();
        return redirect('review/non-aktif');
    }
}
