<?php

class Migration_Add_column_to_notifikasi extends CI_Migration {

	public function __construct()
  	{
	    parent::__construct();
			$this->load->database();
  	}

	public function up()
	{
		if ( ! $this->db->field_exists('tgl_kirim', 'notifikasi_desa') )
		{
	    $fields = array(
	      'tgl_kirim TIMESTAMP NULL DEFAULT NULL'
	    );
	    $this->dbforge->add_column('notifikasi_desa', $fields);
		}
		if ( ! $this->db->field_exists('aksi_ya', 'notifikasi') )
		{
	    $fields = array(
	      'aksi_ya VARCHAR(200) NULL DEFAULT NULL'
	    );
	    $this->dbforge->add_column('notifikasi', $fields);
		}
		if ( ! $this->db->field_exists('aksi_tidak', 'notifikasi') )
		{
	    $fields = array(
	      'aksi_tidak VARCHAR(200) NULL DEFAULT NULL'
	    );
	    $this->dbforge->add_column('notifikasi', $fields);
		}
	}
}