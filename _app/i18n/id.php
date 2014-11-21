<?php defined('SYSPATH') or die('No direct script access.');

return array(
    // General Page Settings
    'page_title'        => SITE_NAME,
    'meta_keywords'     => SITE_NAME,
    'meta_description'  => SITE_NAME,
    'google_analytics'  => SITE_NAME,
    'meta_copyright'    => SITE_NAME,
    'system_language'   => array('id'=>'Indonesia', 'en'=>'English'),
	'is_system'			=> 'Is System',
    
    // Label
    'language'          => 'Bahasa',
    'title'             => 'Title',
    'label'             => 'Label',
    'short_desc'        => 'Short Description',
    'full_desc'         => 'Full Description',
    'date'              => 'Date',
    'time'              => 'Time',
    'datetime'          => 'Date Time',
    'status'            => 'Status',
	'menu_panel'		=> 'MAIN MENU',
    'status_value'      => array(0=>'Unpublish',1=>'Publish'),
	'status_value_user' => array(0=>'Inactive',1=>'Active'),
	'default'           => 'Default',
	'default_value'     => array(0=>'No',1=>'Yes'),
	'delete_value'      => array(0=>'Undelete',1=>'Deleted'),
    'content_listing'   => '%type Listing',
    'content_detail'    => 'View %type Details',
    'content_edit'      => 'Edit %type Details',
    'content_translate' => 'Translate %type Details',
    'content_new'       => 'Add New %type',
    'changed_status'    => 'Changed Status',
    'date_format'       => 'Format : dd-mm-yyyy',
    'title_action'      => 'Click untuk %action item ini',
    'page'              => 'Page',
    'image'             => 'Image',
	'icon'              => 'Ikon',
    'url'               => 'Url',
    'more'              => 'Lihat',
	'news'				=> 'Berita',
	'product' 			=> 'Produk',
	'promo' 			=> 'Promosi',
	'price' 			=> 'Harga',
	'channel' 			=> 'Channel',
	'distribution'		=> 'Distribusi',
	'about' 			=> 'Tentang Kami',
	'latest'       		=> '%type Terkini',
	'back_to'      		=> 'Kembali ke %type',
	'search'			=> 'Cari',
	'language'			=> 'Bahasa',
	'no_detail'			=> 'Tidak ada Detil %type', 
	'here'				=> 'Anda berada di',
	'home'				=> 'Beranda', 
	'contact'			=> 'Hubungi', 
	'us'				=> 'Kami',
	'or'				=> 'atau',
	'send_message'		=> 'Kirim Pesan',
	'no_content'		=> 'Tidak ada isinya', 
	
	// Member
	'login'				=> 'Login',
	'free'				=> 'Free',
	'register'			=> 'Register',
    'username'			=> 'Username',
	'fullname'			=> 'Full Name',
	'phone'				=> 'Handphone',
	'email'				=> 'Email',
	'password'			=> 'Password',
	'password2'			=> 'Confirm Password',
	'address'			=> 'Address',
	'country'			=> 'Country',
	'church'			=> 'Church',	
	'birthday'			=> 'Birthday',
	'about'				=> 'About',
	'forgot'			=> 'Lupa',
	'forgot_password'	=> 'Isi email anda untuk melanjutkan',
	'gender'			=> 'Gender',
	'confirm'			=> 'Konfirmasi',
	'captcha'			=> 'Captcha',
	'agree'				=> 'Persetujuan',
	'agreement'			=> 'Saya setuju dengan Syarat dan Ketentuan',
	'activating_acc'	=> 'Aktivasi Akun',
	'check_email'		=> 'Cek email anda di %email',
	'dashboard'			=> 'Dashboard',
	'admin_panel'		=> 'Administration Control Panel',
	'update_profile'	=> 'Update Profile',
	'view_profile'		=> 'View Profile',
	'logout'			=> 'Logout',
	'message_password'	=> 'Password anda salah',
	'message_email'		=> 'Email tidak terdaftar',
	'account_free'		=> 'Untuk memiliki account di '.SITE_NAME.', anda harus melengkapi data-data dibawah ini',
	'captcha_code'		=> 'Captcha',
	'captcha_reload'	=> 'Reload Captcha',
	'submit'			=> 'Submit',	
	'cancel'			=> 'Cancel',	
	
	//Search 
	'search_empty'		=> 'Tidak ada hasil pencarian',
	
	// Admin
	'admin'				=> 'Admin',
	'menu_panel'		=> 'MODULE PANEL MENUS',
	'welcome_admin'		=> 'Welcome, %admin',
	'error_login'		=> 'Authentication failed',
	
	// Error encountered
	'error_enc'			=> 'Beberapa kesalahan yang muncul, silakan memeriksa rincian yang Anda masukkan.',
	
	// Contact Form
	'admin_contact'		=> '<h2>Form hubungi kami</h2>Administrator yang terhormat, ada seseorang yang menghubungi melalui web<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nama : %name,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Perusahaan : %company,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email : %email,<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pesan : %message<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							Terima kasih atas perhatiannya.<br/>
							<span style="font-style: italic; color: lightgray; font-size: 11px; margin: 0px">
							<b>%site_name</b><br/>%address<br/>
							Phone : %phone Fax : %fax<br/>
							<span>',
	'public_contact'	=> '<h2>Form hubungi kami</h2>
							Yang terhormat %name, terima kasih telah menghubungi kami.<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							Kami akan dengan senang hati merespon pesan email anda secepatnya.<br/>
							-------------------------------------------------------------------------------------------------------------<br/>
							Terima kasih atas perhatiannya.<br/>
							<span style="font-style: italic; color: lightgray; font-size: 11px; margin: 0px">
							<b>%site_name</b><br/>%address<br/>
							Phone : %phone Fax : %fax<br/>
							<span>',
	'contact_success'	=> 'Pesan Anda telah terkirim',
	'form_contact_us'	=> 'Form Hubungi Kami',
	
	// Contact Us
	'name'				=> 'Name',
	'company'			=> 'Perusahaan',
	'message'			=> 'Pesan',
	
	// Career
	'career'			=> 'Karir',
	'no_career'			=> 'Tidak ada lowongan saat ini.',
	
	// Pagination			
	'first'				=> 'Pertama',
	'previous'       	=> 'Sebelumnya',
	'next'				=> 'Selanjutnya',
	'last'       		=> 'Terakhir',
    
    //Day 
    'Sunday'            => 'Minggu',
    'Monday'            => 'Senin',
    'Tuesday'           => 'Selasa',
    'Wednesday'         => 'Rabu',
    'Thursday'          => 'Kamis',
    'Friday'            => "Jum'at",
    'Saturday'          => 'Sabtu',    
	
    //Month 
    'January'           => 'Januari',
    'February'          => 'Februari',
    'March'             => 'Maret',
    'April'             => 'April',
    'May'               => 'Mei',
    'June'              => 'Juni',
    'July'              => 'Juli',
    'August'            => 'Agustus',
    'September'         => 'September',
    'October'           => 'Oktober',
    'November'          => 'November',
    'December'          => 'Desember',
    
    // Warning
    'warning_delete'    => 'Ingin menghapus item ini ? Item yang dihapus tidak dapat di-restore kembali',
	
    // Error
    'error_no_data'     => 'No record found',
    'error_no_translate'        => 'No available translate yet',
    'error_no_direct_access'    => 'No direct script access allowed',
    'error_upload_file' => array (
                                '501' => 'Ups. Sistem error, tidak dapat mengunggah file',
                                '503' => 'File yang anda pilih tidak diijinkan',
                                '504' => 'Ukuran gambar yang anda pilih tidak diijinkan',
                            )
    
    
);