<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>
<div class="ls5"></div>
<div class="bar"></div>
<div class="ls10"></div>

<?php if (!isset($setting)) : ?>
    <div class="warning3"><?php echo i18n::get('error_no_data'); ?></div>
<?php else: ?>    
<?php echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$setting->id), array(
															'enctype' => 'multipart/form-data', 
															'method' => 'get', 
															'class' => 'general autovalid',
															'id' => 'edit-users'
															));	
?>
	<div class="cd_left"><label>Alias</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo $setting->alias; ?></div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>Parameter</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo $setting->parameter; ?></div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>Value</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo $setting->value; ?></div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>Status</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo ucfirst($setting->status); ?></div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>System</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right">
	<?php 
		$system = Lib::config('setting.is_system');
		echo ucfirst($system[$setting->is_system]); 
	?>
	</div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>Created</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo ($setting->added != 0) ? date(Lib::config('admin.date_format'), $setting->added) : '-'; ?></div>
	<div class="clear ls4"></div>

	<div class="cd_left"><label>Last Modified</label></div>
	<div class="cd_center">:</div>
	<div class="cd_right"><?php echo ($setting->modified != 0) ? date(Lib::config('admin.date_format'), $setting->modified) : '-'; ?></div>
	<div class="clear ls4"></div>

	<div class="ls5"></div>
	<div class="bar"></div>
	<div class="ls15"></div>
	
	<?php echo Form::submit('','Edit',array('class'=>'btn btn-primary span2')); ?>
<?php echo Form::close();?>
<?php endif;?>