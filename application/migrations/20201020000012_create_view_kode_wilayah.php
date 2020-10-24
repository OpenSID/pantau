<?php

class Migration_Create_view_kode_wilayah extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
		$this->load->dbforge();
	}

	public function up()
	{
		if ($this->db->table_exists('kode_wilayah')) $this->dbforge->drop_table('kode_wilayah');

		$this->db->query("DROP VIEW IF EXISTS `kode_wilayah`");
		$this->db->query("CREATE VIEW `kode_wilayah` AS
			select d.id, p.region_code as kode_prov, p.region_name as nama_prov, kab.region_code as kode_kab, kab.region_name as nama_kab, kec.region_code as kode_kec, kec.region_name as nama_kec, d.region_code as kode_desa, d.region_name as nama_desa, d.desa_id
			from tbl_regions d
			left join tbl_regions kec on d.parent_code = kec.region_code
			left join tbl_regions kab on kec.parent_code = kab.region_code
			left join tbl_regions p on kab.parent_code = p.region_code
			where char_length(d.region_code) = 13
			");
	}

}
