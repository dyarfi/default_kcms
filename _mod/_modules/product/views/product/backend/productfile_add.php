<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu;?></h2>

<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>

<?php 

echo Form::open(ADMIN . $class_name.'/add', array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'general autovalid form_details',
																'id' => ''
																));
?>
		<?php echo sprintf($errors['title'], 'Title'); ?>
		<div class="form_row">
			<label>Title</label>
			<input type="text" name="title" id="title" class="required" value="<?php echo $fields['title']; ?>" />
		</div>
		<?php echo sprintf($errors['name'], 'Name'); ?>
		<div class="form_row">
			<label>Name</label>
			<input type="text" name="name" id="name" class="required" value="<?php echo $fields['name']; ?>" />
		</div>
		<?php echo sprintf($errors['product_id'], 'Parent'); ?>
		<div class="form_row">
			<label>Album</label>
			<select name="product_id" id="product_id" class="required">
				<option value="">&nbsp;</option>
				<option value="0" <?php echo ($fields['product_id'] == 0) ? 'selected="selected"' : ''; ?>>No album</option>
				<?php foreach ($albums as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="album_<?php echo $row->id; ?>" <?php echo ($fields['product_id'] == $row->id) ? 'selected="selected"' : ''; ?>><?php echo str_repeat('&nbsp;', ($row->sub_level + 1) * 5).text::limit_words(HTML::chars($row->subject, TRUE),8); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo sprintf($errors['description'], 'Description'); ?>
		<div class="form_row">
			<label>Description</label>
			<textarea name="description" id="description" class="ckeditor"><?php echo $fields['description']; ?></textarea>
		</div>

		<?php if (isset($show_upload) && $show_upload) : ?>
        <?php foreach ($uploads as $row_name => $row_params) : ?>
            <fieldset style="clear:both;">
                <legend><strong><?php echo $row_params['label']; ?></strong></legend>
				<?php echo sprintf($errors[$row_name], $row_params['label']); ?>
                <div class="form_row">
                    <label><?php echo $row_params['label']; ?></label>
                    <input type="file" name="<?php echo $row_name; ?>" id="<?php echo $row_name; ?>" />
                    <?php if (isset($row_params['note']) && $row_params['note'] != '') : ?>
                        <div class="form_row">
                            <label>&nbsp;</label>
                            <?php echo HTML::chars($row_params['note'], TRUE); ?>
                        </div>
                    <?php endif; ?>
                </div>
            
                <?php if (isset($row_params['caption']) && $row_params['caption']) : ?>
                <div class="form_row">
                    <label>Caption</label>
                    <input type="text" name="<?php echo $row_name.'_caption'; ?>" id="<?php echo $row_name.'_caption'; ?>" value="<?php echo $fields[$row_name.'_caption']; ?>" />
                </div>
                <?php endif; ?>
    		</fieldset>
        <?php endforeach; ?>
        <?php endif; ?>
		
		<?php if (isset($show_allow_comment) && $show_allow_comment) : ?>
		<?php echo sprintf($errors['allow_comment'], 'Allow Comment'); ?>
		<div class="form_row">
			<label for="allow_comment">Allow User to Comment?</label>
			<input type="checkbox" name="allow_comment" id="allow_comment" value="1" <?php echo ($fields['allow_comment'] == 1) ? 'checked="checked"' : ''; ?> /> <label for="allow_comment" class="sub_label">Yes, user can comment this content</label>
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
			<input type="checkbox" name="add_another" id="add_another" value="TRUE" /> <label for="add_another" class="sub_label">Add another <?php echo ucwords($class_name); ?></label>
		</div>
		<div class="ls12 clear"></div>
		<div class="bar"></div>
        <div class="ls12"></div>
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
<?php echo Form::close(); ?>
