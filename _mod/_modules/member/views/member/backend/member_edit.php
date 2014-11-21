<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>

<?php echo Form::open(ADMIN.$class_name.'/edit/'.$user->id, array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'form_details general autovalid',
																'id' => 'edit-users'
																));	
?>
	<div class="form_row">
		<fieldset>
			<legend>User Login Information</legend>

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
			<legend>User Profile Information</legend>

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
			
			<?php echo sprintf($errors['gender'], 'Gender'); ?>
			<div class="form_row">
				<?php echo Form::label('gender','Gender'); ?>
				<?php echo Form::select('gender', $genders, empty($fields['gender']) ? $fields['gender'] : $fields['gender']); ?>
			</div>

			<?php echo sprintf($errors['about'], 'About'); ?>
			<div class="form_row">
				<?php echo Form::label('about','About'); ?>
				<?php echo Form::textarea('about', $fields['about'], array('class'=>'','id'=>'about'),TRUE);?>	
			</div>

			<?php echo sprintf($errors['country'], 'Country'); ?>
			<div class="form_row">
				<?php echo Form::label('country','Country'); ?>
				<?php echo Form::input('country', $fields['country'], array('class'=>'','id'=>'country'));?>	
			</div>

			<?php echo sprintf($errors['state'], 'State'); ?>
			<div class="form_row">
				<?php echo Form::label('state','State'); ?>					
				<?php echo Form::input('state', $fields['state'], array('class'=>'','id'=>'state'));?>				
			</div>

			<?php echo sprintf($errors['city'], 'City'); ?>
			<div class="form_row">
				<?php echo Form::label('city','City'); ?>	
				<?php echo Form::input('city', $fields['city'], array('class'=>'','id'=>'city'));?>						
			</div>

			<?php echo sprintf($errors['address'], 'Address'); ?>
			<div class="form_row">
				<?php echo Form::label('address','Address'); ?>
				<?php echo Form::input('address', $fields['address'], array('class'=>'','id'=>'address'));?>
			</div>

			<?php echo sprintf($errors['postal_code'], 'Postal Code'); ?>
			<div class="form_row">
				<?php echo Form::label('postal_code','Postal Code'); ?>
				<?php echo Form::input('postal_code', $fields['postal_code'], array('class'=>'','id'=>'postal_code'));?>
			</div>

			<?php echo sprintf($errors['church'], 'Postal Code'); ?>
			<div class="form_row">
				<?php echo Form::label('church','Church'); ?>
				<?php echo Form::input('church', $fields['church'], array('class'=>'','id'=>'church'));?>
			</div>
			
			<?php echo sprintf($errors['birthday'], 'Birthday'); ?>
			<div class="form_row">
				<?php echo Form::label('birthday','Birthday'); ?>
				<?php echo Form::input('birthday', $fields['birthday'], array('class'=>'required simpledate','id'=>'birthday'));?>				
			</div>

			<?php echo sprintf($errors['phone'], 'Phone'); ?>
			<div class="form_row">
				<?php echo Form::label('phone','Phone'); ?>	
				<?php echo Form::input('phone', $fields['phone'], array('class'=>'required','id'=>'phone'));?>						
			</div>

			<?php echo sprintf($errors['fax'], 'Fax'); ?>
			<div class="form_row">
				<?php echo Form::label('fax','Fax'); ?>	
				<?php echo Form::input('fax', $fields['fax'], array('class'=>'','id'=>'fax'));?>
			</div>

			<?php echo sprintf($errors['website'], 'Website'); ?>
			<div class="form_row">
				<?php echo Form::label('website','Website'); ?>						
				<?php echo Form::input('website', $fields['website'], array('class'=>'','id'=>'website'));?>
			</div>
		</fieldset>
	</div>

	<div class="form_row">
		<label>&nbsp;</label>
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
	
<?php echo Form::close();?>
