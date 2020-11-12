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
    $dev_token = $this->config->item('dev_token');
    if ($token === $dev_token) {
      $decodedToken = AUTHORIZATION::validateTimestamp($token);
      if ($decodedToken != false) {
        $this->load->model('desa_model');
        $data = $_POST;
        $data = $this->desa_model->normalkanData($data);
        if($this->desa_model->abaikan($data)) return;
        $this->load->model('akses_model');
        $result1 = $this->desa_model->insert($data);
        $result2 = $this->akses_model->insert($data);
        $this->load->model('notif_model');
        $notif = $this->notif_model->get_semua_notif($data['id']);
        $this->notif_model->non_aktifkan($notif, $data['id']); // non aktfikan agar tidak dikirim berulang kali
        echo json_encode($notif);
      }
    }
  }

}
