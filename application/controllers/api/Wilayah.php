<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Wilayah extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('wilayah_model_api');
    $this->load->helper('url');
    $this->load->model(['pelanggan_model_api', 'referensi_model']);
    $this->pelanggan = $this->pelanggan_model_api;
    $this->wilayah = $this->wilayah_model_api;
  }

  //API Halaman Pelanggan
  public function desa_get()
  {
    $id_desa = $this->input->get('id_desa');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_desa($id_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  //API Halaman Identitas Desa
  public function caridesa_get()
  {
    $cari = $this->input->get('q');
    $token = $this->input->get('token');
    $page = $this->input->get('page');
    $dev_token = $this->config->item('dev_token');
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->list_desa_ajax($cari, $page);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function ambildesa_get()
  {
    $id_desa = $this->input->get('id_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_ambildesa($id_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function kodedesa_get()
  {
    $cari = $this->input->get('kode');
    $token = $this->input->get('token');
    $page = $this->input->get('page');
    $dev_token = $this->config->item('dev_token');
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->desa_by_kode($cari);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  //API Peta Desa Pengguna OpenSID
  public function geoprov_get()
  {
    $this->geo_get('api_get_geojson_prov');
  }

  public function geokab_get()
  {
    $this->geo_get('api_get_geojson_kab');
  }

  public function geokec_get()
  {
    $this->geo_get('api_get_geojson_kec');
  }

  public function geoneg_get()
  {
    $this->geo_get('api_get_geojson_neg');
  }

  private function geo_get($api_get_wilayah)
  {
    $kode_desa = $this->input->get('kode_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        if ($api_get_wilayah == 'api_get_geojson_neg')
          $response = $this->wilayah->$api_get_wilayah();
        else
          $response = $this->wilayah->$api_get_wilayah($kode_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }


  public function geoneg_select_get()
  {
    $this->geo_select_get('api_get_geojson_negara_select');
  }

  public function geoprov_select_get()
  {
    $this->geo_select_get('api_get_geojson_prov_select');
  }

  public function geokab_select_get()
  {
    $this->geo_select_get('api_get_geojson_kab_select');
  }

  public function geokec_select_get()
  {
    $this->geo_select_get('api_get_geojson_kec_select');
  }

  public function geodesa_select_get()
  {
    $this->geo_select_get('api_get_geojson_desa_select');
  }

  private function geo_select_get($api_get_geojson_select)
  {
    $kode_desa = $this->input->get('kode_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        if ($ap_get_wilayah == 'api_get_geojson_negara_select')
          $response = $this->wilayah->$api_get_geojson_select();
        else
          $response = $this->wilayah->$api_get_geojson_select($kode_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

}
