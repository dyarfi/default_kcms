<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<?php 
echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$portfolio->id), array(
													'enctype' => 'multipart/form-data', 
													'method' => 'post', 
													'class' => 'general autovalid form_details',
													'id' => 'edit-portfolio'
												));	
echo Form::hidden('order', $portfolio->order);

?>	
	<div class="form_wrapper">	
	
		<?php echo sprintf($errors['subject'], 'Subject'); ?>
		<div class="form_row">
			<label>Subject</label>
			<input type="text" name="subject" id="subject" class="required" value="<?php echo $fields['subject']; ?>" />
		</div>
		
		<?php if (isset($show_parent) && $show_parent) : ?>
		<?php echo sprintf($errors['parent_id'], 'Parent'); ?>
		<div class="form_row">
			<label>Parent</label>
			<select name="parent_id" id="parent_id" class="required">
				<option value="">&nbsp;</option>
				<option value="0" <?php echo ($fields['parent_id'] == 0) ? 'selected="selected"' : ''; ?>>This <?php echo str_replace('_', ' ', $class_name); ?> is parent</option>
				<?php foreach ($portfolios as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="parent_<?php echo $row->parent_id; ?>" <?php echo ($row->id == $portfolio->id) ? 'disabled="disabled"' : ''; ?> <?php echo ($fields['parent_id'] == $row->id) ? 'selected="selected"' : ''; ?>><?php echo str_repeat('&nbsp;', ($row->sub_level + 1) * 5).text::limit_words(HTML::chars($row->subject, TRUE),8); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>
		
		<?php echo sprintf($errors['name'], 'Name'); ?>
		<div class="form_row">
			<label>Name</label>
			<input type="text" name="name" id="name" value="<?php echo $fields['name']; ?>" class="required" />
		</div>
		
		<?php if (isset($show_description) && $show_description) : ?>
		<?php echo sprintf($errors['description'], 'Description'); ?>
		<div class="form_row">
			<label>Description</label>
			<textarea name="description" id="description" class="ckeditor required"><?php echo $fields['description']; ?></textarea>
		</div>
		<?php endif; ?>

		<?php if (isset($show_upload) && $show_upload) : ?>
        <?php foreach ($uploads as $row_name => $row_params) : ?>
            <fieldset style="clear:both;">
                <legend><?php echo $row_params['label']; ?></legend>
                <?php if (isset($files[$row_name])) : ?>
                <div class="form_row">
                    <!--label><?php echo $row_params['label']; ?></label-->
                    <div class="form_fields">
                        <?php if (is_file($upload_path.$files[$row_name]->file_name) && in_array($files[$row_name]->file_type, $readable_mime)) : ?>
                        <div id="file_<?php echo $files[$row_name]->id; ?>">
                            <?php if (substr($files[$row_name]->file_type, 0, strlen('image/')) == 'image/') : ?>
                            <?php
                                $file_data	= pathinfo(URL::site().$upload_url.$files[$row_name]->file_name);
                                $thumb_ext	= isset($row_params['image_manipulation']['thumbnails'][0]) ? '_resize_'.$row_params['image_manipulation']['thumbnails'][0] : '';
                            ?>
                            <img src="<?php echo URL::site().$upload_url.$file_data['filename'].$thumb_ext.'.'.$file_data['extension']; ?>" alt="<?php echo URL::site().$upload_url.$files[$row_name]->file_name; ?>" />
                            <?php elseif (substr($files[$row_name]->file_type, 0, strlen('audio/')) == 'audio/') : ?>
                            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
                                <param name="movie" value="<?php echo URL::site(); ?>flash/singlemp3player.swf" />
            
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
                                <object type="application/x-shockwave-flash" data="<?php echo URL::site(); ?>flash/singlemp3player.swf" width="320" height="20">
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
                                <param name="movie" value="<?php echo URL::site(); ?>flash/FLVPlayer_Progressive.swf" />
            
                                <param name="quality" value="high" />
                                <param name="wmode" value="opaque" />
                                <param name="scale" value="noscale" />
                                <param name="salign" value="lt" />
                                <param name="FlashVars" value="skinName=<?php echo URL::site(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
                                <param name="swfversion" value="8,0,0,0" />
                                <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
                                <param name="expressinstall" value="Scripts/expressInstall.swf" />
                                <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
            
                                <!--[if !IE]>-->
                                <object type="application/x-shockwave-flash" data="<?php echo URL::site(); ?>flash/FLVPlayer_Progressive.swf" width="320" height="240">
                                <!--<![endif]-->
                                    <param name="quality" value="high" />
                                    <param name="wmode" value="opaque" />
                                    <param name="scale" value="noscale" />
                                    <param name="salign" value="lt" />
                                    <param name="FlashVars" value="skinName=<?php echo URL::site(); ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
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
                    <div class="form_field"><a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.$files[$row_name]->file_name); ?>"><img src="<?php echo IMG; ?>admin/disk.png" alt="<?php echo $files[$row_name]->file_name; ?>" /></a></div>
                </div>
            
                <?php echo sprintf($errors[$row_name], $row_params['label']); ?>
                <div class="form_row">
                    <label>Replace <?php echo $row_params['label']; ?></label>
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <small class="text-info"><?php echo HTML::chars($row_params['note'], TRUE); ?></small>
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
                    <!--label><?php echo $row_params['label']; ?></label-->
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <small class="text-info"><?php echo HTML::chars($row_params['note'], TRUE); ?></small>
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
		
		<?php if (isset($show_owner) && $show_owner) : ?>
		<?php echo sprintf($errors['user_id'], 'Owner'); ?>
		<div class="form_row">
			<label>Owner</label>
			<select name="user_id" id="user_id">
				<option value="">&nbsp;</option>
				<option value="0" <?php echo ($fields['user_id'] == 0) ? 'selected="selected"' : ''; ?>>System</option>
				<?php foreach ($users as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="user_<?php echo $row->id; ?>" <?php echo ($fields['user_id'] == $row->id) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars($row->name.' ('.$row->email.')', TRUE); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>
		
		<?php if (isset($show_order) && $show_order) : ?>
		<?php echo sprintf($errors['order'], 'Order'); ?>
			<div class="form_row">
				<label>Order</label>
				<select name="order" id="order">
					<option value="">&nbsp;</option>
					<option value="1" <?php echo ($fields['order'] == 1) ? 'selected="selected"' : ''; ?>>At the beginning</option>
					<?php if (count($portfolios) != 0) : ?>
					<optgroup label="After ...">
						<?php foreach ($orders as $row) : ?>
						<option value="<?php echo ($row->order + 1); ?>" <?php echo ($fields['order'] == ($row->order + 1)) ? 'selected="selected"' : ''; echo ($row->id == $portfolio->id) ? ' disabled="disabled"' : ''; ?>><?php echo HTML::chars($row->title, TRUE); ?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php endif; ?>
				</select>
			</div>
		<?php endif; ?>
		
		<?php echo sprintf($errors['status'], 'Status'); ?>
		<div class="form_row">
			<label>Status</label>
			<select name="status" id="status" class="required">
				<option value="">&nbsp;</option>
				<?php foreach ($statuses as $row) : ?>
				<option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars(ucfirst($row), TRUE); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form_row">
			<label>Created</label>
			<div class="form_field"><?php echo ($portfolio->added != 0) ? date(Lib::config('site.date_format'), $portfolio->added) : '-'; ?></div>
		</div>
		<div class="form_row">
			<label>Last Modified</label>
			<div class="form_field"><?php echo ($portfolio->modified != 0) ? date(Lib::config('site.date_format'), $portfolio->modified) : '-'; ?></div>
		</div>
	</div>	
    <div class="form_row">
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>
