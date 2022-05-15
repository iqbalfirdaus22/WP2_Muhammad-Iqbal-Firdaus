<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		date_default_timezone_set('Asia/Jakarta');

		$this->load->model('m_data');

		// cek session yang login, 
		// jika session status tidak sama dengan session telah_login, berarti pengguna belum login
		// maka halaman akan di alihkan kembali ke halaman login.
		if($this->session->userdata('status')!="telah_login"){
			redirect(base_url().'login?alert=belum_login');
		}
	}

	public function index()
	{
		// hitung jumlah artikel
		$data['jumlah_artikel'] = $this->m_data->get_data('artikel')->num_rows();
		// hitung jumlah kategori
		$data['jumlah_kategori'] = $this->m_data->get_data('kategori')->num_rows();
		// hitung jumlah pengguna
		$data['jumlah_pengguna'] = $this->m_data->get_data('pengguna')->num_rows();
		// hitung jumlah halaman
		$data['jumlah_halaman'] = $this->m_data->get_data('halaman')->num_rows();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_index',$data);
		$this->load->view('dashboard/v_footer');
	}

public function pages()
	{
	$data['halaman'] =
	$this->m_data->get_data('halaman')->result();
	$this->load->view('dashboard/v_header');
	$this->load->view('dashboard/v_pages',
	$data);
	$this->load->view('dashboard/v_footer');
	}

	public function pages_tambah()
{
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_pages_tambah')
;
$this->load->view('dashboard/v_footer');
}

public function pages_aksi()
{
// Wajib isi judul,konten
$this->form_validation->set_rules('judul',
'Judul',
'required|is_unique[halaman.halaman_judul]');
$this->form_validation->set_rules('konten',
'Konten', 'required');
if ($this->form_validation->run() !=
false) {
$judul =
$this->input->post('judul');
$slug =
strtolower(url_title($judul));
$konten =
$this->input->post('konten');
$data = array(
'halaman_judul' => $judul,
'halaman_slug' => $slug,
'halaman_konten' => $konten
);
$this->m_data->insert_data($data,'halaman');
// alihkan kembali ke method pages
redirect(base_url(),'dashboard/pages');
} 
else {
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_pages_tambah');
$this->load->view('dashboard/v_footer');
}
}
public function pages_edit($id)
{
$where = array(
'halaman_id' => $id
);
$data['halaman'] =
$this->m_data->edit_data($where,
'halaman')->result();
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_pages_edit',
$data);
$this->load->view('dashboard/v_footer');
}
public function pages_update()
{
// Wajib isi judul,konten
$this->form_validation->set_rules('judul',
'Judul', 'required');
$this->form_validation->set_rules('konten',
'Konten', 'required');
if ($this->form_validation->run() !=
false) {
$id = $this->input->post('id');
$judul =
$this->input->post('judul');
$slug =
strtolower(url_title($judul));
$konten =
$this->input->post('konten');
$where = array(
'halaman_id' => $id
);
$data = array(
'halaman_judul' => $judul,
'halaman_slug' => $slug,
'halaman_konten' => $konten
);
$this->m_data->update_data($where, $data,
'halaman');
redirect(base_url() .
'dashboard/pages');
} else {
$id = $this->input->post('id');
$where = array(
'halaman_id' => $id
);
$data['halaman'] =
$this->m_data->edit_data($where,
'halaman')->result();
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_pages_edit',
$data);
$this->load->view('dashboard/v_footer');
}
}

public function pages_hapus($id)
{
$where = array(
'halaman_id' => $id
);
$this->m_data->delete_data($where,
'halaman');
redirect(base_url() .
'dashboard/pages');
}


public function profil()
{
// id pengguna yang sedang login
$id_pengguna =
$this->session->userdata('id');
$where = array(
'pengguna_id' => $id_pengguna
);
$data['profil'] =
$this->m_data->edit_data($where,
'pengguna')->result();
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_profil',
$data);
$this->load->view('dashboard/v_footer');
}


public function profil_update()
{
// Wajib isi nama dan email
$this->form_validation->set_rules('nama',
'Nama', 'required');
$this->form_validation->set_rules('email',
'Email', 'required');
if ($this->form_validation->run() !=
false) {
$id =
$this->session->userdata('id');
$nama = $this->input->post('nama');
$email =
$this->input->post('email');
$where = array(
'pengguna_id' => $id
);
$data = array(
'pengguna_nama' => $nama,
'pengguna_email' => $email
);
$this->m_data->update_data($where,
$data, 'pengguna');
redirect(base_url() .
'dashboard/profil/?alert=sukses');
} else {
// id pengguna yang sedang login
$id_pengguna =
$this->session->userdata('id');
$where = array(
'pengguna_id' => $id_pengguna
);
$data['profil'] =
$this->m_data->edit_data($where,
'pengguna')->result();
$this->load->view('dashboard/v_header');
$this->load->view('dashboard/v_profil', $data);
$this->load->view('dashboard/v_footer');
}
}
	public function keluar()
	{
		$this->session->sess_destroy();
		redirect('login?alert=logout');
	}

	public function ganti_password()
	{
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_ganti_password');
		$this->load->view('dashboard/v_footer');
	}

	public function ganti_password_aksi()
	{

		// form validasi
		$this->form_validation->set_rules('password_lama','Password Lama','required');
		$this->form_validation->set_rules('password_baru','Password Baru','required|min_length[8]');
		$this->form_validation->set_rules('konfirmasi_password','Konfirmasi Password Baru','required|matches[password_baru]');

		// cek validasi
		if($this->form_validation->run() != false){

			// menangkap data dari form
			$password_lama = $this->input->post('password_lama');
			$password_baru = $this->input->post('password_baru');
			$konfirmasi_password = $this->input->post('konfirmasi_password');

			// cek kesesuaian password lama dengan id pengguna yang sedang login dan password lama
			$where = array(
				'pengguna_id' => $this->session->userdata('id'),
				'pengguna_password' => md5($password_lama)
			);
			$cek = $this->m_data->cek_login('pengguna', $where)->num_rows();

			// cek kesesuaikan password lama
			if($cek > 0){

				// update data password pengguna
				$w = array(
					'pengguna_id' => $this->session->userdata('id')
				);
				$data = array(
					'pengguna_password' => md5($password_baru)
				);
				$this->m_data->update_data($where, $data, 'pengguna');

				// alihkan halaman kembali ke halaman ganti password
				redirect('dashboard/ganti_password?alert=sukses');
			}else{
				// alihkan halaman kembali ke halaman ganti password
				redirect('dashboard/ganti_password?alert=gagal');
			}

		}else{
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_ganti_password');
			$this->load->view('dashboard/v_footer');
		}

	}

	// CRUD KATEGORI
	public function kategori()
	{
		$data['kategori'] = $this->m_data->get_data('kategori')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_kategori',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function kategori_tambah()
	{
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_kategori_tambah');
		$this->load->view('dashboard/v_footer');
	}

	public function kategori_aksi()
	{
		$this->form_validation->set_rules('kategori','Kategori','required');

		if($this->form_validation->run() != false){

			$kategori = $this->input->post('kategori');

			$data = array(
				'kategori_nama' => $kategori,
				'kategori_slug' => strtolower(url_title($kategori))
			);

			$this->m_data->insert_data($data,'kategori');

			redirect(base_url().'dashboard/kategori');
			
		}else{
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_kategori_tambah');
			$this->load->view('dashboard/v_footer');
		}
	}

	public function kategori_edit($id)
	{
		$where = array(
			'kategori_id' => $id
		);
		$data['kategori'] = $this->m_data->edit_data($where,'kategori')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_kategori_edit',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function kategori_update()
	{
		$this->form_validation->set_rules('kategori','Kategori','required');

		if($this->form_validation->run() != false){

			$id = $this->input->post('id');
			$kategori = $this->input->post('kategori');

			$where = array(
				'kategori_id' => $id
			);

			$data = array(
				'kategori_nama' => $kategori,
				'kategori_slug' => strtolower(url_title($kategori))
			);

			$this->m_data->update_data($where, $data,'kategori');

			redirect(base_url().'dashboard/kategori');
			
		}else{

			$id = $this->input->post('id');
			$where = array(
				'kategori_id' => $id
			);
			$data['kategori'] = $this->m_data->edit_data($where,'kategori')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_kategori_edit',$data);
			$this->load->view('dashboard/v_footer');
		}
	}


	public function kategori_hapus($id)
	{
		$where = array(
			'kategori_id' => $id
		);

		$this->m_data->delete_data($where,'kategori');

		redirect(base_url().'dashboard/kategori');
	}
	// END CRUD KATEGORI


	public function profil()
	{
		// id pengguna yang sedang login
		$id_pengguna = $this->session->userdata('id');

		$where = array(
			'pengguna_id' => $id_pengguna
		);

		$data['profil'] = $this->m_data->edit_data($where,'pengguna')->result();

		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_profil',$data);
		$this->load->view('dashboard/v_footer');
	}

	public function profil_update()
	{
		// Wajib isi nama dan email
		$this->form_validation->set_rules('nama','Nama','required');
		$this->form_validation->set_rules('email','Email','required');
		
		if($this->form_validation->run() != false){

			$id = $this->session->userdata('id');

			$nama = $this->input->post('nama');
			$email = $this->input->post('email');
			
			$where = array(
				'pengguna_id' => $id
			);

			$data = array(
				'pengguna_nama' => $nama,
				'pengguna_email' => $email
			);

			$this->m_data->update_data($where,$data,'pengguna');

			redirect(base_url().'dashboard/profil/?alert=sukses');
		}else{
			// id pengguna yang sedang login
			$id_pengguna = $this->session->userdata('id');

			$where = array(
				'pengguna_id' => $id_pengguna
			);

			$data['profil'] = $this->m_data->edit_data($where,'pengguna')->result();

			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_profil',$data);
			$this->load->view('dashboard/v_footer');
		}
	}

	public function artikel()
	{
		$data['artikel'] = $this->db->query("SELECT  *     FROM     artikel,kategori,pengguna     WHERE artikel_kategori=kategori_id and artikel_author=pengguna_id order by artikel_id desc")->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_artikel', $data);
		$this->load->view('dashboard/v_footer');
	}

	public function artikel_tambah()
	{
		$data['kategori'] = $this->m_data->get_data('kategori')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_artikel_tambah', $data);
		$this->load->view('dashboard/v_footer');
	}

	public function artikel_aksi()
	{
		// Wajib isi judul,konten dan kategori 
		$this->form_validation->set_rules('judul', 'Judul', 'required|is_unique[artikel.artikel_judul]');
		$this->form_validation->set_rules('konten', 'Konten', 'required');
		$this->form_validation->set_rules('kategori', 'Kategori', 'required');

		// Membuat gambar wajib di isi 
		if (empty($_FILES['sampul']['name'])) {
			$this->form_validation->set_rules('sampul', 'Gambar Sampul', 'required');
		}

		if ($this->form_validation->run() != false) {

			$config['upload_path']  = './gambar/artikel/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('sampul')) {

				// mengambil data tentang gambar 
				$gambar = $this->upload->data();

				$tanggal = date('Y-m-d H:i:s');
				$judul = $this->input->post('judul');
				$slug = strtolower(url_title($judul));
				$konten = $this->input->post('konten');
				$sampul = $gambar['file_name'];
				$author = $this->session->userdata('id');
				$kategori = $this->input->post('kategori');
				$status = $this->input->post('status');

				$data = array(
					'artikel_tanggal' => $tanggal,
					'artikel_judul' => $judul,
					'artikel_slug' => $slug,
					'artikel_konten' => $konten,
					'artikel_sampul' => $sampul,
					'artikel_author' => $author,
					'artikel_kategori' => $kategori,
					'artikel_status' => $status,
				);
				$this->m_data->insert_data($data, 'artikel');
				redirect(base_url() . 'dashboard/artikel');
			} else {

				$this->form_validation->set_message('sampul', $data['gambar_error'] = $this->upload->display_errors());

				$data['kategori'] = $this->m_data->get_data('kategori')->result();
				$this->load->view('dashboard/v_header');
				$this->load->view('dashboard/v_artikel_tambah', $data);
				$this->load->view('dashboard/v_footer');
			}
		} else {
			$data['kategori'] = $this->m_data->get_data('kategori')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_artikel_tambah', $data);
			$this->load->view('dashboard/v_footer');
		}
	}

	public function artikel_edit($id)
	{
		$where = array(
			'artikel_id' => $id
		);
		$data['artikel'] = $this->m_data->edit_data($where, 'artikel')->result();
		$data['kategori'] = $this->m_data->get_data('kategori')->result();
		$this->load->view('dashboard/v_header');
		$this->load->view('dashboard/v_artikel_edit', $data);
		$this->load->view('dashboard/v_footer');
	}

	public function artikel_update()
	{
		// Wajib isi judul,konten dan kategori 







		$this->form_validation->set_rules('judul', 'Judul', 'required');
		$this->form_validation->set_rules('konten', 'Konten', 'required');
		$this->form_validation->set_rules('kategori', 'Kategori', 'required');

		if ($this->form_validation->run() != false) {

			$id = $this->input->post('id');

			$judul = $this->input->post('judul');
			$slug = strtolower(url_title($judul));
			$konten = $this->input->post('konten');
			$kategori = $this->input->post('kategori');
			$status = $this->input->post('status');

			$where = array(
				'artikel_id' => $id
			);

			$data = array(
				'artikel_judul' => $judul,
				'artikel_slug' => $slug,
				'artikel_konten' => $konten,
				'artikel_kategori' => $kategori,

				'artikel_status' => $status,
			);

			$this->m_data->update_data($where, $data, 'artikel');


			if (!empty($_FILES['sampul']['name'])) {
				$config['upload_path']  = './gambar/artikel/';
				$config['allowed_types'] = 'gif|jpg|png';

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('sampul')) {
					// mengambil data tentang gambar 
					$gambar = $this->upload->data();

					$data = array(
						'artikel_sampul' => $gambar['file_name'],
					);
					$this->m_data->update_data($where, $data, 'artikel');
					redirect(base_url() . 'dashboard/artikel');
				} else {
					$this->form_validation->set_message(
						'sampul',
						$data['gambar_error'] = $this->upload->display_errors()
					);

					$where = array(
						'artikel_id' => $id
					);
					$data['artikel'] = $this->m_data->edit_data($where, 'artikel')->result();
					$data['kategori'] = $this->m_data->get_data('kategori')->result();
					$this->load->view('dashboard/v_header');
					$this->load->view('dashboard/v_artikel_edit', $data);

					$this->load->view('dashboard/v_footer');
				}
			} else {
				redirect(base_url() . 'dashboard/artikel');
			}
		} else {
			$id = $this->input->post('id');
			$where = array(
				'artikel_id' => $id
			);
			$data['artikel'] = $this->m_data->edit_data($where, 'artikel')->result();
			$data['kategori'] = $this->m_data->get_data('kategori')->result();
			$this->load->view('dashboard/v_header');
			$this->load->view('dashboard/v_artikel_edit', $data);

			$this->load->view('dashboard/v_footer');
		}
	}

	public function artikel_hapus($id)
	{
		$where = array(
			'artikel_id' => $id
		);

		$this->m_data->delete_data($where, 'artikel');
		redirect(base_url() . 'dashboard/artikel');
	}
}
	
