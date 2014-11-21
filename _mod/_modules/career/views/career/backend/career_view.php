<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<h2><?php echo $module_menu; ?></h2>
<?php echo Form::open(URL::site(ADMIN.$class_name.'/edit/'.$career->id), array(
													'enctype' => 'multipart/form-data', 
													'method' => 'get', 
													'class' => 'general autovalid',
													'id' => 'edit-article'
												));	
?>

	<div class="form_wrapper">
        <div class="form_row">
            <label>Job Title</label>
            <div class="form_fields"><?php echo ($career->subject) ? $career->subject : '-'; ?></div>
        </div>
    
		<div class="form_row">
            <label>Division</label>
            <div class="form_fields"><?php echo @$divisions[$career->division_id]->subject; ?></div>
        </div>
		
        <div class="form_row">
            <label>Sent To</label>
            <div class="form_fields"><?php echo ($career->sent_to) ? $career->sent_to : '-'; ?></div>
        </div>
    
        <div class="form_row">
            <label>Ref No</label>
            <div class="form_fields"><?php echo ($career->ref_no) ? $career->ref_no : '-'; ?></div>
        </div>
    
        <div class="form_row">
            <label>Start Date</label>
            <div class="form_fields"><?php //echo $this->_reverse_date($career->start_date); ?></div>
        </div>
    
        <div class="form_row">
            <label>End Date</label>
            <div class="form_fields"><?php //echo $this->_reverse_date($career->end_date); ?></div>
        </div>
		
		<div class="form_row">
            <label>Location</label>
            <div class="form_field"><?php echo ($career->location) ? $career->location : '-'; ?></div>
        </div>
		
		<!--div class="form_row">
            <label>Company</label>
            <div class="form_field"><?php echo ($career->company) ? $career->company : '-'; ?></div>
        </div-->
        
		<div class="form_row">
            <label>Report To</label>
            <div class="form_field"><?php echo ($career->report_to) ? $career->report_to : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>Job Purpose</label>
            <div class="form_field"><?php echo ($career->job_purpose) ? $career->job_purpose : '-'; ?></div>
        </div>
		
        <div class="form_row">
            <label>Responsibilities</label>
            <div class="form_field"><?php echo ($career->responsibilities) ? $career->responsibilities : '-'; ?></div>
        </div>
        
        <div class="form_row">
            <label>Requirements</label>
            <div class="form_field"><?php echo ($career->requirements) ? $career->requirements : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>Link 1 * JobStreet</label>
            <div class="form_field"><?php echo ($career->ext_link1) ? $career->ext_link1 : '-'; ?></div>
        </div>
        
        <div class="form_row">
            <label>Link 2 * JobsDB</label>
            <div class="form_field"><?php echo ($career->ext_link2) ? $career->ext_link2 : '-'; ?></div>
        </div>
		
		<div class="form_row">
            <label>Link 3 * LionJobs</label>
            <div class="form_field"><?php //echo ($career->ext_link3) ? $career->ext_link3 : '-'; ?></div>
        </div>
    
        <div class="form_row">
            <label>Status</label>
            <div class="form_fields"><?php echo ucfirst($career->status); ?></div>
        </div>
    
        <div class="form_row">
            <label>Created</label>
            <div class="form_field"><?php echo ($career->added != 0) ? date(Lib::config('admin.date_format'), $career->added) : '-'; ?></div>
        </div>
    
        <div class="form_row">
            <label>Last Modified</label>
            <div class="form_field"><?php echo ($career->modified != 0) ? date(Lib::config('admin.date_format'), $career->modified) : '-'; ?></div>
        </div>
		
		<?php if(!empty($career->user_id)): ?>
		<div class="form_row">
            <label>Modified By</label>
            <div class="form_field"><?php echo (@$users[$career->user_id] != '') ? ucfirst(@$users[$career->user_id]->name) : '-'; ?></div>
        </div>
		<?php endif;?>
	</div>
    <div class="form_row">
		<?php echo Form::submit(NULL, 'Edit', array('class' => 'btn btn-primary span2')); ?>
	</div>
<?php echo Form::close();?>
