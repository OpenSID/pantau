<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan_model_api extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_admin_token_from_id($admin_id)
	{
		$this->db->select('token');
		$this->db->from('users');
		$this->db->where('id', $admin_id);
		return $this->db->get()->row('token');
	}

	public function get_admin_id_from_token($token)
	{
		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('token', $token);
		$this->db->where('id_grup', 1);
		return $this->db->get()->row('id');
	}

	public function get_customer_token_from_id($customer_id)
	{
		$this->db->select('token');
		$this->db->from('pelanggan');
		$this->db->where('id', $customer_id);
		return $this->db->get()->row('token');
	}

	public function get_customer_id_from_token($token)
	{
		$this->db->select('id');
		$this->db->from('pelanggan');
		$this->db->where('token', $token);
		return $this->db->get()->row('id');
	}

  public function api_get_all_customer()
	{
    $data = $this->db->get('pelanggan')->result_array();
    $response['PELANGGAN_PREMIUM']=$data;
    return $response;
  }

	public function api_get_customer($customer_id)
	{
		$data = $this->db
    ->where('id', $customer_id)
    ->get('pelanggan')
    ->result_array();

    $response['PELANGGAN_PREMIUM']=$data;
    return $response;
  }

}
