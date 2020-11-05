<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wilayah_model_api extends CI_Model
{

  var $table = 'desa';
	var $column_order = array(null, null, 'nama_desa','nama_kecamatan','nama_kabupaten','nama_provinsi','url_hosting','versi_lokal','versi_hosting','tgl_akses'); //set column field database for datatable orderable
	var $column_order_kabupaten = array(null, 'nama_kabupaten','nama_provinsi','offline','online'); //set column field database for datatable orderable
	var $column_order_versi = array(null, 'versi','offline','online'); //set column field database for datatable orderable
	var $column_search = array('nama_desa','nama_kecamatan','nama_kabupaten','nama_provinsi'); //set column field database for datatable searchable
	var $order = array('id' => 'asc'); // default order

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

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

  //API Halaman Identitas Desa
  private function list_desa_ajax_sql($cari='')
	{
		$this->db
				->from('kode_wilayah');
		if ($cari)
		{
			$cari = $this->db->escape_like_str($cari);
			$this->db
				->group_start()
					->like('nama_desa', $cari)
					->or_like('nama_kec', $cari)
				->group_end();
		}
	}

	public function list_desa_ajax($cari='', $page=1)
	{
		$this->list_desa_ajax_sql($cari);
		$jml = $this->db->select('count(id) as jml')
				->get()->row()->jml;

    $resultCount = 25;
    $offset = ($page - 1) * $resultCount;

    $this->list_desa_ajax_sql($cari);
		$this->db
				->distinct()
				->select('id, nama_desa, nama_kec, nama_kab, nama_prov')
				->limit($resultCount, $offset);
		$data = $this->db->get()->result_array();

		$desa = array();
		foreach ($data as $row)
		{
			$nama = addslashes($row['nama_desa']);
			$info_pilihan_desa = "{$row['nama_desa']} - {$row['nama_kec']} - {$row['nama_kab']} - {$row['nama_prov']}";
			$desa[] = array('id' => $row['id'], 'text' => $info_pilihan_desa);
		}

    $endCount = $offset + $resultCount;
    $morePages = $endCount < $jml;

    $hasil = array(
      "results" => $desa,
      "pagination" => array(
        "more" => $morePages
      )
    );

    $response=$hasil;
    return $response;
	}

  public function api_get_ambildesa($id_desa)
	{
    $data = $this->db
    ->where('id', $id_desa)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

}
