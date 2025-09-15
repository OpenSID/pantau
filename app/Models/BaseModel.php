<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function scopeWilayahKhusus($query)
    {
        $provinsi = session('provinsi');
        $query->when($provinsi, function ($r) use ($provinsi) {
            $r->whereIn('kode_desa', function ($s) use ($provinsi) {
                $s->select('kode_desa')->from('kode_wilayah')->where('kode_prov', $provinsi->kode_prov);
            });
        });
    }
}
