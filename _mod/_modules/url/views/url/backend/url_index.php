<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script type="text/javascript"></script>
<h2><?php echo $module_menu; ?></h2>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>
    <div id="forms_holder">
	<?php
		echo Form::open(ADMIN.$class_name.'/index',array('method'=>'post','id'=>'listing_search'));
		echo Form::select('field', $search_keys, $field, array(''));
		echo Form::input('keyword',$keyword,array('class'=>'','id'=>'keyword'));
		echo Form::submit('find','Search',array('class'=>'btn btn-primary','id'=>'find'));
		echo Form::close();
		echo HTML::anchor(ADMIN.$class_name.'/add', 'Add', array('class'=>'btn btn-primary','id'=>'listing_add'));
	?>
	</div>
<div class="ls5"></div>
<?php if (count($listings) == 0) :?>
	<div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
<?php else: ?>
<?php		
echo Form::open(ADMIN . $class_name.'/change',array('method'=>'post','class'=>'form_details')); 
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
			<th><a href="<?php echo url::site(ADMIN . $class_name.'/index/sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col"><?php echo $value . $order_sign; ?></a></th>
			<?php else : ?>
			<th><a href="<?php echo url::site(ADMIN . $class_name.'/index/sort/'.$key.'/order/asc/'.$page_url); ?>"><?php echo $value; ?></a></th>
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
			$class      = $index % 2 == 0 ? 'even' : 'odd';
			if ($row->status == '0' ) { $class = 'yellow'; }
			?>
            <tr class="listing_<?php echo $class;?> <?php echo $class;?>" id="row_<?php echo $row->id;?>">
                <td class="center">
					<input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" />
				</td>
				<td class="center"><?php echo $i;?></td>
					<td class="bold">
						<a href="<?php echo Url::site() . ADMIN . $class_name.'/view/'.$row->id;?>">
						<?php echo Text::limit_words(strip_tags($row->url),4);?>
					</a>
				</td>		
				<td>
					<?php echo trim(Text::limit_chars(strip_tags($row->keywords),30)); ?>
				</td>
				<td align="left">
				<?php 
					echo empty($row->title) ? '-' : $row->title;
				?>
				</td>		
				<td align="left">
				<?php 
					echo empty($row->clicks) ? '-' : $row->clicks;
				?>
				</td>	
				<td align="left">
				<?php 
					echo empty($row->status) ? 'Inactive' : 'Active';
				?>
				</td>		
				<td align="left">
				<?php 
					echo empty($row->ip) ? '-' : $row->ip;
				?>
				</td>		              
				<td align="center">
				<?php 
					echo empty($row->timestamp) ? '-' : $row->timestamp;
				?>
				</td>              
                <td width="11%">
					<a class="btn btn-mini functions" title="View" href="<?php echo URL::site(ADMIN . $class_name.'/view/' . $row->id); ?>"><div class="icon-eye-open indentMin">View</div></a>
					<a class="btn btn-mini functions" title="Edit" href="<?php echo URL::site(ADMIN . $class_name.'/edit/'.$row->id); ?>"><div class="icon-edit indentMin">Edit</div></a>
					<!--a class="btn btn-mini functions delete_function" title="Delete" href="<?php echo URL::site(ADMIN. $class_name.'/delete/' . $row->id); ?>"><div class="icon-remove-circle indentMin">Delete</div></a-->
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
						<?php foreach ($statuses as $row => $val) : ?>
						<option value="<?php echo $row; ?>"><?php echo ucfirst($val); ?></option>
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
<div class="ls4"></div>
<div>Total Records : <?php echo $total_record;?></div>
<div class="ls5"></div>
<?php endif; ?>
<div class="ls5"></div>
<div class="bar"></div>