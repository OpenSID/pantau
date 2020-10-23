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

define("JENIS_PELANGGAN", serialize([
	1 => 'hosting + update',
	2 => 'hosting saja',
	3 => 'premium',
	4 => 'update saja',
	5 => 'hosting + domain',
	6 => 'hosting + domain + update'
]));

define("STATUS_LANGGANAN", serialize([
	1 => 'aktif',
	2 => 'suspended',
	3 => 'tidak aktif',
]));

define("FILTER_LANGGANAN", serialize([
	1 => 'aktif',
	2 => 'suspended',
	3 => 'tidak aktif',
	4 => 'sebentar lagi berakhir',
	5 => 'baru berakhir',
	6 => 'sudah berakhir'
]));

define("PELAKSANA", serialize([
	1 => 'Herry Wanda',
	2 => 'Mohammad Ihsan',
	3 => 'Rudy Purwanto'
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