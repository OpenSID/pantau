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
		$semua_notif = $this->db->select('n.*')
			->from('notifikasi n')
			->join('notifikasi_desa nd', 'nd.id_notifikasi = n.id', 'left')
			->where('n.aktif', 1)
			->group_start()
				->where('nd.id_desa IS NULL')->or_where('nd.id_desa', $id_desa)
			->group_end()
			->group_start()
				->where('nd.status IS NULL')->or_where('nd.status <>', 0)
			->group_end()
			->get('notifikasi')->result_array();

		return $semua_notif;
	}

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
					->where('id', $ada->id)
					->update('notifikasi_desa');
			}
			else
			{
				$this->db
					->set('status', 0)
					->set('id_notifikasi', $data['id'])
					->set('id_desa', $id_desa)
					->insert('notifikasi_desa');
			}
		}
	}

}

?>