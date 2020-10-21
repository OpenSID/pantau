<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Wilayah extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('desa_model');
    $this->load->model('wilayah_model_api');
    $this->load->model('kabupaten_model');
    $this->load->helper('url');
    $this->wilayah = $this->wilayah_model_api;
  }

  public function all_get()
  {
    $response = $this->wilayah->api_get_all_wilayah();
    return $this->response($response);
  }

  public function kodedesa_get()
  {
    $kode = $this->input->get('kode');
    $response = $this->wilayah->api_get_kodedesa($kode);
    return $this->response($response);
  }

  public function namadesa_get()
  {
    $nama = $this->input->get('nama');
    $response = $this->wilayah->api_get_namadesa($nama);
    return $this->response($response);
  }

  public function kodekec_get()
  {
    $kode = $this->input->get('kode');
    $response = $this->wilayah->api_get_kodekec($kode);
    return $this->response($response);
  }

  public function namakec_get()
  {
    $nama = $this->input->get('nama');
    $response = $this->wilayah->api_get_namakec($nama);
    return $this->response($response);
  }

  public function kodekab_get()
  {
    $kode = $this->input->get('kode');
    $response = $this->wilayah->api_get_kodekab($kode);
    return $this->response($response);
  }

  public function namakab_get()
  {
    $nama = $this->input->get('nama');
    $response = $this->wilayah->api_get_namakab($nama);
    return $this->response($response);
  }

  public function kodeprov_get()
  {
    $kode = $this->input->get('kode');
    $response = $this->wilayah->api_get_kodeprov($kode);
    return $this->response($response);
  }

  public function namaprov_get()
  {
    $nama = $this->input->get('nama');
    $response = $this->wilayah->api_get_namaprov($nama);
    return $this->response($response);
  }

}
