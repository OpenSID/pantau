<?php

namespace App\Http\Controllers;

use App\Models\Akses;
use App\Models\Desa;

class AksesController extends Controller
{
    public function __invoke()
    {
        Akses::bersihkan();
        Desa::hapusNonaktifTidakTerdaftar();

        return redirect('laporan/desa');
    }
}
