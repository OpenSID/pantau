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

  function tgl_in($tgl)
  {
    $tgl = strtotime($tgl);
    $tgl = date("Y-m-d", $tgl);
    return $tgl;
  }

  function date_is_empty($tgl) {
    return (is_null($tgl) || substr($tgl, 0, 10)=='0000-00-00');
  }

  function tgl_out($tgl, $replace_with='-')
  {
    if (date_is_empty($tgl)) return $replace_with;

    $tgl = strtotime($tgl);
    $tgl = date("d-M-Y", $tgl);
    return $tgl;
  }


?>