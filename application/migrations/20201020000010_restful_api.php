<?php

class Migration_Restful_api extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
	}

	public function up()
	{
		if ( ! $this->db->field_exists('token', 'pelanggan') )
		{
	    $fields[] = 'token varchar(255) DEFAULT NULL';
	    $this->dbforge->add_column('pelanggan', $fields);
		}

		if ( ! $this->db->field_exists('token', 'users') )
		{
	    $fields[] = 'token varchar(255) DEFAULT NULL';
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('id_grup', 'users') )
		{
	    $fields[] = 'id_grup int(11) DEFAULT NULL';
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('nama', 'users') )
		{
	    $fields[] = 'nama varchar(50) DEFAULT NULL';
	    $this->dbforge->add_column('users', $fields);
		}

		if ( ! $this->db->field_exists('foto', 'users') )
		{
	    $fields[] = 'foto varchar(100) DEFAULT NULL';
	    $this->dbforge->add_column('users', $fields);
		}

		if (!$this->db->table_exists('user_grup') )
		{
			$query = "
			CREATE TABLE `pelanggan` (
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

		$sql = file_get_contents(FCPATH."application/migrations/kode_wilayah.sql");
    $sqls = explode(';', $sql);
    array_pop($sqls);
    foreach($sqls as $statement){
      $statement = $statement . ";";
      $this->db->query($statement);
    }
    $this->db->close();
    $this->load->database();

	}
}
