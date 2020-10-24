<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('user_model_api');
    $this->user = $this->user_model_api;
  }

  public function admin_get()
  {
    $admin_id = 1;
    $token = $this->input->get('token');
    $admin_token = $this->user->get_admin_token_from_id($admin_id);
    if ($token === $admin_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->user->api_get_all_user();
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
    $user_id = $this->input->get('id');
    $admin_token = $this->user->get_admin_token_from_id($admin_id);
    if ($token === $admin_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->set_response($decodedToken, REST_Controller::HTTP_OK);
        $response = $this->user->api_get_user($user_id);
        $this->response($response);
        return;
      }
    }
    $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
  }

}
