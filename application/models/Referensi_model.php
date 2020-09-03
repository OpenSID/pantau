<?php

define("STATUS_AKTIF", serialize([
	'0' => 'Tidak Aktif',
	'1' => 'Aktif'
]));

define("JENIS_NOTIF", serialize([
	'pemberitahuan',
	'pengumuman',
	'peringatan'
]));

define("SERVER_NOTIF", serialize([
	'TrackSID'
]));

class Referensi_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
  }

	public function list_ref($stat)
	{
		$list_ref = unserialize($stat);
		return $list_ref;
	}


}
?>