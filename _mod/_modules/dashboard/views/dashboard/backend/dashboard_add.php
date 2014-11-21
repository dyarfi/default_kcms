<?php defined('SYSPATH') or die('No direct script access.');?>
<h2><?php echo Helper_Common::label(i18n::get('content_new'),'Career');?></h2>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>
<?php echo Form::open(ADMIN . 'career/save', array(
                                                    'enctype' => 'multipart/form-data', 
                                                    'method' => 'post', 
                                                    'class' => 'general autovalid',
                                                    'id' => 'create-career'
                                                    ));
?>
<fieldset>		
        <legend>Career Title</legend>
        <span><?php echo Form::label('title','Title Page'); ?></span>
        <div class="ls5"></div>
        <?php echo Form::input('title',!empty($_POST['title']) ? $_POST['title'] : '',array('id'=>'title', 'class'=>'required')); ?>
        <div class="ls5"></div>

        <span><?php echo Form::label('location','Location'); ?></span>
        <div class="ls3"></div>
        <?php echo Form::input('location',!empty($_POST['location']) ? $_POST['location'] : '',array('id'=>'location', 'class'=>'required')); ?>

        <div class="ls6"></div>
        <span><?php echo Form::label('start_date','Start Date'); ?><sup style="font-size: 90%;"><?php echo i18n::get('date_format');?></sup></span>
        <div class="ls3"></div>
        <?php echo Form::input('start_date',!empty($_POST['start_date']) ? $_POST['start_date'] : '',array('id'=>'start_date', 'class'=>'required simpledate')); ?>

        <div class="ls6"></div>
        <span><?php echo Form::label('end_date','End Date'); ?><sup style="font-size: 90%;"><?php echo i18n::get('date_format');?></sup></span>
        <div class="ls3"></div>
        <?php echo Form::input('end_date',!empty($_POST['end_date']) ? $_POST['end_date'] : '',array('id'=>'end_date', 'class'=>'required simpledate')); ?>
        
        <div class="ls6"></div>
        <span><?php echo Form::label('synopsis','Synopsis'); ?></span>
        <div class="ls3"></div>
        <?php echo Form::textarea('synopsis',!empty($_POST['synopsis']) ? $_POST['synopsis'] : '',array('id'=>'synopsis', 'class'=>'required')); ?>

        <div class="ls6"></div>
        <span><?php echo Form::label('qualification','Qualification'); ?></span>
        <div class="ls3"></div>
        <?php echo Form::textarea('qualification',!empty($_POST['qualification']) ? $_POST['qualification'] : '',array('id'=>'qualification', 'class'=>'required ckeditor')); ?>
        
        <div class="ls5"></div>
        <div class="ls5"></div>
        <span><?php echo i18n::get('status');?></span>
        <div class="ls3"></div>
        <?php 
            $arr_status = i18n::get('status_value');
            echo Form::select('status', $arr_status, empty($_POST['status']) ? 1 : $_POST['status']); 
        ?>
        <div class="clear ls10"></div>
</fieldset>
<div class="clear ls10"></div>        
<?php echo Form::submit(NULL, 'Save', array('class' => 'btn btn-primary span2')); ?>
<?php echo Form::close(); ?>
