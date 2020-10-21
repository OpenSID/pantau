<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model class.
 *
 * @extends CI_Model
 */
class User_model extends CI_Model {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();
		$this->load->database();

	}

	/**
	 * create_user function.
	 *
	 * @access public
	 * @param mixed $username
	 * @param mixed $email
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	public function create_user($username, $email, $password) {

		$data = array(
			'username'   => $username,
			'email'      => $email,
			'password'   => $this->hash_password($password),
			'created_at' => date('Y-m-j H:i:s'),
		);

		return $this->db->insert('users', $data);

	}

	/**
	 * resolve_user_login function.
	 *
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	public function resolve_user_login($username, $password) {

		$this->db->select('password');
		$this->db->from('users');
		$this->db->where('username', $username);
		$hash = $this->db->get()->row('password');

		return $this->verify_password_hash($password, $hash);

	}

	/**
	 * get_user_id_from_username function.
	 *
	 * @access public
	 * @param mixed $username
	 * @return int the user id
	 */
	public function get_user_id_from_username($username) {

		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('username', $username);

		return $this->db->get()->row('id');

	}

	/**
	 * get_user function.
	 *
	 * @access public
	 * @param mixed $user_id
	 * @return object the user object
	 */
	public function get_user($user_id) {

		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();

	}

	/**
	 * hash_password function.
	 *
	 * @access private
	 * @param mixed $password
	 * @return string|bool could be a string on success, or bool false on failure
	 */
	private function hash_password($password) {

		return password_hash($password, PASSWORD_BCRYPT);

	}

	/**
	 * verify_password_hash function.
	 *
	 * @access private
	 * @param mixed $password
	 * @param mixed $hash
	 * @return bool
	 */
	private function verify_password_hash($password, $hash) {

		return password_verify($password, $hash);

	}

	public function autocomplete()
	{
		$sql = "SELECT username FROM users UNION SELECT nama FROM users";
		$query = $this->db->query($sql);
		$data = $query->result_array();

		$out = '';
		for ($i=0; $i < count($data); $i++)
		{
			$out .= ",'".$data[$i]['username']."'";
		}
		return '['.strtolower(substr($out, 1)).']';
	}

	private function search_sql()
	{
		if (isset($_SESSION['cari']))
		{
			$keyword = $_SESSION['cari'];
			$keyword = '%'.$this->db->escape_like_str($keyword).'%';
			$search_sql = " AND (u.username LIKE '$keyword' OR u.nama LIKE '$keyword')";
			return $search_sql;
		}
	}

	private function filter_sql()
	{
		if (isset($_SESSION['filter']))
		{
			$filter = $_SESSION['filter'];
			$filter_sql = " AND u.id_grup = $filter";
			return $filter_sql;
		}
	}

	public function paging($page = 1, $o = 0)
	{
		$sql = "SELECT COUNT(*) AS jml " . $this->list_data_sql();
		$query = $this->db->query($sql);
		$row = $query->row_array();
		$jml_data = $row['jml'];

		$this->load->library('paging');
		$cfg['page'] = $page;
		$cfg['per_page'] = $_SESSION['per_page'];
		$cfg['num_rows'] = $jml_data;
		$this->paging->init($cfg);

		return $this->paging;
	}

	private function list_data_sql()
	{
		$sql = " FROM users u, user_grup g WHERE u.id_grup = g.id ";
		$sql .= $this->search_sql();
		$sql .= $this->filter_sql();
		return $sql;
	}

	public function list_data($order = 0, $offset = 0, $limit = 500)
	{
		// Ordering sql
		switch($order)
		{
			case 1 :
				$order_sql = ' ORDER BY u.username';
				break;
			case 2:
				$order_sql = ' ORDER BY u.username DESC';
				break;
			case 3:
				$order_sql = ' ORDER BY u.nama';
				break;
			case 4:
				$order_sql = ' ORDER BY u.nama DESC';
				break;
			case 5:
				$order_sql = ' ORDER BY g.nama';
				break;
			case 6:
				$order_sql = ' ORDER BY g.nama DESC';
				break;
			default:
				$order_sql = ' ORDER BY u.username';
		}
		// Paging sql
		$paging_sql = ' LIMIT '.$offset.','.$limit;
		// Query utama
		$sql = "SELECT u.*, g.nama as grup " . $this->list_data_sql();
		$sql .= $order_sql;
		$sql .= $paging_sql;

		$query = $this->db->query($sql);
		$data = $query->result_array();

		// Formating output
		$j = $offset;
		for ($i=0; $i < count($data); $i++)
		{
			$data[$i]['no'] = $j + 1;
			$j++;
		}
		return $data;
	}

	/**
	 * Insert user baru ke database
	 * @return  void
	 */
	public function insert()
	{
		$_SESSION['error_msg'] = NULL;
		$_SESSION['success'] = 1;

		$data = $this->sterilkan_input($this->input->post());

		$sql = "SELECT username FROM users WHERE username = ?";
		$dbQuery = $this->db->query($sql, array($data['username']));
		$userSudahTerdaftar = $dbQuery->row();
		$userSudahTerdaftar = is_object($userSudahTerdaftar) ? $userSudahTerdaftar->username : FALSE;

		if ($userSudahTerdaftar !== FALSE)
		{
			$_SESSION['success'] = -1;
			$_SESSION['error_msg'] = ' -> Username ini sudah ada. silahkan pilih username lain';
			redirect('user');
		}

		$pwHash = $this->generatePasswordHash($data['password']);
		$data['password'] = $pwHash;
		$data['session'] = md5(now());

		$data['foto'] = $this->urusFoto();
		$data['nama'] = strip_tags($data['nama']);

		if (!$this->db->insert('users', $data))
		{
			$_SESSION['success'] = -1;
			$_SESSION['error_msg'] = ' -> Gagal memperbarui data di database';
		}
	}

	private function sterilkan_input($post)
	{
		$data = [];
		$data['password'] = $post['password'];
		if (isset($post['username'])) $data['username'] = alfanumerik($post['username']);
		if (isset($post['nama'])) $data['nama'] = alfanumerik_spasi($post['nama']);
		if (isset($post['email'])) $data['email'] = htmlentities($post['email']);
		if (isset($post['id_grup'])) $data['id_grup'] = $post['id_grup'];
		if (isset($post['foto'])) $data['foto'] = $post['foto'];
		if (isset($post['token'])) $data['token'] = $post['token'];
		return $data;
	}

	/**
	 * Update data user
	 * @param   integer  $idUser  Id user di database
	 * @return  void
	 */
	public function update($idUser)
	{
		$this->session->error_msg = NULL;
		$this->session->success = 1;

		$data = $this->sterilkan_input($this->input->post());

		if (empty($idUser))
		{
			$this->session->error_msg = ' -> Pengguna tidak ditemukan datanya.';
			$this->session->success = -1;
			redirect('user');
		}

		if (empty($data['username']) || empty($data['password'])
		|| empty($data['nama']) || !in_array(intval($data['id_grup']), range(1, 4)))
		{
			$this->session->error_msg = ' -> Nama, Username dan Kata Sandi harus diisi';
			$this->session->success = -1;
			redirect('user');
		}

		// radiisi menandakan password tidak diubah
		if ($data['password'] == 'radiisi') unset($data['password']);
		// Untuk demo jangan ubah username atau password
		if ($idUser == 1 && $this->setting->demo_mode)
		{
			unset($data['username'], $data['password']);
		}
		if ($data['password'])
		{
			$pwHash = $this->generatePasswordHash($data['password']);
			$data['password'] = $pwHash;
		}

		$data['foto'] = $this->urusFoto($idUser);
		if (!$this->db->where('id', $idUser)->update('users', $data))
		{
			$this->session->success = -1;
			$this->session->error_msg = ' -> Gagal memperbarui data di database';
		}
	}

	public function delete($idUser = '', $semua=false)
	{
		// Jangan hapus admin
		if ($idUser == 1) return;

		if (!$semua)
		{
			$this->session->success = 1;
			$this->session->error_msg = '';
		}

    $foto = $this->db->get_where('users',array('id' => $idUser))->row()->foto;
		$hasil = $this->db->where('id', $idUser)->delete('users');
    // Cek apakah pengguna berhasil dihapus
		if ($hasil)
		{
	    // Cek apakah pengguna memiliki foto atau tidak
	    if($foto != 'kuser.png')
	    {
        // Ambil nama foto
        $foto = basename(AmbilFoto($foto));
        // Cek penghapusan foto pengguna
        if (!unlink(LOKASI_USER_PICT.$foto))
        {
          $_SESSION['error_msg'] = 'Gagal menghapus foto pengguna';
          $_SESSION['success'] = -1;
        }
	    }
		}
		else
		{
      $_SESSION['error_msg'] = 'Gagal menghapus pengguna';
			$_SESSION['success'] = -1;
		}
	}

	public function delete_all()
	{
		$this->session->success = 1;
		$this->session->error_msg = '';

		$id_cb = $_POST['id_cb'];
		foreach ($id_cb as $id)
		{
			$this->delete($id, $semua=true);
		}
	}

	public function user_lock($id = '', $val = 0)
	{
		$sql = "UPDATE user SET active = ? WHERE id = ?";
		$hasil = $this->db->query($sql, array($val, $id));
		$_SESSION['success'] = ($hasil === TRUE ? 1 : -1);
	}

	public function get_user_lama($id = 0)
	{
		$sql = "SELECT * FROM users WHERE id = ?";
		$query = $this->db->query($sql, $id);
		$data = $query->row_array();
		// Formating output
		$data['password'] = 'radiisi';
		return $data;
	}

	private function urusFoto($idUser='')
	{
		if ($idUser)
		{
			$berkasLama = $this->db->select('foto')->where('id', $idUser)->get('users')->row();
			$berkasLama = is_object($berkasLama) ? $berkasLama->foto : 'kuser.png';
			$lokasiBerkasLama = $this->uploadConfig['upload_path'].'kecil_'.$berkasLama;
			$lokasiBerkasLama = str_replace('/', DIRECTORY_SEPARATOR, FCPATH.$lokasiBerkasLama);
		}
		else
		{
			$berkasLama = 'kuser.png';
		}

		$nama_foto = $this->uploadFoto('gif|jpg|jpeg|png', LOKASI_USER_PICT, 'foto', 'users');

		if (!empty($nama_foto))
		{
			// Ada foto yang berhasil diunggah --> simpan ukuran 100 x 100
			$tipe_file = TipeFile($_FILES['foto']);
			$dimensi = array("width"=>100, "height"=>100);
			resizeImage(LOKASI_USER_PICT.$nama_foto, $tipe_file, $dimensi);
			// Nama berkas diberi prefix 'kecil'
			$nama_kecil = 'kecil_'.$nama_foto;
			$fileRenamed = rename(
				LOKASI_USER_PICT.$nama_foto,
				LOKASI_USER_PICT.$nama_kecil
			);
			if ($fileRenamed) $nama_foto = $nama_kecil;
			// Hapus berkas lama
			if ($berkasLama and $berkasLama !== 'kecil_kuser.png')
			{
				unlink($lokasiBerkasLama);
				if (file_exists($lokasiBerkasLama)) $_SESSION['success'] = -1;
			}
		}

		return is_null($nama_foto) ? $berkasLama : str_replace('kecil_', '', $nama_foto);
	}

	/***
		* @return
			- success: nama berkas yang diunggah
			- fail: NULL
	*/
	private function uploadFoto($allowed_types, $upload_path, $lokasi, $redirect)
	{
		// Adakah berkas yang disertakan?
		$adaBerkas = !empty($_FILES[$lokasi]['name']);
		if ($adaBerkas !== TRUE)
		{
			return NULL;
		}
		// Tes tidak berisi script PHP
		if (isPHP($_FILES[$lokasi]['tmp_name'], $_FILES[$lokasi]['name']))
		{
			$_SESSION['error_msg'] .= " -> Jenis file ini tidak diperbolehkan ";
			$_SESSION['success'] = -1;
			redirect($redirect);
		}

		if ((strlen($_FILES[$lokasi]['name']) + 20 ) >= 100)
		{
			$_SESSION['success'] = -1;
			$_SESSION['error_msg'] = ' -> Nama berkas foto terlalu panjang, maksimal 80 karakter';
			redirect($redirect);
		}

		$uploadData = NULL;
		// Inisialisasi library 'upload'
		$this->upload->initialize($this->uploadConfig);
		// Upload sukses
		if ($this->upload->do_upload($lokasi))
		{
			$uploadData = $this->upload->data();
			// Buat nama file unik agar url file susah ditebak dari browser
			$namaClean = preg_replace('/[^A-Za-z0-9.]/', '_', $uploadData['file_name']);
      $namaFileUnik = tambahSuffixUniqueKeNamaFile($namaClean); // suffix unik ke nama file
			// Ganti nama file asli dengan nama unik untuk mencegah akses langsung dari browser
			$fileRenamed = rename(
				$this->uploadConfig['upload_path'].$uploadData['file_name'],
				$this->uploadConfig['upload_path'].$namaFileUnik
			);
			// Ganti nama di array upload jika file berhasil di-rename --
			// jika rename gagal, fallback ke nama asli
			$uploadData['file_name'] = $fileRenamed ? $namaFileUnik : $uploadData['file_name'];
		}
		// Upload gagal
		else
		{
			$_SESSION['success'] = -1;
			$_SESSION['error_msg'] = $this->upload->display_errors(NULL, NULL);
		}
		return (!empty($uploadData)) ? $uploadData['file_name'] : NULL;
	}

}
