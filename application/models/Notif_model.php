<?php

class Notif_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
	  }

	public function get_semua_notif()
	{
		$semua_notif = $this->db->select('*')
			->where('aktif',1)
			->get('notifikasi')->result_array();
		return $semua_notif;
	}

	public function non_aktifkan($notif)
	{
		foreach($notif as $data)
		{	
			$this->db->set('aktif', 0);
			$this->db->where('id', $data['id']);
			$this->db->update('notifikasi');
		}		
	}

}

?>