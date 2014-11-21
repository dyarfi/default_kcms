<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<?php 
echo Form::open(URL::site(ADMIN.$class_name.'/add'), array(
																	'enctype' => 'multipart/form-data', 
																	'method' => 'post', 
																	'class' => 'general autovalid form_details',
																)); ?> 
	<div class="form_wrapper">
		<?php echo sprintf($errors['name'], 'User Name'); ?>
        <div class="form_row">
			<?php echo Form::label('name','User Name'); ?>
			<?php echo Form::input('name', $fields['name'], array('class'=>'required','id'=>'fullname'));?>
        </div>
		
        <?php echo sprintf($errors['email'], 'E-Mail'); ?>
        <div class="form_row">
			<?php echo Form::label('email','E-mail'); ?>			
			<?php echo Form::input('email', $fields['email'], array('class'=>'required email','id'=>'email'));?>
        </div>
		
        <?php echo sprintf($errors['password'], 'New Password'); ?>
        <div class="form_row">
			<?php echo Form::label('password','Password'); ?>
			<?php echo Form::password('password', $fields['password'], array('class'=>'required password','id'=>'password'));?>
        </div>
		
		<?php echo sprintf($errors['confirm_password'], 'Confirm Password'); ?>
        <div class="form_row">
            <?php echo Form::label('confirm_password','Confirm Password'); ?>
			<?php echo Form::password('confirm_password', $fields['confirm_password'], array('class'=>'required','id'=>'confirm_password'));?>
        </div>
		
		<?php if (isset($show_levels) && $show_levels) : ?>
        <?php echo sprintf($errors['level_id'], 'Level'); ?>
        <div class="form_row">
			<?php echo Form::label('level_id','Level'); ?>	
            <select name="level_id" id="level_id" class="required">
                <option value="">&nbsp;</option>
                <?php foreach ($levels as $row) : ?>
                <option value="<?php echo $row->id; ?>" <?php echo ($fields['level_id'] == $row->id) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars(ucfirst($row->name), TRUE); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php 
		else : 
			echo Form::hidden('level_id',  '99', array('id'=>'level_id')); 
		endif; 
		?>
		
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
				
				<?php echo sprintf($errors['church'], 'Church'); ?>
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
    </div>    
	<div class="form_row">
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>
