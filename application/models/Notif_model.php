<?php

class Notif_model extends CI_Model {

	// Untuk datatables
	var $table = 'nofifikasi';
	var $column_order = array(null, null, null, 'kode', 'judul', 'jenis', 'server', null); //set column field database for datatable orderable
	var $column_search = array('judul', 'isi'); //set column field database for datatable searchable
	var $order = array('id' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
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

	/*
	 * Get notifikasi by id
	 */
	function get_notifikasi($id)
	{
		return $this->db->get_where('notifikasi',array('id' => $id))->row_array();
	}

	// Setting order_by untuk datatables
	private function set_order_by()
	{
		$order_by = $this->input->post('order');
		if ($order_by)
		{
			$this->db->order_by($this->column_order[$order_by['0']['column']], $order_by['0']['dir']);
		}
		else if ($this->order)
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	private function set_search()
	{
		$search = $this->input->post('search');
		$i = 0;
		foreach ($this->column_search as $item) // loop column
		{
			if ($search['value']) // if datatable send POST for search
			{
				if ($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $search['value']);
				}
				else
				{
					$this->db->or_like($item, $search['value']);
				}
				if (count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
	}

	private function set_filter()
	{
		if ($filter = $this->session->filter)
		{
			$this->db->where('jenis', $filter);
		}
	}

	public function get_all_notifikasi($params = array())
	{
		$this->set_filter();
		$this->set_search();
		$this->set_order_by();

		if (isset($params) && ! empty($params))
		{
			$this->db->limit($params['limit'], $params['offset']);
		}
		$data = $this->db->get('notifikasi')->result_array();

		return $data;
	}

	function get_all_notifikasi_count()
	{
		$this->db->select('*');
		$this->db->from('notifikasi');
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
		$this->db->insert('notifikasi', $params);
		return $this->db->insert_id();
	}

	/*
	 * function to update notifikasi
	 */
	function update_notifikasi($id,$params)
	{
		$this->db->where('id', $id);
		return $this->db->update('notifikasi', $params);
	}

	/*
	 * function to delete notifikasi
	 */
	function delete_notifikasi($id)
	{
		return $this->db->delete('notifikasi', array('id'=>$id));
	}

	public function lock($id=0, $aktif=0)
	{
		$aktif = ($aktif) ? false : true;
		$this->db->where('id', $id)
			->set('aktif', $aktif)
			->update('notifikasi');
	}

	/*
	 * Periksa apakah kode sudah ada.
	 */
	public function cek_kode($kode, $id = null)
	{
		if ($id) $this->db->where('id <>', $id);
		$ada = $this->db
			->where('kode', $kode)
			->get('notifikasi')->num_rows();
		return $ada;
	}

}

?>