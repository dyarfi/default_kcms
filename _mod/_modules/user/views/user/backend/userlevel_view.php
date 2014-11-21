<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu;?></h2>
<div class="bar"></div>
<?php echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$levels->id), array(
													'enctype' => 'multipart/form-data', 
													'method' => 'get', 
													'class' => 'general autovalid',
													'id' => 'edit-users'
												));	
?>
	<div class="form_row">
		<label>Name</label>
		<div class="form_field"><?php echo ucwords($levels->name); ?></div>
	</div>
	<div class="form_row">
		<label>Backend Access</label>
		<div class="form_field"><?php echo ($levels->backend_access) ? 'Yes' : 'No'; ?></div>
	</div>
	<div class="form_row">
		<label>Full Backend Access</label>
		<div class="form_field"><?php echo ($levels->full_backend_access) ? 'Yes' : 'No'; ?></div>
	</div>
	<div class="form_row">
		<label>Status</label>
		<div class="form_field"><?php echo ucwords($levels->status); ?></div>
	</div>
	<div class="form_row">
		<label>Created</label>
		<div class="form_field"><?php echo ($levels->added != 0) ? date(Lib::config('admin.date_format'), $levels->added) : '-'; ?></div>
	</div>
	<div class="form_row">
		<label>Last Modified</label>
		<div class="form_field"><?php echo ($levels->modified != 0) ? date(Lib::config('admin.date_format'), $levels->modified) : '-'; ?></div>
	</div>
	<div class="ls12 clear"></div>
	<div class="bar"></div>
	<div class="ls12"></div>
	<?php echo Form::submit('','Edit', array('class'=>'btn btn-primary span2')); ?>
<?php echo Form::close();?>
