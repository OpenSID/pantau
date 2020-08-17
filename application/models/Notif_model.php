<?php

class Notif_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
  }

  // Ambil notifikasi untuk $id_desa
	public function get_semua_notif($id_desa)
	{
		$this->db
			->select('nd.id')
			->from('notifikasi_desa nd')
			->where('nd.id_notifikasi = n.id')
			->where('nd.id_desa', $id_desa)
			->where('nd.status <>', 0)
			->limit(1);
		$perlu_notifikasi = $this->db->get_compiled_select();

		$this->db
			->select('nd.id')
			->from('notifikasi_desa nd')
			->where('nd.id_notifikasi = n.id')
			->where('nd.id_desa', $id_desa)
			->limit(1);
		$sdh_pernah = $this->db->get_compiled_select();

		$semua_notif = $this->db->select('n.*')
			->from('notifikasi n')
			->where('n.aktif', 1)
			->group_start()
				->where("($perlu_notifikasi) IS NOT NULL")
				->or_where("($sdh_pernah) IS NULL")
			->group_end()
			->get()->result_array();

		return $semua_notif;
	}

	// Catat pengiriman notifikasi
	// Non-aktifkan notififikasi untuk desa $id_desa
	// Asumsi saat ini setiap notifikasi hanya dikirim sekali
	public function non_aktifkan($notif, $id_desa)
	{
		foreach ($notif as $data)
		{
			$ada = $this->db
				->where('id_notifikasi', $data['id'])
				->where('id_desa', $id_desa)
				->get('notifikasi_desa')
				->row();
			if ($ada)
			{
				$this->db
					->set('status', 0)
					->set('tgl_kirim', date("Y-m-d H:i:s"))
					->where('id', $ada->id)
					->update('notifikasi_desa');
			}
			else
			{
				$this->db
					->set('status', 0)
					->set('tgl_kirim', date("Y-m-d H:i:s"))
					->set('id_notifikasi', $data['id'])
					->set('id_desa', $id_desa)
					->insert('notifikasi_desa');
			}
		}
	}

}

?>