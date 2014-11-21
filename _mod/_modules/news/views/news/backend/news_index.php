<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script type="text/javascript"></script>
<h2><?php echo $module_menu;?></h2>
<div class="bar"></div>
    <?php echo Form::open(ADMIN.$class_name.'/index',array('method'=>'post','autocomplete'=>"off")); ?>
<div class="col-xs-5">
	<div class="row"><?php echo Form::select('field', $search_keys, $field, array('class'=>'form-control'));?></div>
</div>
<div class="col-xs-5">
	<div class="input-group">
		<?php echo Form::input('keyword',$keyword,array(
				'id'=>'keyword',
				'class'=>'typeahead form-control',
				'placeholder'=>'',	
				'data-provide'=>'typeahead',
				'data-source'=>''
				));					
		?>
		<span class="input-group-btn">
			<?php echo Form::button('find','<span class="glyphicon glyphicon-search"></span> Search',
					array('class'=>'btn btn-default btn','id'=>'find')); ?>
		</span>
	</div>
</div>			
<?php echo HTML::anchor(ADMIN.$class_name.'/add', '<span class="glyphicon glyphicon-plus-sign"></span> Add',
							array('class'=>'btn btn-default','id'=>'listing_add'));?>		
<?php echo Form::close(); ?> 
<div class="ls5"></div>
<?php if (count($listings) == 0) :?>
	<div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
<?php else: ?>
<?php		
echo Form::open(ADMIN.$class_name.'/change',array('method'=>'post','class'=>'form_details')); 
echo Form::hidden('page',$page_index);
echo Form::hidden('order',$order);
echo Form::hidden('sort',$sort);
?> 
      
<table class="listing_data">
	<thead>
		<tr>
			<th><input type="checkbox" name="check_all" id="check_all" /></th>
			<th><strong>#</strong></th>
			<?php foreach ($table_headers as $key => $value) : ?>
			<?php
				if ($sort == $key) :
					if ($order == 'asc') :
						$order = 'desc';
						$order_sign	= '&nbsp;<img src="'.IMG.'admin/order-asc.gif" alt="&or;" />';
					else :
						$order = 'asc';
						$order_sign	= '&nbsp;<img src="'.IMG.'admin/order-desc.gif" alt="&and;" />';
					endif;
			?>
			<th><a href="<?php echo url::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col"><?php echo $value . $order_sign; ?></a></th>
			<?php else : ?>
			<th><a href="<?php echo url::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/asc/'.$page_url); ?>"><?php echo $value; ?></a></th>
			<?php endif; ?>
			<?php endforeach; ?>
			<th>Functions</th>
		</tr>
	</thead>
    
    <tbody>
    <?php
	    $i = $pagination->current_first_item;
        $index = 1;
        foreach ($listings as $row):
			$arr_status     = i18n::get('status_value');
		
            $class      = $index % 2 == 0 ? 'even' : 'odd';
            //if ($row['editable'] != 1 ) { $class = 'red'; }
			if ($row->status == 'deleted' ) { $class = 'yellow'; }
            if ($row->status == 'unpublish' ) { $class = 'red_row'; }
            if (isset($current_list) && $current_list == $row->id) $class = 'green';
			?>
            <tr id="row_<?php echo $row->id; ?>" class="listing_<?php echo $class;?> <?php echo $class;?>">
                <td class="center">
					<input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" />
				</td>
				<td class="center"><?php echo $i;?></td>
					<td class="bold">
                    <a href="<?php echo Kohana::$base_url.ADMIN.$class_name.'/view/'.$row->id;?>">
						<?php echo Text::limit_words(ucfirst(strip_tags($row->subject)),4);?>
					</a>
				</td>
				<td><?php echo Text::limit_words(ucfirst(strip_tags($row->synopsis)),4); ?></td>
				<td><?php echo Text::limit_words(ucfirst(strip_tags($row->text)),4); ?></td>
				<td><?php echo $row->news_date; ?></td>
				<td><?php echo ucfirst($row->status); ?></td>
				<td><?php echo date(Lib::config('admin.date_format'), $row->added); ?></td>
				<td><?php echo ( $row->modified != 0) ? date(Lib::config('admin.date_format'), $row->modified) : '-'; ?></td>
				<td width="12%">
					<a class="btn btn-default btn-xs functions" title="View" href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>">
					<span class="glyphicon glyphicon-eye-open"></span>
						</a>
						<a class="btn btn-default btn-xs functions" title="Edit" href="<?php echo URL::site(ADMIN.$class_name.'/edit/'.$row->id); ?>"><span class="glyphicon glyphicon-edit"></span></a>
						<a class="btn btn-default btn-xs functions delete_function" title="Delete" href="<?php echo URL::site(ADMIN.$class_name.'/delete/' . $row->id); ?>"><span class="glyphicon glyphicon-trash"></span></a>
				</td>
				</tr>
			<?php
            $index++; $i++;
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td id="corner"><img src="<?php echo IMG; ?>admin/list-corner.gif"/></td>
            <td colspan="<?php echo (count($table_headers) + 2); ?>">
				<div id="selection">
					<?php echo i18n::get('changed_status'); ?> : 
					<select name="select_action" id="select_action">
						<option value="">&nbsp;</option>
						<?php foreach ($statuses as $row) : ?>
						<option value="<?php echo $row; ?>"><?php echo ucfirst($row); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div id="table_pagination"><?php echo $pagination->render(); ?></div>
            </td>
        </tr>
    </tfoot>
</table>
<?php 
echo Form::close();
?>
<div class="label label-default">Total Records : <?php echo $total_record;?></div>
<?php endif; ?>
<div class="bar"></div>