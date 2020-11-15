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
    $page = $this->input->get('page');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
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
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_ambildesa($id_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  //API Peta Desa Pengguna OpenSID
  public function geoprov_get()
  {
    $kode_desa = $this->input->get('kode_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_geojson_prov($kode_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function geokab_get()
  {
    $kode_desa = $this->input->get('kode_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_geojson_kab($kode_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function geokec_get()
  {
    $kode_desa = $this->input->get('kode_desa');
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_geojson_kec($kode_desa);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function geoneg_get()
  {
    $token = $this->input->get('token');
    $dev_token = $this->config->item('dev_token');
    $token_verify = password_verify($dev_token, $token);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token_verify) {
      $decodedToken = AUTHORIZATION::validateTimestamp($dev_token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_geojson_neg();
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

}
