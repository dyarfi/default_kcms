<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2>Add New <?php echo ucwords(str_replace('_', ' ', ucfirst($class_name))); ?></h2>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>
<?php echo Form::open(ADMIN.$class_name.'/add', array(
																'enctype' => 'multipart/form-data', 
																'method' => 'post', 
																'class' => 'general autovalid',
																'id' => ''
																));
?>
	<div class="form_details">
        
        <div class="form_row">
			<?php echo Form::label('subject','Subject'); ?>
			<?php echo Form::input('subject',!empty($fields['subject']) ? $fields['subject'] : '',array('id'=>'subject', 'class'=>'required')); ?>
			<?php echo sprintf($errors['subject'], 'Subject'); ?>
        </div>
		
        <div class="form_row">
            <?php echo Form::label('name','Name'); ?>
			<?php echo Form::input('name',!empty($fields['name']) ? $fields['name'] : '',array('id'=>'name', 'class'=>'name required', 'rel'=>$class_name)); ?>
			<span class="red"></span>
			<?php echo sprintf($errors['name'], 'Name'); ?>
        </div>
        
        <div class="form_row">
            <?php echo Form::label('news_date','News Date'); ?>
			<?php echo Form::input('news_date',!empty($fields['news_date']) ? $fields['news_date'] : '',array('id'=>'news_date', 'class'=>'required datepicker')); ?>
			<?php echo sprintf($errors['news_date'], 'News Date'); ?>
        </div>
    
        <?php if (isset($show_synopsis) && $show_synopsis) : ?>
        <div class="form_row">
            <?php echo Form::label('synopsis','Synopsis'); ?>
			<span>(20 words shown in news list)</span>
			<?php echo Form::textarea('synopsis',!empty($fields['synopsis']) ? $fields['synopsis'] : '' ,array('id'=>'synopsis', 'class'=>'small')); ?>
			
			<?php echo !empty($errors['synopsis']) ? ucfirst($errors['synopsis']) : ''; ?>
        </div>
        <?php endif; ?>
    
        <?php echo sprintf($errors['text'], 'Text'); ?>
        <div class="form_row">
            <?php echo Form::label('text','Text'); ?>
			<?php echo Form::textarea('text',!empty($fields['text']) ? $fields['text'] : '' ,array('id'=>'text', 'class'=>'required ckeditor400')); ?>
			<?php echo !empty($errors['text']) ? ucfirst($errors['text']) : ''; ?>
        </div>
    
        <?php if (isset($show_upload) && $show_upload) : ?>
		<?php 
		foreach ($uploads as $row_name => $row_params) : 
			//print_r($row_params['label']); exit();
			?>
            <fieldset style="clear:both;">
                <legend><strong><?php echo $row_params['label']; ?></strong></legend>
				<?php //echo sprintf($errors[$row_name], $row_params['label']); ?>
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
			<?php echo Form::label('status','Status'); ?>
            <select name="status" id="status" class="required">
                <option value="">&nbsp;</option>
                <?php foreach ($statuses as $row) : ?>
                <option value="<?php echo $row; ?>" <?php echo ($fields['status'] == $row) ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars(ucfirst($row), ENT_QUOTES); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
	</div>    
	<div class="ls10 clear"></div>
	<div class="bar"></div>
	<div class="ls10 clear"></div>	
	<div class="form_row">
		<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>	


<?php if (isset($show_upload) && $show_upload) : ?>
	<!--script type="text/javascript">
		var file_fields = "";
		var accepted_type = "";
		<?php 
			$i = 0;
			foreach ($uploads as $row_name => $row_params) : 
				if (isset($row_params['file_type']) && $row_params['file_type'] != '') :
					$accepted	= explode(',', $row_params['file_type']);
		?>
			var file_fields["<?php echo $i; ?>"]  = '<?php echo $row_name; ?>';
			var accepted_type["<?php echo $i; ?>"] = '<?php echo $row_params['file_type']; ?>';
		<?php 
				endif;
			$i++;	
			endforeach; 
		?>
		//alert(file_fields);
    </script-->
<?php endif; ?>