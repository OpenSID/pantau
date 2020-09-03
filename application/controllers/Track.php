<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
  }

  public function index()
  {
  }

  public function desa()
  {
    /*echo "<pre>";
    print_r($_POST);
    echo "</pre>";*/
    $this->load->model('desa_model');
    $data = $_POST;
    $data = $this->desa_model->normalkanData($data);
    if($this->desa_model->abaikan($data)) return;
    $this->load->model('akses_model');
    $result1 = $this->desa_model->insert($data);
    $result2 = $this->akses_model->insert($data);
    //echo "<pre><br>Result: ".$result1." ".$result2."</pre>";
    $this->load->model('notif_model');
    $notif = $this->notif_model->get_semua_notif($data['id']);
    $this->notif_model->non_aktifkan($notif, $data['id']); // non aktfikan agar tidak dikirim berulang kali
    echo json_encode($notif);
  }

}
