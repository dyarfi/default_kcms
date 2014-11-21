<?php defined('SYSPATH') or die('No direct script access.');

class Lib extends Controller {
	
	protected static $_instance;
	
	// Instances or factory
	public static function instance () {
		if (self::$_instance === NULL)
			self::$_instance	= new self();
		
		return self::$_instance;
	}

	/**
	* Load kohana config in shorter way
	*
	* @access	public
	* @param	array
	* @return	array
	*/	
	public static function config($config = array()) {
		// Return load function
		return Kohana::$config->load($config);
	}
	
	/**
	* Load array and return to object 
	*
	* @access	public
	* @param	array
	* @return	object
	*/	
	public static function to_object($arr = NULL, $all = TRUE) {        
        if(!is_array($arr)):
            return $arr;
        endif;
        
        $object = new stdClass();
        
        foreach ($arr as $k=>$v):
            $k = strtolower(trim($k));
            if (!empty($k)):
                if ($all):
                    $object->$k = self::to_object($v);
                else:
                    $object->$k = $v;
                endif;
            endif;
        endforeach;
        return $object;
    }
	
	// Send download response to browser
	public static function _download ($file='',$encode='') {
		if (!empty($encode)) {
			$file = base64_decode($file);
		}
		
		$filename	= pathinfo($file);
		$mime_type	= File::mime($file);
		
		$options['mime_type']	= $mime_type;
		$options['inline']		= FALSE;
		
		return Response::factory()->send_file($file,$filename['basename'],$options);
	}
	
	// Send headers to force download file
	public static function _download_file_force($nicename='',$file='') {		
		if (!$file)
			return;
		
		$data = NULL;
		$filename = $file;
		
		if (empty($filename))
			return FALSE;

		if (is_file($filename))
		{
			// Get the real path
			$filepath = str_replace('\\', '/', realpath($filename));

			// Set filesize
			$filesize = filesize($filepath);

			// Get filename
			$filename = substr(strrchr('/'.$filepath, '/'), 1);

			// Get extension
			$extension = strtolower(substr(strrchr($filepath, '.'), 1));
		}
		else
		{
			// Get filesize
			$filesize = strlen($data);

			// Make sure the filename does not have directory info
			$filename = substr(strrchr('/'.$filename, '/'), 1);

			// Get extension
			$extension = strtolower(substr(strrchr($filename, '.'), 1));
		}

		// Get the mime type of the file
		$mime = File::mime_by_ext($extension);

		if (empty($mime))
		{
			// Set a default mime if none was found
			$mime = array('application/octet-stream');
		}

		// Generate the server headers
		header('Content-Type: '.$mime[0]);
		header('Content-Disposition: attachment; filename="'.(empty($nicename) ? $filename : $nicename).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.sprintf('%d', $filesize));

		// More caching prevention
		header('Expires: 0');

		if (Request::$user_agent === 'Internet Explorer')
		{
			// Send IE headers
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			// Send normal headers
			header('Pragma: no-cache');
		}

		// Clear the output buffer
		//Kohana::close_buffers(FALSE);

		if (isset($filepath))
		{
			// Open the file
			$handle = fopen($filepath, 'rb');

			// Send the file data
			fpassthru($handle);

			// Close the file
			fclose($handle);
		}
		else
		{
			// Send the file data
			echo $data;
		}
		
	}
	
	// Check if upload dir exists or create one and upload file if true and return file name
	public static function _upload_to ($file = array(), $filename = '', $upload_path='', $file_perm = '') {
		if (!empty($upload_path)) {
			
			if ( ! is_dir($upload_path) OR ! is_writable(realpath($upload_path))) {	
				mkdir($upload_path);
			}
			
			return Upload::save($file, $filename, $upload_path, $file_perm);
		} else {
			return false;
		}
		
	}

	// Image auto manipulation to crop and resize Image file from config files
	public static function _auto_image_manipulation ($upload_path='', $files='',$config='') {
		
		$files		= $files->find();
		$buffers	= array('index.html');
		
		foreach ($files as $row) {
			if (!empty($files) && is_file($upload_path . $row->file_name) && is_readable($upload_path . $row->file_name)) {
				
				/** store to save list **/
				$buffers[]		= $row->file_name;
				
				if (isset($config['uploads'][$row->field_name]['image_manipulation']) && substr($row->file_type, 0, strlen('image/')) == 'image/') {
					$params			= $config['uploads'][$row->field_name]['image_manipulation'];
					$image_ratio	= isset($params['ratio']) ? $params['ratio'] : 'auto';
					$thumbs			= isset($params['thumbnails']) ? $params['thumbnails'] : array();
					$crops			= isset($params['crop']) ? $params['crop'] : array();

					switch ($image_ratio) {
						case 'none':
							$image_ratio	= Image::NONE;
							break;
						case 'width':
							$image_ratio	= Image::WIDTH;
							break;
						case 'height':
							$image_ratio	= Image::HEIGHT;
							break;
						default:
							$image_ratio	= Image::AUTO;
					}

					$file_name		= $upload_path.$row->file_name;
					
					$file_data		= pathinfo($file_name);

					$image_raw		= Image::factory($file_name);

					if (isset($params['thumbnails'])) {
						foreach ($params['thumbnails'] as $size) {
							if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'])) {
								/** store to save list **/
								$buffers[]		= $file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'];

								continue;
							}

							list($width, $height)	= explode('x', $size);

							$image_raw		= Image::factory($file_name);
							
							$image_raw->resize((int) $width, (int) $height, (int) $image_ratio)
							->save($file_data['dirname'].'/'.$file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'],100);
							
							unset($image_raw);

							/** store to save list **/
							$buffers[]		= $file_data['filename'].'_resize_'.$size.'.'.$file_data['extension'];
						}
					}

					if (isset($params['crop'], $params['crop'][0])) {
						if (!is_array($params['crop'][0])) {
							if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'])) {
								/** store to save list **/
								$buffers[]		= $file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'];

								continue;
							}

							list($orig_w, $orig_h)	= getimagesize($file_name);

							$crop_aligns			= array('top', 'bottom', 'left', 'right', 'center');

							if (isset($params['crop'][0]) && strpos($params['crop'][0], 'x') != 0) {
								list($width, $height)	= explode('x', $params['crop'][0]);

								if (($orig_w / $width) < ($orig_h / $height))
									$image_ratio		= Image::WIDTH;
								else
									$image_ratio		= Image::HEIGHT;
							}

							if (isset($params['crop'][1]) && strpos($params['crop'][1], 'x') != 0)
								list($crop_x, $crop_y)	= explode('x', $params['crop'][1]);
							else if (isset($params['crop'][1]) && $params['crop'][1] == 'center')
								list($crop_x, $crop_y)	= array('center', 'center');
							else if (isset($params['crop'][1], $params['crop'][2]))
								list($crop_x, $crop_y)	= array($params['crop'][1], $params['crop'][2]);

							$image_raw		= Image::factory($file_name);
							
							$image_raw->resize((int) $width, (int) $height, (int) $image_ratio)
							->crop((int) $width, (int) $height, (int) $crop_x, (int) $crop_y)
							->save($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'],100);

							unset($image_raw);
							
							/** store to save list **/
							$buffers[]		= $file_data['filename'].'_crop_'.$params['crop'][0].'.'.$file_data['extension'];
						} else {
							foreach ($params['crop'][0] as $crop_row) {
								if (is_file($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'])) {
									/** store to save list **/
									$buffers[]		= $file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'];

									continue;
								}

								list($orig_w, $orig_h)	= getimagesize($file_name);

								$crop_aligns			= array('top', 'bottom', 'left', 'right', 'center');

								if (isset($params['crop'][0]) && strpos($crop_row, 'x') != 0) {
									list($width, $height)	= explode('x', $crop_row);

									if (($orig_w / $width) < ($orig_h / $height))
										$image_ratio		= Image::WIDTH;
									else
										$image_ratio		= Image::HEIGHT;
								}

								
								if (isset($params['crop'][1]) && strpos($params['crop'][0][1], 'x') != 0)
									list($crop_x, $crop_y)	= explode('x', $crop_row);
								else if (isset($params['crop'][1]) && $params['crop'][1] == 'center')
									list($crop_x, $crop_y)	= array('center', 'center');
								else if (isset($params['crop'][1], $params['crop'][2]))
									list($crop_x, $crop_y)	= array($params['crop'][1], $params['crop'][2][0]);
								
								$image_raw		= Image::factory($file_name);
								
								$image_raw->resize((int) $width, (int) $height, (int) $image_ratio)
								->crop((int) $crop_x, (int) $crop_y)
								->save($file_data['dirname'].'/'.$file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'],100);

								unset($image_raw);
								
								/** store to save list **/
								$buffers[]		= $file_data['filename'].'_crop_'.$crop_row.'.'.$file_data['extension'];
							}
						}
					}

					unset($image_raw,$file_name);

				}
			}	
		}
		
		$dir_name		= $upload_path;
		
		if(!is_dir($dir_name)) {
			mkdir($dir_name);
		}
		
		$folder_files	= scandir($dir_name);
		$deleted_files	= array_diff($folder_files, $buffers);

		foreach ($deleted_files as $row) {
			if(is_file($dir_name.$row) && is_writeable($dir_name.$row))
				unlink($dir_name.$row);
		}
	}
	
	// Used for Traversing HTML ul li
	public static function traverse($class='',$url='',$parent_id='',$data='') {
		if (!is_array($data))
			return array();

		$last  = '';
		$html  = '';
		
	/*
		if (count($data) <= 1) {
			$html .= '<ul class="'.$class.'">'; 
			foreach ($data as $val) {
				if ($val->parent_id == $parent_id) {
					$html	.= '<li'.$last.'><a href="'.$val->name.'">'.$val->title.'</a></li>';
				} 
			}
			$html .= '</ul>';
		} else {
			$html .= '<ul class="'.$class.'">'; 
			foreach ($data as $val) {
				if ($val->parent_id == $parent_id) {
					$html	.= '<li'.$last.'><a href="'.$val->name.'">'.$val->title.'</a>
								'. self::traverse($class, $val->id, $data) .'
								</li>';
				} 
			}
			$html .= '</ul>';
		}
	*/	
			$i = 0;
			$j = count($data);
			$children = $data;
			if ($j != 1) {
				$html .= '<ul class="'.$class.'">'; 
				foreach ($data as $val) {					
					if ($i == $j) $last = ' class="noBor"'; 
					if ($val->parent_id == $parent_id) {
						$html	.= '<li'.$last.'><a href="'.URL::site($url).'/'.$val->name.'">'.ucfirst(strip_tags($val->title)).'</a>';
						
						//foreach ($children as $child) {
							//if  ($child->parent_id != $val->id){
								//$data = $children;
							//}
						//}
							
						$html	.= Lib::traverse($class, $url, $val->id, $data);	
						
						$html	.= '</li>';
						$i++;
					} 
				}
				$html .= '</ul>';
			}
		
		return $html;
	}

	/* make a URL small */
	public static function bitly_url($url) {
		
		$login = self::config('site.login_bitly');
		$appkey = self::config('site.appkey_bitly');
		
		// $url = 'http://www.pemulihan.or.id/about'; 
		// print_r($url);
	
		// $format = 'xml';
		$format = 'json';
		// $version = '2.0.1';
		
		// create the URL
		// For version 2
		// $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
		// For version 3
		// http://api.bitly.com/v3/shorten?login=bitlyapidemo&apiKey=R_0da49e0a9118ff35f52f629d2d71bf07&longUrl=http%3A%2F%2Fbetaworks.com%2Fpage%3Fparameter%3Dvalue%23anchor&format=json
		$bitly = 'http://api.bitly.com/v3/shorten?longUrl='.rawurlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
		 
		// get the url
		// could also use cURL here
		$response = file_get_contents($bitly);
		// print_r($response); exit();

		// parse depending on desired format
		if(strtolower($format) == 'json') {
			$json = @json_decode($response,true);
			if ($json['status_code'] == '200')
				return $json['data']['url'];
		}
		else //xml
		{
			$xml = simplexml_load_string($response);
			if ($xml['status_code'] == '200')
				return 'http://bit.ly/'.$xml->data->hash;
		}
		
		/* usage */
		// $short = bitly_url('http://davidwalsh.name','davidwalshblog','R_96acc320c5c423e4f5192e006ff24980','json');
		// echo 'The short URL is:  '.$short; 

		// returns:  http://bit.ly/11Owun
	}	
	
	public static function _explode_keywords($string = '', $delimiter = ', ') {
		if (empty($string))
			return;
		
		//$return = preg_replace('/\s+/',' ',$string);
		$return = explode(' ', trim(strip_tags($string),"\x00..\x1F"));
		$return = str_replace(array(',', '.'), '', $return);
		$return = implode(', ', $return);
		$return = preg_replace('/\s+/',' ',$return);
		
		if (!empty($return))
			return trim(Text::limit_words($return,64,''),"\x00..\x1F");
	}
	
	public static function _clear_whitespace($string = '') {
		$return = strip_tags($string);
		return preg_replace('/\s+/',' ', $return);
	}

	public static function _get_curl_meta_tags($options=array()) {
		if (!is_array($options))
			return;
		
		$request = Request::factory($options['url'])->client(new Request_Client_Curl)->execute();

		$buffers = array();
		$matches = array();
		foreach ($options['meta'] as $meta) {
			preg_match_all('/[<]meta name=\"'.$meta.'\" content=\"([^<]*)\"\/>/i',$request->body(),$matches[$meta]);
			$buffers[$meta] = $matches[$meta][1][0];
		}
		
		return ($buffers) ? $buffers : ''; 
	}
	
	public static function _trim_strip($string='') {
		return trim(strip_tags($string),"\n\r\x00..\x1F");
	}
	
	public static function _get_curl_title_tags($title='') {
		if (empty($title))
			return;
		
		$request = Request::factory($title)->client(new Request_Client_Curl)->execute();

		$pattern = '/[<]title[>]([^<]*)[<][\/]title>/i';
		preg_match($pattern, $request->body(), $matches);
		
		return (@$matches[1]) ? @$matches[1] : '';
	}
	
	public static function recursive_remove_directory($directory, $empty=FALSE) {
        if(substr($directory,-1) == '/') :
            $directory = substr($directory,0,-1);
        endif;
        if(!file_exists($directory) || !is_dir($directory)) :
            return FALSE;
        elseif(is_readable($directory)) :
            $handle = opendir($directory);
            while (FALSE !== ($item = readdir($handle))) :
                if($item != '.' && $item != '..') :
                    $path = $directory.'/'.$item;
                        if(is_dir($path)) :
                            Helper_Common::recursive_remove_directory($path);
                        else:
                            unlink($path);
                        endif;
                endif;
            endwhile;
            closedir($handle);
            if($empty == FALSE) :
                if(!rmdir($directory)):
                    return FALSE;       
                endif;
            endif;
        endif;
        return TRUE;
    }
	
	public static function multi_attach_mail($to, $files, $sendermail){
        // email fields: to, from, subject, and so on
        $from = "Files attach <".$sendermail.">"; 
        $subject = date("d.M H:i")." F=".count($files); 
        $message = date("Y.m.d H:i:s")."\n".count($files)." attachments";
        $headers = "From: $from";
        // boundary 
        $semi_rand = md5(time()); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        // headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
        // multipart boundary 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
        // preparing attachments
        for($i=0;$i<count($files);$i++){
            if(is_file($files[$i])){
                $message .= "--{$mime_boundary}\n";
                $fp =    @fopen($files[$i],"rb");
            $data =    @fread($fp,filesize($files[$i]));
                        @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
                "Content-Description: ".basename($files[$i])."\n" .
                "Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                }
            }
        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $sendermail;
        $ok = @mail($to, $subject, $message, $headers, $returnpath); 
        if($ok){ return $i; } else { return 0; }
    }
}

