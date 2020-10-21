<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model_api extends CI_Model {

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

	public function get_user_token_from_id($user_id)
	{
		$this->db->select('token');
		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row('token');
	}

	public function api_get_all_user()
	{
    $data = $this->db->get('users')->result_array();
    $response['USERS']=$data;
    return $response;
  }

	public function api_get_user($user_id)
	{
		$data = $this->db
    ->where('id', $user_id)
    ->get('users')
    ->result_array();

    $response['USERS']=$data;
    return $response;
  }

}
