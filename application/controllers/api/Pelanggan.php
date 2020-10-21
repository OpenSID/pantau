<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Pelanggan extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model(['pelanggan_model_api', 'referensi_model']);
    $this->pelanggan = $this->pelanggan_model_api;
  }

  public function admin_get()
  {
    $admin_id = 1;
    $token = $this->input->get('token');
    $admin_token = $this->pelanggan->get_admin_token_from_id($admin_id);
    if ($token === $admin_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->pelanggan->api_get_all_customer();
        $this->response($response);
        return;
      }
    }
    $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function admincs_get()
  {
    $admin_id = 1;
    $token = $this->input->get('token');
    $customer_id = $this->input->get('id');
    $admin_token = $this->pelanggan->get_admin_token_from_id($admin_id);
    if ($token === $admin_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->pelanggan->api_get_customer($customer_id);
        $this->response($response);
        return;
      }
    }
    $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
  }

  public function customer_get()
  {
    $customer_id = $this->input->get('id');
    $token = $this->input->get('token');
    $customer_token = $this->pelanggan->get_customer_token_from_id($customer_id);
    if ($token === $customer_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->pelanggan->api_get_customer($customer_id);
        $this->response($response);
        return;
      }
    }
    $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
  }

}
