<?php

class Migration_Create_table_pelanggan extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
		$this->load->database();
	}

	public function up()
	{
		if (!$this->db->table_exists('pelanggan') )
		{
			$query = "
			CREATE TABLE `pelanggan` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`nama` VARCHAR(100) NOT NULL,
				`nik` VARCHAR(16) NOT NULL,
				`tgl_lahir` DATE NOT NULL,
				`no_hp` VARCHAR(100) NOT NULL,
				`email` VARCHAR(50) NULL,
				`id_desa` TEXT NOT NULL,
				`tgl_mulai` DATE NOT NULL,
				`tgl_akhir` DATE NOT NULL,
				`domain` VARCHAR(100),
				`jenis_langganan` TINYINT(4) NOT NULL DEFAULT 1,
				`status_langganan` TINYINT(4) NOT NULL DEFAULT 1,
				`pelaksana` VARCHAR(100) NULL,
				PRIMARY KEY (`id`)
			)";
			$this->db->query($query);

			$query = "
			CREATE TABLE `iuran_pelanggan` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`id_pelanggan` INT(11) NOT NULL,
				`jumlah_iuran` INT(11) NOT NULL,
				`tgl_iuran` TINYINT(2) NOT NULL DEFAULT 0,
				`resi` VARCHAR(200),
				PRIMARY KEY (`id`)
			)";
			$this->db->query($query);
			$this->dbforge->add_column('iuran_pelanggan', array(
	    	'CONSTRAINT iuran_pelanggan_fk FOREIGN KEY (id_pelanggan) REFERENCES pelanggan (id) ON DELETE CASCADE ON UPDATE CASCADE'
			));
		}

	}
}