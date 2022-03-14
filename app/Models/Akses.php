<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akses extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'akses';

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = ['desa_id', 'url_referrer', 'request_uri', 'client_ip', 'external_ip', 'opensid_version', 'tgl'];

    /** {@inheritdoc} */
    protected $cast = [
        'tgl' => 'datetime',
    ];

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public static function bersihkan()
    {
        //$list_desa = $this->db->select('id')->get('desa')->result_array();
        $list_desa = Desa::select('id')->get();
        foreach ($list_desa as $desa)
        {
            // Hapus semua akses kecuali yang terakhir
            //$akses_terakhir = $this->db->select('id')->where('desa_id', $desa['id'])->order_by('tgl DESC')->limit(1)->get('akses')->row();
            $akses_terakhir = self::select('id')->where('desa_id', $desa->id)->orderBy('tgl', 'DESC')->first();
            if ($akses_terakhir)
            {
                //$this->db->where('desa_id', $desa['id'])->where("id <>", $akses_terakhir->id)->delete('akses');
                self::select('id')->where('desa_id', '!=', $akses_terakhir->id)->delete();
            }
        }
    }
}
