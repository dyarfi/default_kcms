<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu; ?></h2>

<div class="ls10"></div>
<div class="form_wrapper">
	<form class="" action="<?php echo URL::site(ADMIN.$class_name.'/update/'.$profile->user_id); ?>" method="get">
		<div class="form_row">
			<fieldset>
				<legend>Login Information</legend>

				<div class="form_row">
					<label>E-mail :</label>
					<strong><?php echo $user->email; ?></strong>
				</div>

				<div class="form_row">
					<label>User Name :</label>
					<strong><?php echo $user->name; ?></strong>
				</div>

				<div class="form_row">
					<label>Level :</label>
					<strong><?php echo $level->name; ?></strong>
				</div>
			</fieldset>

			<fieldset>
				<legend>User Information</legend>

				<!--div class="form_row">
					<label>Photo</label>
					<div class="form_field">
						<?php
						if (is_file(Lib::config('member.upload_path').$profile->file_name)) :
							$file_data	= pathinfo(Lib::config('member.upload_path').$profile->file_name);

							$file_name	= url::base().Lib::config('member.upload_url').$file_data['filename'].'_crop_'.Lib::config('user_profile.image.crop.0.0').'.'.$file_data['extension'];
						else :
							$file_name	= url::base().'images/profile-no-image.png';
						endif;
						?>
						<img src="<?php echo $file_name; ?>" alt="Photo" />
					</div>
				</div-->

				<div class="form_row">
					<label>Gender :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->gender)) ? ucfirst($profile->gender) : '-'; ?></strong></div>
				</div>
				
				<div class="form_row">
					<label>About :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->about)) ? ucfirst($profile->about) : '-'; ?></strong></div>
				</div>
				
				<div class="form_row">
					<label>Country :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->country)) ? ucfirst($profile->country) : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>State :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->state)) ? ucfirst($profile->state) : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>City :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->city)) ? ucfirst($profile->city) : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Address :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->address)) ? ucfirst($profile->address) : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Postal Code :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->postal_code)) ? $profile->postal_code : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Birthday :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->birthday)) ? $profile->birthday : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Phone :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->phone)) ? $profile->phone : '-'; ?></strong></div>
				</div>
				
				<div class="form_row">
					<label>Church :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->church)) ? $profile->church : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Fax :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->fax)) ? $profile->fax : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Website :</label>
					<div class="form_field"><strong><?php echo (!empty($profile->website)) ? $profile->website : '-'; ?></strong></div>
				</div>
				
				<div class="form_row">
					<label>Created</label>
					<div class="form_field"><strong><?php echo ($profile->added != 0) ? date(Lib::config('admin.date_format'), $profile->added) : '-'; ?></strong></div>
				</div>

				<div class="form_row">
					<label>Last Modified</label>
					<div class="form_field"><strong><?php echo ($profile->modified != 0) ? date(Lib::config('admin.date_format'), $profile->modified) : '-'; ?></strong></div>
				</div>
				
			</fieldset>
		</div>
	</form>
</div>	
<div class="form_row">
	<label>&nbsp;</label>
	<a href="<?php echo Kohana::$base_url.ADMIN.$class_name.'/edit/'.$user->id;?>">
		<img src="<?php echo IMG; ?>admin/edit_button.png"/>
	</a>	
</div>
