<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Wilayah_model_api extends CI_Model
{
  public function __construct()
  {
		parent::__construct();
		$this->load->database();
	}

  public function api_get_all_wilayah()
	{
    $data = $this->db->get('kode_wilayah')->result();
    $response['KODE_WILAYAH']=$data;
    return $response;
  }

	public function api_get_kodedesa($kode)
	{
    $data = $this->db
    ->where('kode_desa', $kode)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

	public function api_get_namadesa($nama)
	{
    $data = $this->db
    ->where('nama_desa', $nama)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

  public function api_get_kodekec($kode)
	{
    $data = $this->db
    ->where('kode_kec', $kode)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

	public function api_get_namakec($nama)
	{
    $data = $this->db
    ->where('nama_kec', $nama)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

  public function api_get_kodekab($kode)
	{
    $data = $this->db
    ->where('kode_kab', $kode)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

	public function api_get_namakab($nama)
	{
    $data = $this->db
    ->where('nama_kab', $nama)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE WILAYAH']=$data;
    return $response;
  }

  public function api_get_kodeprov($kode)
	{
    $data = $this->db
    ->where('kode_prov', $kode)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

  public function api_get_namaprov($nama)
	{
    $data = $this->db
    ->where('nama_prov', $nama)
    ->get('kode_wilayah')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

  public function api_get_desa($id_desa)
	{
    $data = $this->db
    ->where('id', $id_desa)
    ->get('desa')
    ->result_array();

    $response['KODE_WILAYAH']=$data;
    return $response;
  }

}
