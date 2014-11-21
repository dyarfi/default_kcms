<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<form class="form_details" action="<?php echo url::site(ADMIN.$class_name.'/edit/'.$album->id); ?>" method="get">
		<div class="form_wrapper">
			<div class="form_row">
				<label>Name</label>
				<div class="form_field"><?php echo HTML::chars($album->name, TRUE); ?></div>
			</div>
			<div class="form_row">
				<label>Parent</label>
				<div class="form_field"><?php echo $parent; ?></div>
			</div>
			<div class="form_row">
				<label>Title</label>
				<div class="form_field"><?php echo HTML::chars($album->subject, TRUE); ?></div>
			</div>
			
			<?php if (isset($show_description) && $show_description) : ?>
			<div class="form_row">
				<label>Description</label>
				<div class="form_field"><?php echo ($album->description != '') ? $album->description : '-'; ?></div>
			</div>
			<?php endif; ?>			
			
			<?php if (isset($show_order) && $show_order) : ?>
			<div class="form_row">
				<label>Order</label>
				<div class="form_field"><?php echo $order; ?></div>
			</div>
			<?php endif; ?>
			
			<div class="form_row">
				<label>Status</label>
				<div class="form_field"><?php echo ucfirst($album->status); ?></div>
			</div>
			<div class="form_row">
				<label>Created</label>
				<div class="form_field"><?php echo ($album->added != 0) ? date(Lib::config('site.date_format'), $album->added) : '-'; ?></div>
			</div>
			<div class="form_row">
				<label>Last Modified</label>
				<div class="form_field"><?php echo ($album->modified != 0) ? date(Lib::config('site.date_format'), $album->modified) : '-'; ?></div>
			</div>
		</div>
		<div class="form_row">
			<?php echo Form::submit(NULL, 'Edit', array('class' => 'btn btn-primary span2')); ?>
		</div>
<?php echo Form::close();?>