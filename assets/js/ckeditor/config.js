/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	config.enterMode				= CKEDITOR.ENTER_BR;
	config.shiftEnterMode			= CKEDITOR.ENTER_BR;
	config.language 				= 'en';
	config.uiColor 					= '#bebebe';
	//config.filebrowserImageBrowseUrl = '/browser/browse.php?type=Images';
	//config.filebrowserImageBrowseUrl = '/ckfinder/ckfinder.html?Type=Images';
	config.toolbar_Full 			=
    	[
    		['Source','Save','NewPage','Preview','Templates','Undo','Redo','Find','Replace','SelectAll','RemoveFormat','BidiLtr', 'BidiRtl','JustifyLeft','JustifyCenter','JustifyRight',
    		'JustifyBlock','Bold','Italic','Underline','Strike','Subscript','Superscript','TextColor','BGColor'],['Maximize'],
    		//['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
    		//['Print', 'SpellChecker', 'Scayt'],
    		//['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat','About','Smiley','PageBreak'],											
    		//['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField','CreateDiv','Anchor', 'ShowBlocks'],										
    		'/',
    		['NumberedList','BulletedList','Outdent','Indent','Blockquote','Link','Unlink','Image','Flash','Table','HorizontalRule','SpecialChar','Styles','Format','Font','FontSize',]
    		
    		
    		//'/',
    		
    		
    	];
};

CKEDITOR.editorConfig = function(config) {

   config.filebrowserBrowseUrl 		=  base_URL+'assets/js/kcfinder/browse.php?type=files';
   config.filebrowserImageBrowseUrl =  base_URL+'assets/js/kcfinder/browse.php?type=images';
   config.filebrowserFlashBrowseUrl =  base_URL+'assets/js/kcfinder/browse.php?type=flash';
   config.filebrowserUploadUrl		=  base_URL+'assets/js/kcfinder/upload.php?type=files';
   config.filebrowserImageUploadUrl =  base_URL+'assets/js/kcfinder/upload.php?type=images';
   config.filebrowserFlashUploadUrl =  base_URL+'assets/js/kcfinder/upload.php?type=flash';
};

