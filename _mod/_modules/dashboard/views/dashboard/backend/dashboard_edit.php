<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2>Edit <?php echo ucwords(str_replace('_', ' ', Request::current()->controller())); ?> Details</h2>

<form class="form_details" action="<?php echo url::site(ADMIN . Request::current()->controller().'/edit/'.$dashboard->id); ?>" method="post" enctype="multipart/form-data">
	<div class="form_wrapper">
		<?php echo sprintf($errors['name'], 'Name'); ?>
        <div class="form_row">
            <label>Name</label>
            <input type="text" name="name" id="name_ceo" class="required" value="<?php echo $fields['name']; ?>" />
        </div>
    
        <?php echo sprintf($errors['position'], 'Position'); ?>
        <div class="form_row">
            <label>Position</label>
            <input type="text" name="position" id="subject" class="required" value="<?php echo $fields['position']; ?>" />
        </div>

		<?php echo sprintf($errors['quote'], 'Quote'); ?>
        <div class="form_row">
           <label>Quote<br/>(30 words shown in front page)</label>
			<textarea name="quote" id="quote" class="small"><?php echo $fields['quote']; ?></textarea>
        </div>
		
		<?php if (isset($show_biography) && $show_biography) : ?>
	    <?php echo sprintf($errors['biography'], 'Biography'); ?>
        <div class="form_row">
            <label>Biography <br/>(80 words shown in front page)</label>
			<textarea name="biography" id="biography" class="small tiny_mce"><?php echo $fields['biography']; ?></textarea>
        </div>
		<?php endif; ?>
    
        <?php if (isset($show_upload) && $show_upload) : ?>
        <?php foreach ($uploads as $row_name => $row_params) : ?>
            <fieldset style="clear:both;">
                <legend><strong><?php echo $row_params['label']; ?></strong></legend>
                <?php if (isset($files[$row_name])) : ?>
                <div class="form_row">
                    <label><?php echo $row_params['label']; ?></label>
                    <div class="form_fields">
                        <?php if (is_file(Lib::config($this->_class_name.'.upload_path').$files[$row_name]->file_name) && in_array($files[$row_name]->file_type, $readable_mime)) : ?>
                        <div id="file_<?php echo $files[$row_name]->id; ?>">
                            <?php if (substr($files[$row_name]->file_type, 0, strlen('image/')) == 'image/') : ?>
                            <?php
                                $file_data	= pathinfo(url::base().Lib::config($this->_class_name.'.upload_url').$files[$row_name]->file_name);
                                $thumb_ext	= isset($row_params['image_manipulation']['thumbnails'][0]) ? '_resize_'.$row_params['image_manipulation']['thumbnails'][0] : '';
                            ?>
                            <img src="<?php echo url::base().Lib::config($this->_class_name.'.upload_url').$file_data['filename'].$thumb_ext.'.'.$file_data['extension']; ?>" alt="<?php echo url::base().Lib::config('dashboard.upload_url').$files[$row_name]->file_name; ?>" />
                            <?php elseif (substr($files[$row_name]->file_type, 0, strlen('audio/')) == 'audio/') : ?>
                            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
                                <param name="movie" value="<?php echo url::base(); ?>flash/singlemp3player.swf" />
            
                                <param name="quality" value="high" />
                                <param name="wmode" value="opaque" />
                                <param name="scale" value="noscale" />
                                <param name="salign" value="lt" />
                                <param name="FlashVars" value="file=<?php echo url::site(ADMIN . $this->uri->segment(2).'/download/'.$files[$row_name]->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
                                <param name="swfversion" value="8,0,0,0" />
                                <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
                                <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
            
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="<?php echo url::base(); ?>flash/singlemp3player.swf" width="320" height="20">
                                <!--<![endif]-->
                                    <param name="quality" value="high" />
                                    <param name="wmode" value="opaque" />
                                    <param name="scale" value="noscale" />
                                    <param name="salign" value="lt" />
                                    <param name="FlashVars" value="file=<?php echo url::site(ADMIN . $this->uri->segment(2).'/download/'.$files[$row_name]->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
                                    <param name="swfversion" value="8,0,0,0" />
            
                                    <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                    <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
                                    <div>
                                    <h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
                                    <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                                    </div>
                                <!--[if !IE]>-->
                                </object>
            
                                <!--<![endif]-->
                            </object>
                            <?php else : ?>
                            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="240" id="FLVPlayer">
                                <param name="movie" value="<?php echo url::base(); ?>flash/FLVPlayer_Progressive.swf" />
            
                                <param name="quality" value="high" />
                                <param name="wmode" value="opaque" />
                                <param name="scale" value="noscale" />
                                <param name="salign" value="lt" />
                                <param name="FlashVars" value="skinName=<?php echo url::base(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo url::site(ADMIN . $this->uri->segment(2).'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
                                <param name="swfversion" value="8,0,0,0" />
                                <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
                                <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
            
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="<?php echo url::base(); ?>flash/FLVPlayer_Progressive.swf" width="320" height="240">
                                <!--<![endif]-->
                                    <param name="quality" value="high" />
                                    <param name="wmode" value="opaque" />
                                    <param name="scale" value="noscale" />
                                    <param name="salign" value="lt" />
                                    <param name="FlashVars" value="skinName=<?php echo url::base(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo url::site(ADMIN . $this->uri->segment(2).'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
                                    <param name="swfversion" value="8,0,0,0" />
            
                                    <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                    <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
                                    <div>
                                    <h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
                                    <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                                    </div>
                                <!--[if !IE]>-->
                                </object>
            
                                <!--<![endif]-->
                            </object>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        Cannot preview this file
                        <?php endif; ?>
                    </div>
                </div>
            
                <div class="form_row">
                    <label class="no_border">&nbsp;</label>
                    <div class="form_field"><a href="<?php echo url::site(ADMIN . $this->uri->segment(2).'/download/'.$files[$row_name]->file_name); ?>"><img src="<?php echo url::base(); ?>images/cms/icon/disk.png" alt="<?php echo $files[$row_name]->file_name; ?>" /></a></div>
                </div>
            
                <?php echo sprintf($errors[$row_name], $row_params['label']); ?>
                <div class="form_row">
                    <label>Replace <?php echo $row_params['label']; ?></label>
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <label>&nbsp;</label>
                            <?php echo htmlspecialchars($row_params['note'], ENT_QUOTES); ?>
                        </div>
                    <?php endif; ?>
                </div>
            
                <?php if (isset($row_params['caption']) && $row_params['caption']) : ?>
                <div class="form_row">
                    <label>Caption</label>
                    <input type="text" name="<?php echo $row_name.'_caption'; ?>" id="<?php echo $row_name.'_caption'; ?>" value="<?php echo $fields[$row_name.'_caption']; ?>" />
                </div>
                <?php endif; ?>
            
                <?php if (isset($row_params['optional']) && $row_params['optional']) : ?>
                <div class="form_row">
                    <label>Delete <?php echo $row_params['label']; ?>?</label>
                    <input type="checkbox" name="delete_<?php echo $row_name; ?>" id="delete_<?php echo $row_name; ?>" value="1" />
                    <label for="delete_<?php echo $row_name; ?>" class="sub_label">Yes, delete this <?php echo $row_params['label']; ?></label>
                </div>
                <?php endif; ?>
            
                <?php else : ?>
            
                <?php echo sprintf($errors[$row_name], $row_params['label']); ?>
                <div class="form_row">
                    <label><?php echo $row_params['label']; ?></label>
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <label>&nbsp;</label>
                            <?php echo htmlspecialchars($row_params['note'], ENT_QUOTES); ?>
                        </div>
                    <?php endif; ?>
                </div>
            
                <?php if (isset($row_params['caption']) && $row_params['caption']) : ?>
                <div class="form_row">
                    <label>Caption</label>
                    <input type="text" name="<?php echo $row_name.'_caption'; ?>" id="<?php echo $row_name.'_caption'; ?>" value="<?php echo $fields[$row_name.'_caption']; ?>" />
                </div>
                <?php endif; ?>
            
                <?php endif; ?>
        	</fieldset>    
        <?php endforeach; ?>
        <?php endif; ?>
    
        <?php echo sprintf($errors['status'], 'Status'); ?>
        <div class="form_row">
            <label>Status</label>
            <select name="status" id="status" class="required">
                <option value="">&nbsp;</option>
                <?php foreach ($statuses as $row) : ?>
                <option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars(ucfirst($row), ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    
        <div class="form_row">
            <label>Created</label>
            <div class="form_field"><?php echo ($dashboard->added != 0) ? date(Lib::config('admin.date_format'), $dashboard->added) : '-'; ?></div>
        </div>
        <div class="form_row">
            <label>Last Modified</label>
            <div class="form_field"><?php echo ($dashboard->modified != 0) ? date(Lib::config('admin.date_format'), $dashboard->modified) : '-'; ?></div>
        </div>
	</div>
    <div class="form_row">
		<label>&nbsp;</label>
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>

<?php if (isset($show_upload) && $show_upload) : ?>
	<!--script type="text/javascript">
		<?php 
			foreach ($uploads as $row_name => $row_params) : 
				if (isset($row_params['file_type']) && $row_params['file_type'] != '') :
					$accepted	= explode(',', $row_params['file_type']);
		?>
			var file_fields		= '<?php echo $row_name; ?>';
			var accepted_type	= '<?php echo $row_params['file_type']; ?>';
		<?php 
				endif;
			endforeach; 
		?>
    </script-->
<?php endif; ?>