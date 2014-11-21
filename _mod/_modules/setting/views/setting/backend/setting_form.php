<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script>
    $(document).ready(function(){
       $("#alias").counter({
            type: 'char',
            goal: 512,
            count: 'down'            
        });  
    });
</script>
<h2><?php echo $module_menu;?></h2>
<div class="bar"></div>
		<?php 
			echo Form::open(ADMIN.$class_name.'/'.$action.'/'.$param, array(
															'enctype' => 'multipart/form-data',
															'method' => 'post', 
															'class' => 'general autovalid',
															'id' => 'create-news'
														));
			//echo Form::hidden('id',$fields['id']);
		?>
        <div class="ls6"></div>
        <span>Alias</span>
        <div class="ls3"></div>
		<?php echo Form::input('alias', $fields['alias'], array('class'=>'required', 'id' => 'alias')); ?>
		<div class="ls6"></div>
        <span>Parameter</span>
        <div class="ls3"></div>
		<?php 
		$readonly = (ACL::instance()->user->id == 1) ? array() : array('readonly'=>'readonly');
		echo Form::input('parameter', $fields['parameter'], 
				array('class'=>'required', 'id' => 'value', $readonly)); 
		?>
        <div class="ls6"></div>
        <span>Value</span>
        <div class="ls3"></div>
		<?php echo Form::textarea('value', $fields['value'], array('class'=>'required ckeditorsmall', 'id' => 'value')); ?>
		<div class="ls6"></div>
        <span><label for="is_system">System</label></span>
        <div class="ls3"></div>
		<?php 			 
		$is_system = $fields['is_system'];
		$modes = Lib::config('setting.is_system');
		foreach ($modes as $mode => $value) {
			echo Form::radio('is_system',$mode,($is_system == $mode) ? TRUE : FALSE,array('class'=>"is_system_mode"));
			echo '&nbsp;&nbsp;';
			echo $value;
			echo '&nbsp;&nbsp;';
		}
		?>
        <div class="ls6"></div>
        <?php 
			foreach ($statuses as $status) {
				$arr_status[$status] = ucfirst($status);
			}			
			//print_r($arr_status);
			echo Form::select('status', $arr_status, $fields['status']); 
		?>
		<div class="clear ls20"></div>
		<div class="bar"></div>
		<div class="ls10"></div>	
		<div class="ls10"></div>  
	<?php echo Form::submit('submit', 'SAVE', array('class' => 'btn btn-sm btn-primary')); ?>
    <?php echo Form::close();?>
