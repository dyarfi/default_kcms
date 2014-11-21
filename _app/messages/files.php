<?php defined('SYSPATH') or die('No direct script access.');

// if(i18n::$lang == 'en') {
// return array(
	// 'csv_data' => array(
					// 'Upload::not_empty'     => ':field tidak boleh kosong',
					// 'Upload::type'			=> ':field wrong type'
				  // ),

// );
// } elseif(i18n::$lang == 'id') {
// return array(
	// 'csv_data' => array(
					// 'Upload::not_empty'     => ':field tidak boleh kosong',
					// 'Upload::type'			=> 'tipe file :field salah'
				  // ),
	// );
// }

return array(
	'image' => array(
					'Upload::not_empty'     => ':field cannot empty',
					'Upload::type'			=> ':field wrong type',
				  ),
);