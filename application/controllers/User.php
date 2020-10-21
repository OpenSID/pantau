<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * User class.
 *
 * @extends CI_Controller
 */
class User extends CI_Controller {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();
		$this->load->helper(array('url'));
		$this->load->model('user_model');
		$this->load->model('referensi_model');
		//if ( ! admin_logged_in()) redirect('login'); //enable development
	}


	public function clear()
	{
		unset($_SESSION['cari']);
		unset($_SESSION['filter']);
		redirect('user');
	}

	public function index($p = 1, $o = 0)
	{
		$data['p'] = $p;
		$data['o'] = $o;

		if (isset($_SESSION['cari']))
			$data['cari'] = $_SESSION['cari'];
		else $data['cari'] = '';

		if (isset($_SESSION['filter']))
			$data['filter'] = $_SESSION['filter'];
		else $data['filter'] = '';

		if (isset($_POST['per_page']))
			$_SESSION['per_page'] = $_POST['per_page'];
		$data['per_page'] = $_SESSION['per_page'];

		$data['paging'] = $this->user_model->paging($p, $o);
		$data['main'] = $this->user_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
		$data['keyword'] = $this->user_model->autocomplete();

		$data['user_group'] = $this->referensi_model->list_data("user_grup");

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/nav');
		$this->load->view('user/table', $data);
		$this->load->view('dashboard/footer');
	}

	public function form($p = 1, $o = 0, $id = '')
	{
		$data['p'] = $p;
		$data['o'] = $o;

		if ($id)
		{
			$data['user'] = $this->user_model->get_user($id);
			$data['form_action'] = site_url("user/update/$p/$o/$id");
		}
		else
		{
			$data['user'] = NULL;
			$data['form_action'] = site_url("user/insert");
		}

		$data['user_group'] = $this->referensi_model->list_data("user_grup");

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/nav');
		$this->load->view('user/form', $data);
		$this->load->view('dashboard/footer');
	}

	public function search()
	{
		$cari = $this->input->post('cari');
		if ($cari != '')
			$_SESSION['cari'] = $cari;
		else unset($_SESSION['cari']);
		redirect('user');
	}

	public function filter()
	{
		$filter = $this->input->post('filter');
		if ($filter != 0)
			$_SESSION['filter'] = $filter;
		else unset($_SESSION['filter']);
		redirect('user');
	}

	public function insert()
	{
		$this->set_form_validation();

		if ($this->form_validation->run() !== true)
		{
			$this->session->success = -1;
			$this->session->error_msg = trim(validation_errors());
			redirect("user/form/$p/$o");
		}
		else
		{
			$this->user_model->insert();
			redirect('user');
		}
	}

	private function set_form_validation()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('password', 'Kata Sandi Baru', 'required|callback_syarat_sandi');
		$this->form_validation->set_message('syarat_sandi','Harus 6 sampai 20 karakter dan sekurangnya berisi satu angka dan satu huruf besar dan satu huruf kecil');
	}

	// Kata sandi harus 6 sampai 20 karakter dan sekurangnya berisi satu angka dan satu huruf besar dan satu huruf kecil
	public function syarat_sandi($str)
	{
		// radiisi berarti tidak sandi tidak diubah
		if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/', $str) or $str == 'radiisi')
			return TRUE;
		else
			return FALSE;
	}

	public function update($p = 1, $o = 0, $id = '')
	{
		$this->set_form_validation();

		if ($this->form_validation->run() !== true)
		{
			$this->session->success = -1;
			$this->session->error_msg = trim(validation_errors());
			redirect("user/form/$p/$o/$id");
		}
		else
		{
			$this->user_model->update($id);
			redirect("user/index/$p/$o");
		}
	}

	public function delete($p = 1, $o = 0, $id = '')
	{
		$this->redirect_hak_akses('h', "user/index/$p/$o");
		$this->user_model->delete($id);
		redirect("user/index/$p/$o");
	}

	public function delete_all($p = 1, $o = 0)
	{
		$this->redirect_hak_akses('h', "user/index/$p/$o");
		$this->user_model->delete_all();
		redirect("user/index/$p/$o");
	}

	public function user_lock($id = '')
	{
		$this->user_model->user_lock($id, 0);
		redirect("user/index/$p/$o");
	}

	public function user_unlock($id = '')
	{
		$this->user_model->user_lock($id, 1);
		redirect("user/index/$p/$o");
	}

	/**
	 * register function.
	 *
	 * @access public
	 * @return void
	 */
	public function register() {

		// create the data object
		$data = new stdClass();

		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');

		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

		if ($this->form_validation->run() === false) {

			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('user/register/register', $data);
			$this->load->view('footer');

		} else {

			// set variables from the form
			$username = $this->input->post('username');
			$email    = $this->input->post('email');
			$password = $this->input->post('password');

			if ($this->user_model->create_user($username, $email, $password)) {

				// user creation ok
				$this->load->view('header');
				$this->load->view('user/register/register_success', $data);
				$this->load->view('footer');

			} else {

				// user creation failed, this should never happen
				$data->error = 'There was a problem creating your new account. Please try again.';

				// send error to the view
				$this->load->view('header');
				$this->load->view('user/register/register', $data);
				$this->load->view('footer');

			}

		}

	}

	/**
	 * login function.
	 *
	 * @access public
	 * @return void
	 */
	public function login() {

		// create the data object
		$data = new stdClass();

		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');

		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == false) {

	    $header = new stdClass();
	    $header->title = 'Login';
		// validation not ok, send validation errors to the view
	    $this->load->view('dashboard/header', $header);
	    $this->load->view('dashboard/nav');
			$this->load->view('user/login/login');
			$this->load->view('dashboard/footer');

		} else {

			// set variables from the form
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			if ($this->user_model->resolve_user_login($username, $password)) {

				$user_id = $this->user_model->get_user_id_from_username($username);
				$user    = $this->user_model->get_user($user_id);

				// set session user datas
				$_SESSION['user_id']      = (int)$user->id;
				$_SESSION['username']     = (string)$user->username;
				$_SESSION['logged_in']    = (bool)true;
				$_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
				$_SESSION['is_admin']     = (bool)$user->is_admin;

				// user login ok
				redirect('/');
				// $this->load->view('header');
				// $this->load->view('user/login/login_success', $data);
				// $this->load->view('footer');

			} else {

				// login failed
				$data->error = 'Wrong username or password.';

				// send error to the view
				$this->load->view('header');
				$this->load->view('user/login/login', $data);
				$this->load->view('footer');

			}

		}

	}

	/**
	 * logout function.
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {

		// create the data object
		$data = new stdClass();

		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

			// remove session datas
			foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			}

			// user logout ok
			redirect('/');
			// $this->load->view('header');
			// $this->load->view('user/logout/logout_success', $data);
			// $this->load->view('footer');

		} else {

			// there user was not logged in, we cannot logged him out,
			// redirect him to site root
			redirect('/');

		}

	}

	public function generate_token()
	{
		$id = $this->input->get('id');
		$tokenData = array();
		$tokenData['id'] = $id;
		$tokenData['timestamp'] = now();
		$data = AUTHORIZATION::generateToken($tokenData);
		echo json_encode($data);
	}

}
