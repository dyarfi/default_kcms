<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>

<div class="ls10 clear"></div>
<div class="bar"></div>
<div class="ls10"></div>

<?php echo Form::open(URL::site(ADMIN . $class_name.'/edit/'.$listings->id), array(
													'enctype' => 'multipart/form-data', 
													'method' => 'get', 
													'class' => 'general autovalid',
													'id' => 'edit-users'
												));	
?>
		
		<fieldset>
			<legend>Url Information</legend>
			<?php if (!empty($listings)):?>
			<div class="clear ls4"></div>
			<div class="cd_left"><label>Keywords</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->keywords) ? HTML::anchor(URL::site() . $listings->keywords, URL::site().$listings->keywords,array('target'=>'_blank','title'=>URL::site() . $listings->keywords)) : '-'; ?></div>
			<div class="clear ls4"></div>

			<div class="cd_left"><label>Url</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->url) ? $listings->url : '-'; ?></div>
			<div class="clear ls4"></div>

			<div class="cd_left"><label>Title</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->title) ? $listings->title : '-'; ?></div>
			<div class="clear ls4"></div>

			<div class="cd_left"><label>Timestamp</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->timestamp) ? ucfirst($listings->timestamp) : '-'; ?></div>
			<div class="clear ls4"></div>
			
			<div class="cd_left"><label>Ip</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->ip) ? ucfirst($listings->ip) : '-'; ?></div>
			<div class="clear ls4"></div>

			<div class="cd_left"><label>Clicks</label></div>
			<div class="cd_center">:</div>
			<div class="cd_right"><?php echo !empty($listings->clicks) ? ucfirst($listings->clicks) : '-'; ?></div>
			<div class="clear ls4"></div>
			
			<?php else: ?>
			<div class="clear ls4"></div>
			No Url
			<div class="clear ls4"></div>
			<?php endif;?>
			<div class="ls10 clear"></div>
		</fieldset>

	<div class="ls12 clear"></div>
	<div class="bar"></div>
	<div class="ls12"></div>
	
	<?php echo Form::submit('','Edit', array('class'=>'btn btn-primary span2')); ?>
<?php echo Form::close();?>
