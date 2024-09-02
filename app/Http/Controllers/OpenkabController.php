<?php

namespace App\Http\Controllers;

use App\Models\Opendk;
use GuzzleHttp\Client;
use App\Models\Openkab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class OpenkabController extends Controller
{
    private $openkab;

    protected $baseRoute = 'openkab';

    protected $baseView = 'openkab';

    public function __construct()
    {
        $this->openkab = new Openkab();
        Config::set('title', $this->baseView.'');
    }

    public function kerjaSama(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Openkab::query())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.kerja_sama');
    }

    public function getWilayah()
    {
        $url = config('layanan.uri') . '/api/v1/pelanggan/diskominfo';
        try {
            $client = new Client();

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('layanan.token'),
                    'Accept' => 'application/json',
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);
            if (Openkab::upsert($data, ['kode_kab'])) {
                return back()->with('success', 'Data Wilayah berhasil diperbarui');
            }          
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Data Wilayah gagal diperbarui');
        }
    }
}
