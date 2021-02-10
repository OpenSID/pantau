<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peta extends CI_Controller {

 private $_list_session;

 function __construct(){
    parent::__construct();
    $this->load->model('wilayah_model_api');
  }

	public function index()
	{
    $data['provinsi'] = $this->wilayah_model_api->list_provinsi();

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/nav');
		$this->load->view('dashboard/peta', $data);
		$this->load->view('dashboard/footer');
	}

  public function list_kab($provinsi = '')
	{
		$list_kab = $this->wilayah_model_api->list_kabupaten($provinsi);
		echo json_encode($list_kab);
	}

	public function list_kec($provinsi = '', $kabupaten = '')
	{
		$list_kec = $this->wilayah_model_api->list_kecamatan($provinsi, $kabupaten);
		echo json_encode($list_kec);
	}

  public function list_desa($provinsi = '', $kabupaten = '', $kecamatan = '')
	{
		$list_desa = $this->wilayah_model_api->list_desa($provinsi, $kabupaten, $kecamatan);
		echo json_encode($list_desa);
	}

}
