<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>

<form class="form_details" action="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/add'); ?>" method="post" enctype="multipart/form-data">
	<div class="form_wrapper">
		<?php //echo sprintf($errors['name'], 'Name'); ?>
        <div class="form_row">
            <label>Name</label>
            <input type="text" name="name" id="name" class="required" value="<?php //echo $fields['name']; ?>" />
        </div>
    
        <?php if (isset($show_category) && $show_category) : ?>
		<?php //echo sprintf($errors['category_id'], 'Category'); ?>
        <div class="form_row">
            <label>Category</label>
            <select name="category_id" id="category_id" class="required">
                <option value="">&nbsp;</option>
                <?php //foreach ($categories as $row) : ?>
                <option value="<?php echo $row->id; ?>" <?php echo ($row->id == $fields['category_id']) ? 'selected="selected"' : ''; ?>><?php echo str_repeat('&nbsp;', $row->sub_level * 5).htmlspecialchars($row->title, ENT_QUOTES); ?></option>
                <?php //endforeach; ?>
            </select>
        </div>
        <?php else : ?>
        <input type="hidden" name="category_id" id="category_id" value="<?php //echo $fields['category_id']; ?>" />
        <?php endif; ?>
    
        <?php //echo sprintf($errors['subject'], 'Subject'); ?>
        <div class="form_row">
            <label>Subject</label>
            <input type="text" name="subject" id="subject" class="required" value="<?php //echo $fields['subject']; ?>" />
        </div>
    
    	<?php //echo sprintf($errors['news_date'], 'News Date'); ?>
        <div class="form_row">
            <label>Date</label>
            <input type="text" name="news_date" id="news_date" class="required datepicker" value="<?php //echo $this->_reverse_date($fields['news_date']); ?>" />
        </div>
              
		<?php //echo sprintf($errors['synopsis'], 'Synopsis'); ?>
        <div class="form_row">
            <label>Synopsis</label>
            <textarea name="synopsis" id="synopsis"><?php //echo $fields['synopsis']; ?></textarea>
        </div>
        
        <?php //echo sprintf($errors['description'], 'Description'); ?>
        <div class="form_row">
            <label>Description</label>
            <textarea name="description" id="description" class="tiny_mce"><?php //echo $fields['description']; ?></textarea>
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
                    <label class="sub_label"><?php echo htmlspecialchars($row_params['note'], ENT_QUOTES); ?></label>
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
    
        <?php //echo sprintf($errors['status'], 'Status'); ?>
        <div class="form_row">
            <label>Status</label>
            <select name="status" id="status" class="required">
                <option value="">&nbsp;</option>
                <?php //foreach ($statuses as $row) : ?>
                <option value="<?php //echo $row; ?>" <?php //echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php //echo htmlspecialchars(ucfirst($row), ENT_QUOTES); ?></option>
                <?php //endforeach; ?>
            </select>
        </div>
    
        <div class="form_row">
            <label>&nbsp;</label>
            <input type="checkbox" name="add_another" id="add_another" value="TRUE" /> <label for="add_another" class="sub_label">Add another <?php echo ucwords(str_replace('_', ' ', $this->uri->segment(2))); ?></label>
        </div>
	</div>
	<div class="form_row">
		<label>&nbsp;</label>
		<input type="submit" class="btn_add" value="" />
	</div>
</form>

<?php if (isset($show_upload) && $show_upload) : ?>
	<script type="text/javascript">
		<?php 
			foreach ($uploads as $row_name => $row_params) : 
				if (isset($row_params['file_type']) && $row_params['file_type'] != '') :
					$accepted	= explode(',', $row_params['file_type']);
		?>
			var file_fields		= '<?php echo $row_name; ?>';
			var accepted_type	= '<?php echo $row_params['file_type']; ?>';
		<?php 
				endif;
			endforeach; 
		?>
    </script>
<?php endif; ?>