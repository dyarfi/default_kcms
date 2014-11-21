<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'alpha'         => ':field must contain only letters',
	'alpha_dash'    => ':field must contain only numbers, letters and dashes',
	'alpha_numeric' => ':field must contain only letters and numbers',
	'color'         => ':field must be a color',
	'credit_card'   => ':field must be a credit card number',
	'date'          => ':field must be a date',
	'decimal'       => ':field must be a decimal with :param2 places',
	'digit'         => ':field must be a digit',
	'email'         => ':field must be a email address',
	'email_domain'  => ':field must contain a valid email domain',
	'equals'        => ':field must equal :param2',
	'exact_length'  => ':field must be exactly :param2 characters long',
	'in_array'      => ':field must be one of the available options',
	'ip'            => ':field must be an ip address',
	//'matches'       => ':field must be the same as :param2',
	'matches'       => ':field must be the same as :param3',
	'min_length'    => ':field must be at least :param2 characters long',
	'max_length'    => ':field must not exceed :param2 characters long',
	'not_empty'     => ':field must not be empty',
	'numeric'       => ':field must be numeric',
	'phone'         => ':field must be a phone number',
	'range'         => ':field must be within the range of :param2 to :param3',
	'regex'         => ':field does not match the required format',
	'url'           => ':field must be a url',
	'url'           => array('Valid::url'=>':field must be a url valid'),
	'invalid_search_key' => ':field has invalid search key',
	'keywords'      => array('unique_keyword'=>':field already used'),
	
	'_url_default'		=> array(
							'Valid::url'=> ':field not valid'
						),
	
	'_url_custom'		=> array(
							'Valid::url'=> ':field not valid'
						),
	
	'_keyword_custom'	=> array('unique_keyword' => ':field is not available'),
	
	'_token_default'	=> array('token_check' => ':fields is not valid'),
	
	'_token_custom'		=> array('token_check' => ':fields is not valid'),
	
	'name'          => array(
						'unique_name' => ':field is not available',
						'name_exists' => ':field is already exists'
						),
	
	'email'			=> array(
						'email_exists'=> ':field already exists',
						'Valid::email'=> ':field not valid'
						),
	
	'sent_to'			=> array(
						'email_exists'=> ':field already exists',
						'Valid::email'=> ':field not valid format email'
						),
	
	
	'captcha'		=> array(
						'Captcha::valid' => ':field invalid',
						),
	
	'file_1'	=> array(
						'Upload::not_empty' => ':field not allowed to be empty',
						'Upload::size' => ':field size is not allowed',
						'Upload::type' => ':field is not valid',
						'unique_filename' => ':field name is not available to upload or delete the original file',
						),

	'file_2'	=> array(
						'Upload::not_empty' => ':field not allowed to be empty',
						'Upload::size' => ':field size is not allowed',
						'Upload::type' => ':field is not valid',
						'unique_filename' => ':field name is not available to upload or delete the original file',
						),
	
	'image_1'	=> array(
						'Upload::not_empty' => ':field not allowed to be empty',
						'Upload::size' => ':field size is not allowed',
						'Upload::type' => ':field is not valid',
						'unique_filename' => ':field name is not available to upload or delete the original file',
						),

	'image_2'	=> array(
						'Upload::not_empty' => ':field not allowed to be empty',
						'Upload::size' => ':field size is not allowed',
						'Upload::type' => ':field is not valid',
						'unique_filename' => ':field name is not available to upload or delete the original file',
						),	
	
);
