<?php

class Migration_Add_column_to_desa extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
	}

	public function up()
	{
		if ( ! $this->db->field_exists('opensid_valid', 'desa') )
		{
	    $fields[] = 'opensid_valid TINYINT(1) UNSIGNED DEFAULT 1';
	    $this->dbforge->add_column('desa', $fields);
		}
	}
}
