<?php

class Migration_Restful_api extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
		$this->load->dbforge();
    $this->load->database();
	}

	public function up()
	{
		if ( ! $this->db->field_exists('token', 'pelanggan') )
		{
			$fields = array(
	      'token varchar(255) DEFAULT NULL'
	    );
	    $this->dbforge->add_column('pelanggan', $fields);
		}

		if ( ! $this->db->field_exists('token', 'users') )
		{
			$fields = array(
	      'token varchar(255) DEFAULT NULL'
	    );
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('id_grup', 'users') )
		{
			$fields = array(
	      'id_grup int(11) DEFAULT NULL'
	    );
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('nama', 'users') )
		{
			$fields = array(
	      'nama varchar(50) DEFAULT NULL'
	    );
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('foto', 'users') )
		{
			$fields = array(
	      'foto varchar(100) DEFAULT NULL'
	    );
	    $this->dbforge->add_column('users', $fields);
		}

		if (!$this->db->table_exists('user_grup') )
		{
			$query = "
			CREATE TABLE `user_grup` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`nama` VARCHAR(20) NOT NULL,
				PRIMARY KEY (`id`)
			)";
			$this->db->query($query);
		}

		if (!$this->db->table_exists('user_grup') )
		{
			$query = "
			INSERT INTO `user_grup` (`id`, `nama`) VALUES
			(1, 'Administrator'),
			(2, 'Operator'),
			(3, 'Redaksi'),
			(4, 'Kontributor'),
			(5, 'Satgas Covid-19')";
			$this->db->query($query);
		}

	}

}
