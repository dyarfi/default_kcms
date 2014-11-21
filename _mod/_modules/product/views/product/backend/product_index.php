<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu;?></h2>
<div class="bar"></div>
<div class="row-fluid">
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
</div>

<?php if (count($listings) == 0) :?>
    <div class="ls5 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls5"></div>
<?php else : ?>
<?php 
echo Form::open(ADMIN.$class_name.'/change',array('method'=>'post','class'=>'form_details')); 
echo Form::hidden('page',$page_index);
echo Form::hidden('order',$order);
echo Form::hidden('sort',$sort);
?>
	<table class="listing_table">
		<thead>
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" /></th>
				<th><strong>#</strong></th>
				<?php foreach ($table_headers as $key => $value) : ?>
				<?php
					if ($sort == $key) :
						if ($order == 'asc') :
							$order = 'desc';
							$order_sign	= '&nbsp;<img src="'.url::base().'images/cms/icon/order-asc.gif" alt="&or;" />';
						else :
							$order = 'asc';
							$order_sign	= '&nbsp;<img src="'.url::base().'images/cms/icon/order-desc.gif" alt="&and;" />';
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
				$i				= $page_index + 1;

				foreach ($listings as $row) :
			?>
				<tr id="row_<?php echo $row->id; ?>" class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?> <?php echo ($row->status != $statuses[0]) ? 'red_row' : ''; ?>">
					<td align="center"><input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" /></td>
					<td><?php echo $i; ?></td>
					<td><?php echo str_repeat('&nbsp;', abs($row->sub_level * 5)); ?><strong><a href="<?php echo url::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><?php echo text::limit_words(strip_tags($row->subject),6).' ('.text::limit_chars(strip_tags($row->name),25).')'; ?></a></strong></td>
					<!--td align="center"><?php echo $row->order; ?></td-->
					<td align="center"><?php echo ucfirst($row->status); ?></td>
					<td align="center"><?php echo date(Lib::config('site.date_format'), $row->added); ?></td>
					<td align="center"><?php echo ($row->modified != 0) ? date(Lib::config('site.date_format'), $row->modified) : '-'; ?></td>
					<td width="12%">
						<a class="btn btn-default btn-xs functions" title="View" href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><span class="glyphicon glyphicon-eye-open"></span></a>
						<a class="btn btn-default btn-xs functions" title="Edit" href="<?php echo URL::site(ADMIN.$class_name.'/edit/'.$row->id); ?>"><span class="glyphicon glyphicon-edit"></span></a>
							<?php if (!$row->is_system && Session::instance()->get('user_id') != $row->id) { ?>
						<a class="btn btn-default btn-xs functions delete_function" title="Delete" href="<?php echo URL::site(ADMIN.$class_name.'/delete/' . $row->id); ?>"><span class="glyphicon glyphicon-trash"></span></a>							
							<?php } ?>
					</td>
				</tr>
			<?php
					$i++;
				endforeach;
			?>
		</tbody>

		<tfoot>
			<tr>
				<td id="corner"><img src="<?php echo url::base(); ?>images/cms/icon/list-corner.gif" alt="&nbsp;" /></td>
				<td colspan="<?php echo (count($table_headers) + 2); ?>">
					<div id="selection">
						Change status :
						<select name="select_action" id="select_action">
							<option value="">&nbsp;</option>
							<?php foreach ($statuses as $row) : ?>
							<option value="<?php echo $row; ?>"><?php echo ucfirst($row); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div id="table_pagination"><?php echo $pagination->render('digg'); ?></div>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
<div class="label label-default">Total Records : <?php echo $total_record;?></div>
<?php endif; ?>
<div class="bar"></div>