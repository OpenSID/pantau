<?php

class Migration_Create_table_notifikasi extends CI_Migration {

	public function __construct()
  	{
	    parent::__construct();
		$this->load->database();
  	}

	public function up()
	{
		if (!$this->db->table_exists('notifikasi') )
		{
			$query = "
			CREATE TABLE `notifikasi` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`kode` VARCHAR(100) NOT NULL,
				`judul` VARCHAR(100) NOT NULL,
				`jenis` VARCHAR(50) NOT NULL,
				`isi` TEXT NOT NULL,
				`server` VARCHAR(20) NOT NULL DEFAULT 'TrackSID',
				`frekuensi` SMALLINT(6) NOT NULL DEFAULT 30,
				`aktif` TINYINT(4) NOT NULL DEFAULT 1,
				PRIMARY KEY (`id`),
				UNIQUE KEY (kode)
			)";
			$this->db->query($query);
		}
		
	}
}