<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<form class="form_details" action="<?php echo URL::site(ADMIN . $class_name .'/edit/'.$career->id); ?>" method="get">
	<div class="form_wrapper">

	<fieldset><legend>Personal Information</legend>
        <div class="form_row">
            <label>Name</label>
            <div class="form_fields"><?php echo $career->name; ?></div>
        </div>
    
		<div class="form_row">
            <label>Email</label>
            <div class="form_fields"><?php echo HTML::mailto(strip_tags($career->email)); ?></div>
        </div>
		
		<div class="form_row">
            <label>Gender</label>
            <div class="form_fields"><?php echo Lib::config('career.gender.'.$career->gender); ?></div>
        </div>
    
        <div class="form_row">
            <label>Marital Status</label>
            <!--div class="form_fields"><?php //echo $this->_reverse_date($career->start_date); ?></div-->
			<div class="form_fields"><?php echo Lib::config('career.marital_status.'.$career->marital_status); ?></div>
        </div>
		
		<div class="form_row">
            <label>Id Number</label>
            <div class="form_fields"><?php echo ($career->id_number) ? $career->id_number : '-'; ?></div>
        </div>
		
        <div class="form_row">
            <label>Phone</label>
            <div class="form_fields"><?php echo ($career->phone) ? $career->phone : '-'; ?></div>
        </div>
            
		<div class="form_row">
            <label>Address</label>
            <div class="form_field"><?php echo ($career->address) ? $career->address : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>Birth Date</label>
			<div class="form_fields"><?php echo !empty($career->birth_date) ? $career->birth_date : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>Birth Place</label>
            <!--div class="form_fields"><?php //echo $this->_reverse_date($career->start_date); ?></div-->
			<div class="form_fields"><?php echo ($career->birth_place) ? $career->birth_place : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>CV</label>
            <div class="form_fields">
				<?php if(!empty($career->cv_file)):?>
					<a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/download/'.$career->cv_file); ?>" target="_blank">
						<?php echo HTML::image(URL::base().'images/admin/icon/disk.png'); ?>
					</a>
				<?php else: ?>
					No CV
				<?php endif; ?>
			</div>
        </div>
		
        <div class="form_row">
            <label>Photo</label>		
			<?php if (!empty($career->photo) && is_file(Lib::config('career.upload_path_cv'). $career->photo)):?>
				<a href="#file_<?php echo $career->id; ?>" class="zoom">
					<img src="<?php echo URL::base(); ?>images/cms/icon/picture.png" alt="picture.png" />
				</a>	
				<div id="file_<?php echo $career->id; ?>" class="hide">
					<img src="<?php echo URL::site().Lib::config('career.upload_url_cv').$career->photo;?>" alt="<?php echo $career->photo;?>"/>
				</div>	
			<?php else: ?>
					No Photo
			<?php endif; ?>
		    <!--div class="form_fields"><?php echo ucfirst($career->status); ?></div-->
        </div>		
	</fieldset>
		
	<fieldset><legend>Educational Information</legend>		
        <div class="form_row">
            <label>Education Major</label>
            <div class="form_fields"><?php echo ($career->education_major) ? $career->education_major : '-'; ?></div>
		</div>
			
		<div class="form_row">	
			<label>Education Grade</label>
            <div class="form_fields"><?php echo Lib::config('career.grade.'.$career->education_grade); ?></div>
		</div>
		
		<div class="form_row">
			<label>Education Name</label>
            <div class="form_fields"><?php echo ($career->education_name) ? $career->education_name : ''; ?></div>			
		</div>
		
		<div class="form_row">
            <label>From</label>
            <div class="form_fields">
			<?php 
				echo !empty($career->education_from) ? $career->education_from : '-'; 
			?>
			</div>
		</div>
		
		<div class="form_row">
			<label>To</label>
            <div class="form_fields">
			<?php 
				echo !empty($career->education_to) ? $career->education_to : '-'; 
			?></div>
		</div>	
		</fieldset>
		
		<fieldset><legend>Employment Information / Working References</legend>					
		<div class="form_row">
			<label>Company Name</label>
            <div class="form_fields"><?php echo ($career->employment_name) ? $career->employment_name : '-'; ?></div>			
		</div>
		
		<div class="form_row">	
			<label>Employment Position</label>
            <div class="form_fields"><?php echo ($career->employment_position) ? $career->employment_position : '-'; ?></div>
		</div>
		
		<div class="form_row">
            <label>From</label>
            <div class="form_fields">
			<?php 
				echo !empty($career->employment_from) ? $career->employment_from : '-'; 
			?>
			</div>
		</div>
		
		<div class="form_row">
			<label>To</label>
            <div class="form_fields">
			<?php 
				echo !empty($career->employment_to) ? $career->employment_to : '-'; 
			?>
			</div>
		</div>	
	</fieldset>
		
	<fieldset><legend>Requirement Info</legend>
		<div class="form_row">
            <label>Availability date to start</label>
            <div class="form_field">
				<?php echo is_integer($career->available_date) ? Lib::config('career.available_date.'.$career->available_date) : $career->available_date ?></div>
        </div>
		
		<div class="form_row">
            <label>Will to Located</label>
            <div class="form_field"><?php echo Lib::config('career.yesno.'.$career->is_located); ?></div>
        </div>
		
		<div class="form_row">
            <label>Family Related Employee</label>
            <div class="form_field"><?php echo Lib::config('career.yesno.'.$career->is_related); ?></div>
        </div>
    
		<?php if (!empty($career->messages)):?>
		<div class="form_row">
            <label>Messages</label>
            <div class="form_field"><?php echo ($career->messages) ? $career->messages : '-'; ?></div>
        </div>
		<?php endif; ?>
				
        <div class="form_row">
            <label>Status</label>
            <div class="form_fields"><?php echo ucfirst($career->status); ?></div>
        </div>
		
        <div class="form_row">
            <label>Created</label>
            <div class="form_field"><?php echo ($career->added != 0) ? date(Lib::config('admin.date_format'), $career->added) : '-'; ?></div>
        </div>
    </fieldset>
        <!--div class="form_row">
            <label>Last Modified</label>
            <div class="form_field"><?php echo ($career->modified != 0) ? date(Lib::config('admin.date_format'), $career->modified) : '-'; ?></div>
        </div-->
		
	</div>
	<!--div class="form_row">
		<label>&nbsp;</label>
		<input type="submit" class="btn_edit" value="" />
	</div-->
</form>
