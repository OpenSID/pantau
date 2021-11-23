<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wilayah_model_api extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('user_agent');
		$this->load->model('provinsi_model');
		$this->load->model('wilayah_model');
	}

	//API Halaman Pelanggan
	public function api_get_desa($id_desa)
	{
		$data = $this->db
			->where('id', $id_desa)
			->get('desa')
			->result_array();

		$response['KODE_WILAYAH'] = $data;

		return $response;
	}

	//API Halaman Identitas Desa
	private function list_desa_ajax_sql($cari = '')
	{
		$this->db
			->from('kode_wilayah');
		if ($cari) {
			$this->db
				->group_start()
					->like('nama_desa', $cari)
					->or_like('nama_kec', $cari)
				->group_end();
		}
	}

	public function list_desa_ajax($cari = '', $page = 1)
	{
		$this->list_desa_ajax_sql($cari);
		$jml = $this->db->select('count(id) as jml')
			->get()
			->row()->jml;

		$resultCount = 25;
		$offset = ($page - 1) * $resultCount;

		$this->list_desa_ajax_sql($cari);
		$this->db
			->distinct()
			->select('*')
			->limit($resultCount, $offset);
		$data = $this->db->get()->result_array();

		$desa = array();
		foreach ($data as $row)
		{
			$info_pilihan_desa = "{$row['nama_desa']} - {$row['nama_kec']} - {$row['nama_kab']} - {$row['nama_prov']}";
			$desa[] = array_merge($row, ['text' => $info_pilihan_desa]);
		}

		$endCount = $offset + $resultCount;
		$morePages = $endCount < $jml;

		$hasil = array(
			"results" => $desa,
			"pagination" => array(
				"more" => $morePages
			)
		);

		$response = $hasil;

		return $response;
	}

	public function api_get_ambildesa($id_desa)
	{
		$data = $this->db
			->where('id', $id_desa)
			->get('kode_wilayah')
			->result_array();

		$response['KODE_WILAYAH'] = $data;

		return $response;
	}

	// Ambil rincian wilayah desa dari tabel kode_wilayah
	public function desa_by_kode($kode_desa)
	{
		$desa = $this->db
			->select(['kode_prov', 'nama_prov', 'kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa'])
			->where('kode_desa', kode_wilayah($kode_desa))
			->limit(1)
			->get('kode_wilayah')->row();

		return $desa;
	}

	//API Peta Desa Pengguna OpenSID
	//Indonesia Bounding Box Coordinates : (95.2930261576, -10.3599874813, 141.03385176, 5.47982086834)
	public function api_get_geojson_prov($kode_desa)
	{
		$desa = $this->desa_by_kode($kode_desa);
		$db_results = $this->db
			->where('nama_provinsi', $desa->nama_prov)
			->where('lat BETWEEN -10 AND 6')
			->where('lng BETWEEN 95 AND 142')
			->where("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_hosting, tgl_akses_hosting), NOW()) <= 1") //sejak dua bulan yang lalu
			->where('url_hosting <>', null)
			->get('desa');

		$data = $db_results->result_array();
		$jml_desa_prov = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'jml_desa_prov' => $jml_desa_prov,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = array(
				'type' => 'Feature',
				'properties' => array(
					'desa' => $row['nama_desa'],
					'kec' => $row['nama_kecamatan'],
					'kab' => $row['nama_kabupaten'],
					'prov' => $row['nama_provinsi'],
					'web' => $row['url_hosting'],
					'alamat' => $row['alamat_kantor'],
				),
				'geometry' => array(
					'type' => 'Point',
					'coordinates' => array(
						$row['lng'],
						$row['lat']
					)
				)
			);
			array_push($geojson['features'], $marker);
		}
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_kab($kode_desa)
	{
		$desa = $this->desa_by_kode($kode_desa);
		$db_results = $this->db
			->where('nama_provinsi', $desa->nama_prov)
			->like('nama_kabupaten', $desa->nama_kab, 'after')
			->where('lat BETWEEN -10 AND 6')
			->where('lng BETWEEN 95 AND 142')
			->where("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_hosting, tgl_akses_hosting), NOW()) <= 1") //sejak dua bulan yang lalu
			->where('url_hosting <>', null)
			->get('desa');
		$data = $db_results->result_array();
		$jml_desa_kab = $db_results->num_rows();
		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'nama_kabupaten' => $desa->nama_kab,
			'jml_desa_kab' => $jml_desa_kab,
		);
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_kec($kode_desa)
	{
		$desa = $this->desa_by_kode($kode_desa);
		$db_results = $this->db
			->where('nama_provinsi', $desa->nama_prov)
			->like('nama_kabupaten', $desa->nama_kab, 'after')
			->like('nama_kecamatan', $desa->nama_kec, 'after')
			->where('lat BETWEEN -10 AND 6')
			->where('lng BETWEEN 95 AND 142')
			->where("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_hosting, tgl_akses_hosting), NOW()) <= 1") //sejak dua bulan yang lalu
			->where('url_hosting <>', null)
			->get('desa');
		$data = $db_results->result_array();
		$jml_desa_kec = $db_results->num_rows();
		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'nama_kabupaten' => $desa->nama_kab,
			'nama_kecamatan' => $desa->nama_kec,
			'jml_desa_kec' => $jml_desa_kec,
		);
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_neg()
	{
		$db_results = $this->db
			->where('lat BETWEEN -10 AND 6')
			->where('lng BETWEEN 95 AND 142')
			->where("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_hosting, tgl_akses_hosting), NOW()) <= 1") //sejak dua bulan yang lalu
			->where('url_hosting <>', null)
			->get('desa');
		$data = $db_results->result_array();
		$jml_desa = $db_results->num_rows();
		$geojson = array(
			'type'      => 'FeatureCollection',
			'nama_negara'   => "INDONESIA",
			'jml_desa'    => $jml_desa,
		);
		$response = $geojson;

		return $response;
	}

	//API Peta Desa Pengguna OpenSID by Selection
	private function desa_by_prov($kode_desa)
	{
		$desa = $this->db
			->select('nama_prov, nama_kab, nama_kec')
			->where('nama_prov', $kode_desa)
			->limit(1)
			->get('kode_wilayah')->row();

		return $desa;
	}

	private function desa_by_kab($kode_desa)
	{
		$desa = $this->db
			->select('nama_prov, nama_kab, nama_kec')
			->where('nama_kab', $kode_desa)
			->limit(1)
			->get('kode_wilayah')->row();
		return $desa;
	}

	private function desa_by_kec($kode_desa)
	{
		$desa = $this->db
			->select('nama_prov, nama_kab, nama_kec')
			->where('nama_kec', $kode_desa)
			->limit(1)
			->get('kode_wilayah')->row();
		return $desa;
	}

	private function desa_by_desa($kode_desa)
	{
		$desa = $this->db
			->select('nama_prov, nama_kab, nama_kec, nama_desa')
			->where('nama_desa', $kode_desa)
			->limit(1)
			->get('kode_wilayah')->row();

		return $desa;
	}

	// Ambil desa yg berisi data lokasi valid, yg diakses dalam 7 hari yg lalu
	private function api_get_geojson_select()
	{
		$db_results = $this->db
			->where("concat('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
			->where("concat('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
			->where('lat BETWEEN -10 AND 6')
			->where('lng BETWEEN 95 AND 142')
			->where("GREATEST(tgl_akses_lokal, tgl_akses_hosting) >= NOW()-INTERVAL 7 DAY")
			->get('desa');

		return $db_results;
	}

	private function set_marker_desa($row)
	{
		$marker = array(
			'type' => 'Feature',
			'properties' => array(
				'desa' => $row['nama_desa'],
				'kec' => $row['nama_kecamatan'],
				'kab' => $row['nama_kabupaten'],
				'prov' => $row['nama_provinsi'],
				'web' => $row['url_hosting'],
				'alamat' => $row['alamat_kantor'],
			),
			'geometry' => array(
				'type' => 'Point',
				'coordinates' => array(
					$row['lng'],
					$row['lat']
				)
			)
		);

		return $marker;
	}

	public function api_get_geojson_negara_select()
	{
		$db_results = $this->api_get_geojson_select();

		$data = $db_results->result_array();
		$jml_desa = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_negara' => "INDONESIA",
			'jml_desa' => $jml_desa,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = $this->set_marker_desa($row);
			array_push($geojson['features'], $marker);
		}

		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_prov_select($kode_desa)
	{
		$desa = $this->desa_by_prov($kode_desa);
		$this->db->where('nama_provinsi', $desa->nama_prov);
		$db_results = $this->api_get_geojson_select();

		$data = $db_results->result_array();
		$jml_desa_prov = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'jml_desa_prov' => $jml_desa_prov,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = $this->set_marker_desa($row);
			array_push($geojson['features'], $marker);
		}
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_kab_select($kode_desa)
	{
		$desa = $this->desa_by_kab($kode_desa);
		$this->db
			->where('nama_provinsi', $desa->nama_prov)
			->like('nama_kabupaten', $desa->nama_kab, 'after');
		$db_results = $this->api_get_geojson_select();

		$data = $db_results->result_array();
		$jml_desa_kab = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'nama_kabupaten' => $desa->nama_kab,
			'jml_desa_kab' => $jml_desa_kab,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = $this->set_marker_desa($row);
			array_push($geojson['features'], $marker);
		}
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_kec_select($kode_desa)
	{
		$desa = $this->desa_by_kec($kode_desa);
		$this->db
			->where('nama_provinsi', $desa->nama_prov)
			->like('nama_kabupaten', $desa->nama_kab, 'after')
			->like('nama_kecamatan', $desa->nama_kec, 'after');
		$db_results = $this->api_get_geojson_select();

		$data = $db_results->result_array();
		$jml_desa_kec = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'nama_kabupaten' => $desa->nama_kab,
			'nama_kecamatan' => $desa->nama_kec,
			'jml_desa_kec' => $jml_desa_kec,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = $this->set_marker_desa($row);
			array_push($geojson['features'], $marker);
		}
		$response = $geojson;

		return $response;
	}

	public function api_get_geojson_desa_select($kode_desa)
	{
		$desa = $this->desa_by_desa($kode_desa);
		$this->db
			->where('nama_provinsi', $desa->nama_prov)
			->like('nama_kabupaten', $desa->nama_kab, 'after')
			->like('nama_kecamatan', $desa->nama_kec, 'after')
			->like('nama_desa', $desa->nama_desa, 'after');
		$db_results = $this->api_get_geojson_select();

		$data = $db_results->result_array();
		$jml_desa_desa = $db_results->num_rows();

		$geojson = array(
			'type' => 'FeatureCollection',
			'nama_provinsi' => $desa->nama_prov,
			'nama_kabupaten' => $desa->nama_kab,
			'nama_kecamatan' => $desa->nama_kec,
			'nama_desa' => $desa->nama_desa,
			'jml_desa_desa' => $jml_desa_desa,
			'features' => array()
		);

		foreach ($data as $row)
		{
			$marker = $this->set_marker_desa($row);
			array_push($geojson['features'], $marker);
		}
		$response = $geojson;

		return $response;
	}

	public function list_provinsi($cari = null)
	{
		if ($cari) $this->db->like('nama_prov', $cari);

		$data = $this->db
			->select('nama_prov, kode_prov')
			->order_by('kode_prov', 'ASC')
			->group_by('kode_prov')
			->get('kode_wilayah')
			->result_array();

		return $data;
	}

	// TODO : Sederhanakan Parameter Sesuai Tingkatan
	public function list_kabupaten($provinsi = null, $cari = null)
	{
		if ($provinsi)
		{
			$this->db
				->group_start()
					->where('nama_prov', urldecode($provinsi))
					->or_where('kode_prov', $provinsi)
				->group_end();
		}

		if ($cari) $this->db->like('nama_kab', $cari);

		$data = $this->db
			->select('nama_prov, kode_prov, nama_kab, kode_kab')
			->group_by('kode_kab')
			->order_by('nama_kab', 'ASC')
			->get('kode_wilayah')
			->result_array();

		return $data;
	}

	// TODO : Sederhanakan Parameter Sesuai Tingkatan
	public function list_kecamatan($provinsi = null, $kabupaten = null, $cari = null)
	{
		if ($provinsi && $kabupaten)
		{
			$this->db
				->group_start()
					->where('nama_prov', urldecode($provinsi))
					->or_where('kode_prov', $provinsi)
				->group_end()
				->group_start()
					->where('nama_kab', urldecode($kabupaten))
					->or_where('kode_kab', $kabupaten)
				->group_end();
		}

		if ($cari) $this->db->like('nama_kec', $cari);

		$data = $this->db
			->select('nama_prov, kode_prov, nama_kab, kode_kab, nama_kec, kode_kec')
			->group_by('kode_kec')
			->order_by('nama_kec', 'ASC')
			->get('kode_wilayah')
			->result_array();

		return $data;
	}

	// TODO : Sederhanakan Parameter Sesuai Tingkatan
	public function list_desa($provinsi = null, $kabupaten = null, $kecamatan = null, $cari = null)
	{
		if ($provinsi && $kabupaten && $kecamatan)
		{
			$this->db
				->group_start()
					->where('nama_prov', urldecode($provinsi))
					->or_where('kode_prov', $provinsi)
				->group_end()
				->group_start()
					->where('nama_kab', urldecode($kabupaten))
					->or_where('kode_kab', $kabupaten)
				->group_end()
				->group_start()
					->where('nama_kec', urldecode($kecamatan))
					->or_where('kode_kec', $kecamatan)
				->group_end();
		}

		if ($cari) $this->db->like('nama_desa', $cari);

		$data = $this->db->select('nama_prov, kode_prov, nama_kab, kode_kab, nama_kec, kode_kec, nama_desa, kode_desa')
			->group_by('kode_desa')
			->order_by('nama_desa', 'ASC')
			->get('kode_wilayah')
			->result_array();

		return $data;
	}

	// TODO :  Buat per tingkat wilayag, hanya gunakan parameter cari dan kode.
	public function list_wilayah($kode = null, $cari = null)
	{
		if (strlen($kode) == 8) {
			$data = $this->list_desa(substr($kode, 0, 2), substr($kode, 0, 5), $kode, $cari);
		} elseif (strlen($kode) == 5) {
			$data = $this->list_kecamatan(substr($kode, 0, 2), $kode, $cari);
		} elseif (strlen($kode) == 2) {
			$data = $this->list_kabupaten($kode, $cari);
		} else {
			$data = $this->list_provinsi($cari);
		}

		$response = [
			"results" => $data,
			"pagination" => [
				"more" => false
			]
		];

		return $response;
	}
}
