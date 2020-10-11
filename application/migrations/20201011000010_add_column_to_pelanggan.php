<?php

class Migration_Add_column_to_pelanggan extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
	}

	public function up()
	{
		if ( ! $this->db->field_exists('bukti', 'pelanggan') )
		{
	    $fields[] = 'bukti varchar(100) DEFAULT NULL';
	    $this->dbforge->add_column('pelanggan', $fields);
		}
	}
}