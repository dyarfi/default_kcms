<?php
class lang {
	
	// Get language list data with PUBLISH status
	public static function _valid_langId () {
		// Load language model
		$language_id = Model_Language::instance()->find(array('status'=>1));
		$buffers_id = array();
		if (!empty($language_id)) {
			foreach($language_id as $langid){
				$buffers_id[$langid->id] = $langid->id;
			}
		}
		$language_id	= $buffers_id;
		// Return valid ids 
		return $language_id;
	}

	// This function is to check all in content for default given language
	/*
	public static function _check_valid_lang ($prefix = '') {
		if (!empty($prefix)) {			
			$languages = Model_Language::instance()->load_by_prefix(I18n::lang());
			
			$lang_id = $languages->id;
			
			$maintenance = FALSE;
			if (!empty($languages)) {
				foreach($languages as $language) {

					$check[1][$languages->id] = Model_ContentMenus::instance()->find(array('lang_id'=> $lang_id));
					$check[2][$languages->id] = Model_ContentPages::instance()->find(array('lang_id'=> $lang_id));
					$check[4][$languages->id] = Model_News::instance()->find(array('lang_id'=> $lang_id));

					// print_r($check); exit();
					if (empty($check[1][$languages->id])) {
						$maintenance = TRUE;
					} elseif (empty($check[2][$languages->id])) {
						$maintenance = TRUE;
					} elseif (empty($check[3][$languages->id])) {
						$maintenance = TRUE;
					} elseif (empty($check[4][$languages->id])) {
						$maintenance = TRUE;
					} else {
						$maintenance = FALSE;
					}
				}
			}
			//print_r($maintenance); exit();
			return $maintenance;
		}
	}
	 */
	
}
?>
