<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('user_model');
  }

  public function index()
  {
    redirect('login/login');
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
        $_SESSION['nama']         = $user->nama;
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

}
