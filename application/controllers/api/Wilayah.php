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

  public function all_get()
  {
    $token = $this->input->get('token');
    $admin_id = $this->pelanggan->get_admin_id_from_token($token);
    $admin_token = $this->pelanggan->get_admin_token_from_id($admin_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $admin_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_all_wilayah();
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function kodedesa_get()
  {
    $kode = $this->input->get('kode');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_kodedesa($kode);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function kodekec_get()
  {
    $kode = $this->input->get('kode');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_kodekec($kode);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function kodekab_get()
  {
    $kode = $this->input->get('kode');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_kodekab($kode);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function kodeprov_get()
  {
    $kode = $this->input->get('kode');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_kodeprov($kode);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function namadesa_get()
  {
    $nama = $this->input->get('nama');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_namadesa($nama);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function namakec_get()
  {
    $nama = $this->input->get('nama');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_namakec($nama);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function namakab_get()
  {
    $nama = $this->input->get('nama');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_namakab($nama);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function namaprov_get()
  {
    $nama = $this->input->get('nama');
    $token = $this->input->get('token');
    $customer_id = $this->pelanggan->get_customer_id_from_token($token);
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    $invalidLogin = ['status' => '401 Unauthorized'];
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->wilayah->api_get_namaprov($nama);
        $this->response($response);
        return;
      }
    }
    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
  }

}
