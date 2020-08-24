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

	/*
     * Get notifikasi by id
     */
    function get_notifikasi($id)
    {
        return $this->db->get_where('notifikasi',array('id'=>$id))->row_array();
    }
    
    function get_all_notifikasi($params = array(),$filter='',$search='')
    {
        $this->db->order_by('id', 'asc');
        if(isset($params) && !empty($params))
        {
            $this->db->limit($params['limit'], $params['offset']);
        }
        if($search != ''){
            $this->db->like('judul', $search);
        }
        if($filter != '')
        {
            $this->db->where('jenis', $filter);
        }
        return $this->db->get('notifikasi')->result_array();
    }

    function get_all_notifikasi_count($filter='',$search='')
    {
        $this->db->select('*');
        $this->db->from('notifikasi');
        if($search != ''){
            $this->db->like('judul', $search);
        }
        if($filter != '')
        {
            $this->db->where('jenis', $filter);
        }
        $count = $this->db->count_all_results();
        return $count;
    }

    function get_all_jenis()
    {
        return $this->db->select('jenis')->distinct()->get('notifikasi')->result_array();
    }
        
    /*
     * function to add new notifikasi
     */
    function add_notifikasi($params)
    {
        $this->db->insert('notifikasi',$params);
        return $this->db->insert_id();
    }
    
    /*
     * function to update notifikasi
     */
    function update_notifikasi($id,$params)
    {
        $this->db->where('id',$id);
        return $this->db->update('notifikasi',$params);
    }
    
    /*
     * function to delete notifikasi
     */
    function delete_notifikasi($id)
    {
        return $this->db->delete('notifikasi',array('id'=>$id));
    }


}

?>