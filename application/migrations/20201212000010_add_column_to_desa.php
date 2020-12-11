<?php

class Migration_Add_column_to_desa extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
	}

	public function up()
	{
		if ( ! $this->db->field_exists('email_desa', 'desa') )
		{
	    $fields[] = 'email_desa varchar(50) DEFAULT NULL';
 	 		$fields[] = 'telepon varchar(50) DEFAULT NULL';
	    $this->dbforge->add_column('desa', $fields);
		}
	}
}
