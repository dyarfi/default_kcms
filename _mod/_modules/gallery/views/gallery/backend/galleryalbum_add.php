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
		
		<?php echo sprintf($errors['subject'], 'Subject'); ?>
		<div class="form_row">
			<label>Subject</label>
			<input type="text" name="subject" id="subject" class="required" value="<?php echo $fields['subject']; ?>" />
		</div>

		<?php echo sprintf($errors['name'], 'Name'); ?>
		<div class="form_row">
			<label>Name</label>
			<input type="text" name="name" id="name" class="required" value="<?php echo $fields['name']; ?>" />
		</div>
		
		<?php echo sprintf($errors['parent_id'], 'Parent'); ?>
		<div class="form_row">
			<label>Parent</label>
			<select name="parent_id" id="parent_id" class="required">
				<option value="">&nbsp;</option>
				<option value="0">This <?php echo ucfirst($class_name); ?> is parent</option>
				<?php foreach ($albums as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="parent_<?php echo $row->parent_id; ?>"><?php echo str_repeat('&nbsp;', ($row->sub_level + 1) * 5).text::limit_words(HTML::chars($row->subject, TRUE),8); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		
		<?php if (isset($show_description) && $show_description) : ?>
		<?php echo sprintf($errors['description'], 'Description'); ?>
		<div class="form_row">
			<label>Description</label>
			<textarea name="description" id="description" class="tiny_mce"><?php echo $fields['description']; ?></textarea>
		</div>
		<?php endif; ?>

		<?php if (isset($show_owner) && $show_owner) : ?>
		<?php echo sprintf($errors['user_id'], 'Owner'); ?>
		<div class="form_row">
			<label>Owner</label>
			<select name="user_id" id="user_id">
				<option value="">&nbsp;</option>
				<option value="0" <?php echo ($fields['user_id'] == 0) ? 'selected="selected"' : ''; ?>>System</option>
				<?php foreach ($users as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="user_<?php echo $row->id; ?>" <?php echo ($fields['user_id'] == $row->id) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars($row->name.' ('.$row->email.')', TRUE); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<?php if (isset($show_order) && $show_order) : ?>
		<?php echo sprintf($errors['order'], 'Order'); ?>
		<div class="form_row">
			<label>Order</label>
			<select name="order" id="order">
				<option value="">&nbsp;</option>
			</select>
		</div>
		<?php endif; ?>
		
		<?php echo sprintf($errors['status'], 'Status'); ?>
		<div class="form_row">
			<label>Status</label>
			<select name="status" id="status" class="required">
				<option value="">&nbsp;</option>
				<?php foreach ($statuses as $row) : ?>
				<option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars(ucfirst($row), TRUE); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		
		<div class="form_row">
			<label>&nbsp;</label>
			<input type="checkbox" name="add_another" id="add_another" value="TRUE" /> <label for="add_another" class="sub_label"><?php echo ucwords($module_menu); ?></label>
		</div>

		<div class="ls12 clear"></div>
		<div class="bar"></div>
        <div class="ls12"></div>
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
<?php echo Form::close(); ?>
