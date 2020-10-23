<?php

class Migration_Update_kode_wilayah extends CI_Migration {

	public function __construct()
	{
    parent::__construct();
		$this->load->dbforge();
	}

	public function up()
	{
		$this->baca_kode_wilayah(); // Data tersimpan di tabel kode_wilayah
		$limit = 2000;
		$jml_wilayah = $this->db->get('kode_wilayah')->num_rows();
		// Karena tabel besar update secara blok
		for ($offset=0; $offset <= $jml_wilayah; $offset+=$limit)
		{
			$list_wilayah = $this->db->limit($limit, $offset)
				->get('kode_wilayah')->result_array();
			// Update tabel tbl_regions
			foreach ($list_wilayah as $wilayah)
			{
				$desa = $this->db->where('region_code', $this->normalkan_kode($wilayah['kode_desa']))
					->get('tbl_regions');
				if ($desa->num_rows())
				{
					// Update desa yg sudah ada
					$desa = $desa->row_array();
					$this->db
						->where('id', $desa['id'])
						->set('region_name', $wilayah['nama_desa'])
						->update('tbl_regions');
					continue;
				}
				// Tambah desa baru; kalau perlu juga tambah kecamatan, kabupaten dan provinsi
				$desa = [
					'region_code' => $this->normalkan_kode($wilayah['kode_desa']),
					'region_name' => $wilayah['nama_desa'],
					'parent_code' => $this->normalkan_kode($wilayah['kode_kec'])
				];
				$this->update_wilayah($desa);
				$kec = [
					'region_code' => $this->normalkan_kode($wilayah['kode_kec']),
					'region_name' => $wilayah['nama_kec'],
					'parent_code' => $this->normalkan_kode($wilayah['kode_kab'])
				];
				$this->update_wilayah($kec);
				$kab = [
					'region_code' => $this->normalkan_kode($wilayah['kode_kab']),
					'region_name' => $wilayah['nama_kab'],
					'parent_code' => $wilayah['kode_prov']
				];
				$this->update_wilayah($kab);
				$prov = [
					'region_code' => $wilayah['kode_prov'],
					'region_name' => $wilayah['nama_prov'],
					'parent_code' => 0
				];
				$this->update_wilayah($prov);
			}
		}
		$list_wilayah = null;
		// if ($this->db->table_exists('kode_wilayah')) $this->dbforge->drop_table('kode_wilayah');
    $this->db->close();
    $this->load->database();
	}

	private function normalkan_kode($wilayah)
	{
		$str = '';
		switch (strlen($wilayah))
		{
			case 10:
				$str = '.' . substr($wilayah, -4) . $str;
				$wilayah = substr($wilayah, 0, 6);
			case 6:
				$str = '.' . substr($wilayah, -2) . $str;
				$wilayah = substr($wilayah, 0, 4);
			case 4:
				$str = '.' . substr($wilayah, -2) . $str;
				$wilayah = substr($wilayah, 0, 2);
			case 2:
				$str = $wilayah . $str;
				break;
		}
		return $str;
	}

	private function update_wilayah($wilayah)
	{
		log_message('error', 'Wilayah baru: ' . print_r($wilayah, true));
		$sql = $this->db->insert_string('tbl_regions', $wilayah) . " ON DUPLICATE KEY UPDATE region_name = VALUES(region_name)";
		$this->db->query($sql);
	}

	private function baca_kode_wilayah()
	{
		$sql = file_get_contents(FCPATH."application/migrations/kode_wilayah_permendagri_72_2019.sql");
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
