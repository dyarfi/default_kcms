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
    
		<?php if (isset($show_synopsis) && $show_synopsis) : ?>
        <?php echo sprintf($errors['synopsis'], 'Synopsis'); ?>
        <div class="form_row">
            <label>Synopsis <br/>(50 words shown in front page)</label>
            <textarea name="synopsis" id="synopsis" class="small"><?php echo $fields['synopsis']; ?></textarea>
        </div>
        <?php endif; ?>
		
		<?php if (isset($show_description) && $show_description) : ?>
        <?php echo sprintf($errors['description'], 'Description'); ?>
        <div class="form_row">
            <label>Description <br/>(512 words shown in front page)</label>
            <textarea name="description" id="description" class="ckeditor"><?php echo $fields['description']; ?></textarea>
        </div>
		<?php endif; ?>
		
		<?php if (isset($show_upload) && $show_upload) : ?>
		<?php 
		foreach ($uploads as $row_name => $row_params) : ?>
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
        <?php 
		//$i++;
		endforeach; 
		?>
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
            <input type="checkbox" name="add_another" id="add_another" value="TRUE" /> <label for="add_another" class="sub_label">Add another <?php echo ucwords(str_replace('_', ' ', $class_name)); ?></label>
        </div>
	</div>
	<div class="form_row">
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>	