<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Akses;

class AksesController extends Controller
{
    public function __invoke()
    {
        Akses::bersihkan();
        Desa::hapusNonaktifTidakTerdaftar();

        return redirect('laporan/desa');
    }
}
