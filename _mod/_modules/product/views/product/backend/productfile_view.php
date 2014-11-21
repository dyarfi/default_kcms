<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>

<?php echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$file->id), array(
													'enctype' => 'multipart/form-data', 
													'method' => 'get', 
													'class' => 'general autovalid',
													'id' => 'edit-gallery'
												));	
?>	
<div class="form_wrapper">
	<div class="form_row">
		<label>Name</label>
		<div class="form_field"><?php echo $file->name; ?></div>
	</div>
	<div class="form_row">
		<label>Product</label>
		<div class="form_field"><?php echo $product; ?></div>
	</div>
	<div class="form_row">
		<label>Title</label>
		<div class="form_field">
			<?php echo HTML::chars($file->title, TRUE); ?>
		</div>
	</div>
	<div class="form_row">
		<label>Description</label>
		<div class="form_field"><?php echo ($file->description != '') ? $file->description : '-'; ?></div>
	</div>

	<?php foreach ($uploads as $row_name => $row_params) : ?>
	<div class="form_row">
		<label><?php echo $row_params['label']; ?></label>

		<div class="form_fields">
			<?php if (in_array($file->file_type, $readable_mime)) : ?>
			<div id="file_<?php echo $file->id; ?>">
				<?php if (substr($file->file_type, 0, strlen('image/')) == 'image/') : ?>
				<?php
					$file_data	= pathinfo(URL::site() . $upload_url . $file->file_name);
					$thumb_ext	= isset($row_params['image_manipulation']['thumbnails'][0]) ? '_resize_'.$row_params['image_manipulation']['thumbnails'][0] : '';
				?>
				<img src="<?php echo URL::site().$upload_url.$file_data['filename'].$thumb_ext.'.'.$file_data['extension']; ?>" alt="<?php echo URL::site().$upload_url.$file->file_name; ?>" />
				<?php elseif (substr($file->file_type, 0, strlen('audio/')) == 'audio/') : ?>
				<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
					<param name="movie" value="<?php echo ASSETS; ?>flash/singlemp3player.swf" />

					<param name="quality" value="high" />
					<param name="wmode" value="opaque" />
					<param name="scale" value="noscale" />
					<param name="salign" value="lt" />
					<param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$file->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
					<param name="swfversion" value="8,0,0,0" />
					<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
					<param name="expressinstall" value="Scripts/expressInstall.swf" />
					<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->

					<!--[if !IE]>-->
					<object type="application/x-shockwave-flash" data="<?php echo ASSETS; ?>flash/singlemp3player.swf" width="320" height="20">
					<!--<![endif]-->
						<param name="quality" value="high" />
						<param name="wmode" value="opaque" />
						<param name="scale" value="noscale" />
						<param name="salign" value="lt" />
						<param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$file->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
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
						<param name="FlashVars" value="skinName=<?php echo ASSETS; ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.ASSETS.'/download/'.$file->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
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

		<?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
		<label class="sub_label"><?php echo HTML::chars($row_params['note'], TRUE); ?></label>
		<?php endif; ?>
	</div>

	<div class="form_row">
		<label class="no_border">&nbsp;</label>
		<div class="form_field">
			<a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.base64_encode($upload_url . $file->file_name)); ?>">
				<img src="<?php echo IMG; ?>admin/disk.png" alt="<?php echo $file->file_name; ?>" />
			</a>
		</div>
	</div>

	<?php if (isset($row_params['caption']) && $row_params['caption']) : ?>
	<div class="form_row">
		<label>Caption</label>
		<div class="form_field"><?php echo HTML::chars($file->caption, TRUE); ?></div>
	</div>
	<?php endif; ?>

	<?php endforeach; ?>

	<?php if (isset($show_allow_comment) && $show_allow_comment) : ?>
	<div class="form_row">
		<label for="allow_comment">Allow User to Comment?</label>
		<div class="form_field"><?php echo ($file->allow_comment != 0) ? 'Yes' : 'No'; ?></div>
	</div>
	<?php endif; ?>

	<div class="form_row">
		<label>Status</label>
		<div class="form_field"><?php echo ucfirst($file->status); ?></div>
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
		<?php echo Form::submit(NULL, 'Edit', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>