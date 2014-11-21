<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<form class="form_details" action="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/update/'.$id); ?>" method="post" enctype="multipart/form-data">
	<div class="form_row">
		<fieldset>
			<legend>Login Information</legend>

			<div class="form_row">
				<label>E-mail</label>
				<b><?php echo $user->email; ?></b>
			</div>

			<div class="form_row">
				<label>User Name</label>
				<b><?php echo $user->name; ?></b>
			</div>

			<div class="form_row">
				<label>Level</label>
				<b><?php echo $level->name; ?></b>
			</div>
		</fieldset>
	</div>

	<div class="form_row">
		<fieldset>
			<legend>User Information</legend>

			<!--div class="form_row">
				<label>Photo</label>
				<div class="form_field">
					<?php
					if (is_file(Lib::config('member.upload_path').@$profile->file_name)) :
						$file_data	= pathinfo(Lib::config('member.upload_path').@$profile->file_name);

						$file_name	= url::base().Lib::config('member.upload_url').$file_data['filename'].'_crop_'.Lib::config('member.image.crop.0.0').'.'.$file_data['extension'];
					else :
						$file_name	= url::base().'images/profile-no-image.png';
					endif;
					?>
					<img src="<?php echo $file_name; ?>" alt="Photo" />
				</div>
			</div-->

			<?php if (@$profile->file_name != '') : ?>
			<?php //echo sprintf($errors['photo'], 'Uploaded Photo'); ?>
			<!--div class="form_row">
				<label>Delete Photo</label>
				<input type="checkbox" name="delete_photo" id="delete_photo" value="1" />
			</div-->
			<?php endif; ?>

			<?php //echo sprintf($errors['photo'], 'Uploaded Photo'); ?>
			<!--div class="form_row">
				<label>Upload Photo</label>
				<input type="file" name="photo" id="photo" />
			</div-->

			<?php echo sprintf($errors['name'], 'Name'); ?>
			<div class="form_row">
				<label>Full Name</label>
				<input type="text" name="name" id="name" value="<?php echo $fields['name']; ?>" />
			</div>

			<?php echo sprintf($errors['country'], 'Country'); ?>
			<div class="form_row">
				<label>Country</label>
				<input type="text" name="country" id="country" value="<?php echo $fields['country']; ?>" />
			</div>

			<?php echo sprintf($errors['state'], 'State'); ?>
			<div class="form_row">
				<label>State</label>
				<input type="text" name="state" id="state" value="<?php echo $fields['state']; ?>" />
			</div>

			<?php echo sprintf($errors['city'], 'City'); ?>
			<div class="form_row">
				<label>City</label>
				<input type="text" name="city" id="city" value="<?php echo $fields['city']; ?>" />
			</div>

			<?php echo sprintf($errors['address'], 'Address'); ?>
			<div class="form_row">
				<label>Address</label>
				<input type="text" name="address" id="address" value="<?php echo $fields['address']; ?>"/>
			</div>

			<?php echo sprintf($errors['postal_code'], 'Postal Code'); ?>
			<div class="form_row">
				<label>Postal Code</label>
				<input type="text" name="postal_code" id="postal_code" value="<?php echo $fields['postal_code']; ?>" />
			</div>

			<?php echo sprintf($errors['birthday'], 'Birthday'); ?>
			<div class="form_row">
				<label>Birthday</label>
				<input type="text" name="birthday" id="birthday" value="<?php echo $fields['birthday']; ?>" class="datepicker"/>
			</div>

			<?php echo sprintf($errors['phone'], 'Phone'); ?>
			<div class="form_row">
				<label>Phone</label>
				<input type="text" name="phone" id="phone" value="<?php echo $fields['phone']; ?>" />
			</div>

			<?php echo sprintf($errors['fax'], 'Fax'); ?>
			<div class="form_row">
				<label>Fax</label>
				<input type="text" name="fax" id="fax" value="<?php echo $fields['fax']; ?>" />
			</div>

			<?php echo sprintf($errors['website'], 'Website'); ?>
			<div class="form_row">
				<label>Website</label>
				<input type="text" name="website" id="website" value="<?php echo $fields['website']; ?>" />
			</div>
		</fieldset>
	</div>

	<div class="form_row">
		<label>&nbsp;</label>
		<input type="submit" value="Save" />
	</div>
</form>
