<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>

<?php 
echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$news->id), array(
															'enctype' => 'multipart/form-data', 
															'method' => 'post', 
															'class' => 'general autovalid form_details',
															'id' => ''
														));
?>

	<div class="form_wrapper">
    
        <div class="form_row">
            <?php echo Form::label('subject','Subject'); ?>
			<?php echo Form::input('subject',!empty($fields['subject']) ? $fields['subject'] : '',array('id'=>'subject', 'class'=>'required')); ?>
			<?php echo sprintf($errors['subject'], 'Subject'); ?>
        </div>
		
        <div class="form_row">
            <?php echo Form::label('name','Name'); ?>
			<?php echo Form::input('name',$fields['name'],array('id'=>'name', 'class'=>'required', 'rel'=>$class_name)); ?>
			<span class="red"></span>
			<?php echo sprintf($errors['name'], 'Name'); ?>
        </div>
        
        <div class="form_row">
			<?php echo Form::label('news_date','News Date'); ?>
			<?php echo Form::input('news_date',$fields['news_date'],array('id'=>'news_date', 'class'=>'required datepicker')); ?>
			<?php echo sprintf($errors['news_date'], 'News Date'); ?>
        </div>
    
        <?php if (isset($show_synopsis) && $show_synopsis) : ?>
        <div class="form_row">
            <?php echo Form::label('synopsis','Synopsis'); ?>
			<?php echo Form::textarea('synopsis',!empty($fields['synopsis']) ? $fields['synopsis'] : '' ,array('id'=>'synopsis', 'class'=>'small')); ?>
			<?php echo sprintf($errors['synopsis'], 'Synopsis'); ?>
        </div>
        <?php endif; ?>
    
        <div class="form_row">
			<?php echo Form::label('text','Text'); ?>
			<?php echo Form::textarea('text',!empty($fields['text']) ? $fields['text'] : '' ,array('id'=>'text', 'class'=>'required ckeditor')); ?>
			<?php echo sprintf($errors['text'], 'Text'); ?>
        </div>
    
        <?php if (isset($show_upload) && $show_upload) : ?>
        <?php foreach ($uploads as $row_name => $row_params) : ?>
            <fieldset style="clear:both;">
                <legend><strong><?php echo $row_params['label']; ?></strong></legend>
                <?php if (isset($files[$row_name])) : ?>
                <div class="form_row">
                    <label><?php echo $row_params['label']; ?></label>
                    <div class="form_fields">
                        <?php if (is_file($upload_path.$files[$row_name]->file_name) && in_array($files[$row_name]->file_type, $readable_mime)) : ?>
                        <div id="file_<?php echo $files[$row_name]->id; ?>">
                            <?php if (substr($files[$row_name]->file_type, 0, strlen('image/')) == 'image/') : ?>
                            <?php
                                $file_data	= pathinfo(URL::base().$upload_url.$files[$row_name]->file_name);
                                $thumb_ext	= isset($row_params['image_manipulation']['thumbnails'][0]) ? '_resize_'.$row_params['image_manipulation']['thumbnails'][0] : '';
                            ?>
                            <img src="<?php echo URL::base().$upload_url.$file_data['filename'].$thumb_ext.'.'.$file_data['extension']; ?>" alt="<?php echo URL::base().$upload_url.$files[$row_name]->file_name; ?>" />
                            <?php elseif (substr($files[$row_name]->file_type, 0, strlen('audio/')) == 'audio/') : ?>
                            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
                                <param name="movie" value="<?php echo URL::base(); ?>flash/singlemp3player.swf" />
            
                                <param name="quality" value="high" />
                                <param name="wmode" value="opaque" />
                                <param name="scale" value="noscale" />
                                <param name="salign" value="lt" />
                                <param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
                                <param name="swfversion" value="8,0,0,0" />
                                <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
                                <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
            
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="<?php echo URL::base(); ?>flash/singlemp3player.swf" width="320" height="20">
                                <!--<![endif]-->
                                    <param name="quality" value="high" />
                                    <param name="wmode" value="opaque" />
                                    <param name="scale" value="noscale" />
                                    <param name="salign" value="lt" />
                                    <param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
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
                                <param name="movie" value="<?php echo URL::base(); ?>flash/FLVPlayer_Progressive.swf" />
            
                                <param name="quality" value="high" />
                                <param name="wmode" value="opaque" />
                                <param name="scale" value="noscale" />
                                <param name="salign" value="lt" />
                                <param name="FlashVars" value="skinName=<?php echo URL::base(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
                                <param name="swfversion" value="8,0,0,0" />
                                <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
                                <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
            
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="<?php echo URL::base(); ?>flash/FLVPlayer_Progressive.swf" width="320" height="240">
                                <!--<![endif]-->
                                    <param name="quality" value="high" />
                                    <param name="wmode" value="opaque" />
                                    <param name="scale" value="noscale" />
                                    <param name="salign" value="lt" />
                                    <param name="FlashVars" value="skinName=<?php echo URL::base(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
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
                    <!--label class="no_border">&nbsp;</label-->
                    <div class="form_field"><a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>"><img src="<?php echo IMG; ?>admin/disk.png" alt="<?php echo $files[$row_name]->file_name; ?>" /></a></div>
                </div>
            
                <?php echo sprintf($errors[$row_name], $row_params['label']); ?>
                <div class="form_row">
                    <label>Replace <?php echo $row_params['label']; ?></label>
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <!--label>&nbsp;</label-->
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
            <div class="form_field"><?php echo ($news->added != 0) ? date(Lib::config('admin.date_format'), $news->added) : '-'; ?></div>
        </div>
        <div class="form_row">
            <label>Last Modified</label>
            <div class="form_field"><?php echo ($news->modified != 0) ? date(Lib::config('admin.date_format'), $news->modified) : '-'; ?></div>
        </div>
	</div>
	<div class="form_row">
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