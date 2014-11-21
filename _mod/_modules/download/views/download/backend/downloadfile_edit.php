<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu; ?></h2>
<?php echo Form::open(ADMIN.$class_name.'/edit/'.$file->id, array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'general autovalid form_details',
																'id' => ''
																));
// For checking if the type_id is changed
echo Form::hidden('_type_id', $fields['type_id']);
?>	
<div class="form_wrapper">	
	<div class="form_row">
		<label>Title</label>
		<input type="text" name="title" id="title" value="<?php echo $fields['title']; ?>" />
		<?php echo sprintf($errors['title'], 'Title'); ?>
	</div>
	<div class="form_row">
		<label>Name</label>
		<input type="text" name="name" id="name" value="<?php echo $fields['name']; ?>" />
		<?php echo sprintf($errors['name'], 'Name'); ?>
	</div>
	<?php echo sprintf($errors['type_id'], 'Download'); ?>
		<div class="form_row">
			<label>Download</label>
			<select name="type_id" id="type_id" class="required">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['type_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Type ---</option>
				<?php foreach ($downloads as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="type_<?php echo $row->id; ?>" <?php echo ($fields['type_id'] == $row->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($row->subject, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo sprintf($errors['category_id'], 'Category'); ?>
		<div class="form_row">
			<label>Category</label>
			<select name="category_id" id="category_pid" class="required" data-url="<?php echo URL::site(ADMIN.$class_name.'/productlookup');?>">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['category_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Category ---</option>
				<?php foreach ($categories as $category) : ?>
				<option value="<?php echo $category->id; ?>" class="category_<?php echo $category->id; ?>" <?php echo ($fields['category_id'] == $category->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($category->title, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo sprintf($errors['product_id'], 'Product'); ?>
		<div class="form_row">
			<label>Product</label>
			<select name="product_id" id="product_id" class="required">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['product_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Product ---</option>
				<?php foreach ($products as $product) : ?>
				<option value="<?php echo $product->id; ?>" class="category_<?php echo $product->id; ?>" <?php echo ($fields['product_id'] == $product->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($product->subject, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php echo sprintf($errors['description'], 'Description'); ?>
	<div class="form_row">
		<label>Description</label>
		<textarea name="description" id="description" class="ckeditor"><?php echo $fields['description']; ?></textarea>
	</div>
	<?php foreach ($uploads as $row_name => $row_params) : ?>
	<?php echo sprintf($errors[$row_name], $row_params['label']); ?>
	<div class="form_row">
		<label><?php echo $row_params['label']; ?></label>
		<div class="form_fields">
			<?php if (in_array($file->file_type, $readable_mime)) : ?>
			<div id="file_<?php echo $file->id; ?>">
				<?php if (substr($file->file_type, 0, strlen('image/')) == 'image/') : ?>
				<?php
					$file_data	= pathinfo(url::base().Lib::config($this->_module_name.'.upload_url').$file->file_name);
					$thumb_ext	= isset($row_params['image_manipulation']['thumbnails'][0]) ? '_resize_'.$row_params['image_manipulation']['thumbnails'][0] : '';
				?>
				<img src="<?php echo url::base().Lib::config($this->_module_name.'.upload_url').$file_data['filename'].$thumb_ext.'.'.$file_data['extension']; ?>" alt="<?php echo url::base().Lib::config('gallery.upload_url').$file->file_name; ?>" />
				<?php elseif (substr($file->file_type, 0, strlen('audio/')) == 'audio/') : ?>
				<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
					<param name="movie" value="<?php echo ASSETS; ?>flash/singlemp3player.swf" />
					<param name="quality" value="high" />
					<param name="wmode" value="opaque" />
					<param name="scale" value="noscale" />
					<param name="salign" value="lt" />
					<param name="FlashVars" value="file=<?php echo URL::base().$upload_url.$file->file_name; ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
					<param name="swfversion" value="8,0,0,0" />
					<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don'\t want users to see the prompt. -->
					<param name="expressinstall" value="Scripts/expressInstall.swf" />
					<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
					<!--[if !IE]>-->
					<object type="application/x-shockwave-flash" data="<?php echo ASSETS; ?>flash/singlemp3player.swf" width="320" height="20">
					<!--<![endif]-->
						<param name="quality" value="high" />
						<param name="wmode" value="opaque" />
						<param name="scale" value="noscale" />
						<param name="salign" value="lt" />
						<param name="FlashVars" value="file=<?php echo URL::base().$upload_url.$file->file_name; ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
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
					<param name="movie" value="<?php echo ASSETS; ?>flash/FLVPlayer_Progressive.swf" />
					<param name="quality" value="high" />
					<param name="wmode" value="opaque" />
					<param name="scale" value="noscale" />
					<param name="salign" value="lt" />
					<param name="FlashVars" value="skinName=<?php echo ASSETS; ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$file->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
					<param name="swfversion" value="8,0,0,0" />
					<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
					<param name="expressinstall" value="Scripts/expressInstall.swf" />
					<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
					<!--[if !IE]>-->
					<object type="application/x-shockwave-flash" data="<?php echo ASSETS; ?>flash/FLVPlayer_Progressive.swf" width="320" height="240">
					<!--<![endif]-->
						<param name="quality" value="high" />
						<param name="wmode" value="opaque" />
						<param name="scale" value="noscale" />
						<param name="salign" value="lt" />
						<param name="FlashVars" value="skinName=<?php echo ASSETS; ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$file->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
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
		<div class="form_field">
			<!--a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.$file->file_name); ?>">
				<img src="<?php echo IMG; ?>admin/disk.png" alt="<?php echo $file->file_name; ?>" />
			</a-->
			<a class="download" title="Download" href="<?php echo URL::site(ADMIN.$class_name.'/download/'.base64_encode($upload_url.$file->file_name));?>">
				<img src="<?php echo IMG; ?>admin/disk.png" alt="<?php echo $file->file_name; ?>" />
			</a>
		</div>
	</div>
	<fieldset>
		<legend><?php echo ucfirst($row_params['label']); ?></legend>
		<div class="form_row">
			<label>Replace <?php echo $row_params['label']; ?></label>
			<input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
			<?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
			<label class="sub_label"><?php echo HTML::chars($row_params['note'], TRUE); ?></label>
			<?php endif; ?>
		</div>
		<?php if (isset($row_params['caption']) && $row_params['caption']) : ?>
		<div class="form_row">
			<label>Caption</label>
			<input type="text" name="<?php echo $row_name.'_caption'; ?>" id="<?php echo $row_name.'_caption'; ?>" value="<?php echo $fields[$row_name.'_caption']; ?>" />
		</div>
		<?php endif; ?>
	</fieldset>
	<?php endforeach; ?>
	<?php if (isset($show_allow_comment) && $show_allow_comment) : ?>
	<?php echo sprintf($errors['allow_comment'], 'Allow Comment'); ?>
	<div class="form_row">
		<label for="allow_comment">Allow User to Comment?</label>
		<input type="checkbox" name="allow_comment" id="allow_comment" value="1" <?php echo ($fields['allow_comment'] == 1) ? 'checked="checked"' : ''; ?> /> <label for="allow_comment" class="sub_label">Yes, user can comment this content</label>
	</div>
	<?php endif; ?>
	<?php echo sprintf($errors['status'], 'Status'); ?>
	<div class="form_row">
		<label>Status</label>
		<select name="status" id="status">
			<option value="">&nbsp;</option>
			<?php foreach ($statuses as $row) : ?>
			<option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars(ucfirst($row), TRUE); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="form_row">
		<label>Created</label>
		<div class="form_field"><?php echo ($file->added != 0) ? date(Lib::config('site.date_format'), $file->added) : '-'; ?></div>
	</div>
	<div class="form_row">
		<label>Last Modified</label>
		<div class="form_field"><?php echo ($file->modified != 0) ? date(Lib::config('site.date_format'), $file->modified) : '-'; ?></div>
	</div>
</div>	
	<div class="form_row">
		<label>&nbsp;</label>
		<input type="submit" value="" class="btn_save" />
	</div>
<?php echo Form::close();?>
