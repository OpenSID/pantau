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

			$query = "
			CREATE TABLE `notifikasi_desa` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`id_desa` INT(11) NOT NULL,
				`id_notifikasi` INT(11) NOT NULL,
				`status` TINYINT(2) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				UNIQUE KEY (id_desa, id_notifikasi)
			)";
			$this->db->query($query);
			$this->dbforge->add_column('notifikasi_desa', array(
	    	'CONSTRAINT notifikasi_desa_fk FOREIGN KEY (id_notifikasi) REFERENCES notifikasi (id) ON DELETE CASCADE ON UPDATE CASCADE'
			));
			$this->dbforge->add_column('notifikasi_desa', array(
	    	'CONSTRAINT desa_notifikasi_fk FOREIGN KEY (id_desa) REFERENCES desa (id) ON DELETE CASCADE ON UPDATE CASCADE'
			));
		}

	}
}