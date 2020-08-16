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
	}
}