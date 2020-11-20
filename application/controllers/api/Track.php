<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Track extends REST_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
  }

  public function index()
  {
  }

  public function desa_post()
  {
    $token = $this->input->get('token');
    $lisensi = fopen('LICENSE_OPENSID', 'r');
    $token_opensid = sha1(file_get_contents($lisensi));
    $invalidLogin = ['status' => 'Harap Akses melalui Aplikasi OpenSID'];
    $data = $_POST;

    if ($token === $token_opensid)
    {
      $this->set_response($token, REST_Controller::HTTP_OK);
      $this->load->model('desa_model');
      $data = $this->desa_model->normalkanData($data);
      if ($this->desa_model->abaikan($data)) return;
      $data['opensid_valid'] = TRUE;
      $this->load->model('akses_model');
      $result1 = $this->desa_model->insert($data);
      $result2 = $this->akses_model->insert($data);
      $this->load->model('notif_model');
      $notif = $this->notif_model->get_semua_notif($data['id']);
      $this->notif_model->non_aktifkan($notif, $data['id']);
      $this->response($notif);
      return;
    }

    $this->set_response($invalidLogin, REST_Controller::HTTP_UNAUTHORIZED);
    $this->load->model('desa_model');
    $data = $this->desa_model->normalkanData($data);
    if ($this->desa_model->abaikan($data)) return;
    // OpenSID v20.12 ke atas seharusnya ada token yg valid
    if (version_compare($data['version'], '20.12') >= 0) $data['opensid_valid'] = FALSE;
    $this->load->model('akses_model');
    $result1 = $this->desa_model->insert($data);
    $result2 = $this->akses_model->insert($data);
    $this->load->model('notif_model');
    $notif = $this->notif_model->get_semua_notif($data['id']);
    $this->notif_model->non_aktifkan($notif, $data['id']);
    echo json_encode($notif);
  }

}
