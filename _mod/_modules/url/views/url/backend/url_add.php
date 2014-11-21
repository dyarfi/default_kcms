<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>

<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>

<?php echo Form::open(ADMIN . $class_name.'/add', array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'general autovalid form_details',
																'id' => ''
																));
?>

		<fieldset>
			<legend>Url Information</legend>

			<div class="form_row">
				<?php echo Form::label('url','Url'); ?>
				<?php echo Form::input('url', $fields['url'], array('class'=>'','id'=>'url'));?>	
				<?php echo sprintf($errors['url'], 'Url'); ?>
			</div>
			
			<div class="form_row">
				<?php echo Form::label('keywords','Keywords'); ?>
				<?php echo Form::input('keywords', $fields['keywords'] ? $fields['keywords'] : Text::random('alnum', rand(4,7)), array('class'=>'','id'=>'keywords'));?>
				<?php echo sprintf($errors['keywords'], 'Keywords'); ?>
			</div>
			
			<div class="form_row">
				<?php echo Form::label('title','Title'); ?>
				<?php echo Form::textarea('title', $fields['title'], array('class'=>'','id'=>'title'),TRUE);?>	
				<?php echo sprintf($errors['title'], 'Title'); ?>
			</div>

			<div class="ls20 clear"></div>
			
		</fieldset>
		<div class="ls12"></div>
		<div class="bar"></div>
        <div class="ls12"></div>
	<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
<?php echo Form::close();?>
