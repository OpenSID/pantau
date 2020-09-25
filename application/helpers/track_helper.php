<?php

  define("LOKASI_CONFIG", 'config/');

  function get_client_ip_server() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
      $ipaddress = 'UNKNOWN';

    return $ipaddress;
  }

  function is_local($url) {
    if (preg_match('/localhost|192\.168|:|127\.0\.0\.1|\/10\.|^10\./i', $url))
      return true;
    else return false;
  }

/**
 * KonfigurasiDatabase
 *
 * Mengembalikan path file konfigurasi database desa
 *
 * @access  public
 * @return  string
 */
  function konfigurasi_database()
  {
    $konfigurasi_database = LOKASI_CONFIG . 'database.php';
    return $konfigurasi_database;
  }

  function pre_print_r($data)
  {
    print("<pre>".print_r($data, true)."</pre>");
  }

  /*
  * Termasuk mengubah dari Ind ke En supaya bisa masuk ke MySQL
  */
  function tgl_in($tgl)
  {
    $tgl = str_replace (
      array('Mei', 'Ags', 'Okt', 'Des'),
      array('May', 'Aug', 'Oct', 'Dec'),
        $tgl);
    $tgl = strtotime($tgl);
    $tgl = date("Y-m-d", $tgl);
    return $tgl;
  }

  function date_is_empty($tgl) {
    return (is_null($tgl) || substr($tgl, 0, 10)=='0000-00-00');
  }

  /*
  * Termasuk mengubah dari En ke Ind untuk tampilan dan datetimepicker
  */
  function tgl_out($tgl, $replace_with='-')
  {
    if (date_is_empty($tgl)) return $replace_with;

    $tgl = strtotime($tgl);
    $tgl = date("d-M-Y", $tgl);
    $tgl = str_replace (
      array('May', 'Aug', 'Oct', 'Dec'),
      array('Mei', 'Ags', 'Okt', 'Des'),
        $tgl);
    return $tgl;
  }

  function tgl_format($tgl, $format='d-m-Y')
  {
    if (date_is_empty($tgl)) return;

    $tgl = strtotime($tgl);
    $tgl = date($format, $tgl);
    return $tgl;
  }


?>