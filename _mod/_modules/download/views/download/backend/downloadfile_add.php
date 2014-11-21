<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu; ?></h2>
<?php echo Form::open(ADMIN.$class_name.'/add', array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'general autovalid form_details',
																'id' => ''
																));
?>	
	<div class="form_wrapper">
		<div class="form_row">
			<label>Title</label>
			<input type="text" name="title" id="title" class="required" value="<?php echo $fields['title']; ?>" />
			<?php echo sprintf($errors['title'], 'Title'); ?>
		</div>
		<?php echo sprintf($errors['type_id'], 'Download'); ?>
		<div class="form_row">
			<label>Download</label>
			<select name="type_id" id="type_id" class="required">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['type_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Type ---</option>
				<?php foreach ($downloads as $row) : ?>
				<option value="<?php echo $row->id; ?>" class="type_<?php echo $row->id; ?>" <?php echo ($fields['type_id'] == $row->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($row->subject, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo sprintf($errors['category_id'], 'Category'); ?>
		<div class="form_row">
			<label>Category</label>
			<select name="category_id" id="category_pid" class="required" data-url="<?php echo URL::site(ADMIN.$class_name.'/productlookup');?>">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['category_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Category ---</option>
				<?php foreach ($categories as $category) : ?>
				<option value="<?php echo $category->id; ?>" class="category_<?php echo $category->id; ?>" <?php echo ($fields['category_id'] == $category->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($category->title, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo sprintf($errors['product_id'], 'Product'); ?>
		<div class="form_row">
			<label>Product</label>
			<select name="product_id" id="product_id" class="required">
				<!--option value="">&nbsp;</option-->
				<option value="0" <?php echo ($fields['product_id'] == 0) ? 'selected="selected"' : ''; ?>>--- Product ---</option>
				<?php foreach ($products as $product) : ?>
				<option value="<?php echo $product->id; ?>" class="category_<?php echo $product->id; ?>" <?php echo ($fields['product_id'] == $product->id) ? 'selected="selected"' : ''; ?>>
				<?php 
					echo Text::limit_words(HTML::chars($product->subject, TRUE),8);
				?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form_row">
			<label>Name</label>
			<input type="text" name="name" id="name" class="required" value="<?php echo $fields['name']; ?>" />
			<?php echo sprintf($errors['name'], 'Name'); ?>
		</div>
		<div class="form_row">
			<label>Description</label>
			<textarea name="description" id="description" class="ckeditor"><?php echo $fields['description']; ?></textarea>
			<?php echo sprintf($errors['description'], 'Description'); ?>
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
						<label class="sub_label"><?php echo HTML::chars($row_params['note'], TRUE); ?></label>
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
		<div class="form_row">
			<label>Status</label>
			<select name="status" id="status" class="required">
				<option value="">&nbsp;</option>
				<?php foreach ($statuses as $row) : ?>
				<option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo HTML::chars(ucfirst($row), TRUE); ?></option>
				<?php endforeach; ?>
			</select>
			<?php echo sprintf($errors['status'], 'Status'); ?>
		</div>
		<div class="form_row">
			<label>&nbsp;</label>
			<input type="checkbox" name="add_another" id="add_another" value="TRUE" /> <label for="add_another" class="sub_label">Add another <?php echo ucwords(str_replace('_', ' ', $class_name)); ?></label>
		</div>
		</div>
		<div class="form_row">
			<input type="submit" value="" class="btn_add" />
		</div>
<?php echo Form::close();?>
