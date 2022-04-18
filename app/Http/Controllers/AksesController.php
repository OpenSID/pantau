<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Akses;

use Illuminate\Http\Request;

class AksesController extends Controller
{
    public function __invoke()
    {
        Akses::bersihkan();
        Desa::hapusNonaktifTidakTerdaftar();
        return redirect('review/non-aktif');
    }

}
