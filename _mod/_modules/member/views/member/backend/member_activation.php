<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu; ?></h2>
<div class="form_wrapper">
    <div id="forms_warper">
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
    </div>

<?php if (count($listings) == 0) :?>
    <div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
<?php else : ?>
<form class="form_details" action="<?php echo URL::site(ADMIN.$class_name.'/index/select_action'); ?>" method="post">
	<input type="hidden" name="page" value="<?php echo $page_index; ?>" />
	<input type="hidden" name="order" value="<?php echo $order; ?>" />
	<input type="hidden" name="sort" value="<?php echo $sort; ?>" />

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

				<th>
					<?php if (!is_numeric($key)) : ?>
					<a href="<?php echo URL::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col"><?php echo $value.$order_sign; ?></a>
					<?php else : ?>
					<?php echo $value; ?>
					<?php endif; ?>
				</th>

				<?php else : ?>

				<th>
					<?php if (!is_numeric($key)) : ?>
					<a href="<?php echo URL::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/asc/'.$page_url); ?>"><?php echo $value; ?></a>
					<?php else : ?>
					<?php echo $value; ?>
					<?php endif; ?>
				</th>

				<?php endif; ?>
				<?php endforeach; ?>
				<th>Functions</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$i				= $page_index + 1;
				foreach ($listings as $row) :
				$load = isset($profiles[$row->id]) ? $profiles[$row->id] : FALSE;
			?>
				<tr class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?> <?php echo ($row->status != $statuses[0]) ? 'red_row' : ''; ?>">
					<td align="center"><input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" /></td>
					<td><?php echo $i; ?></td>
					<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
					<td><strong><a href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><?php echo $row->name; ?></a></strong></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->division : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->country : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->state : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->city : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->phone : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->status : '-'; ?></td>
					<td align="center"><?php echo ($load !== FALSE) ? date(Lib::config('admin.date_format'), $profiles[$row->id]->added) : '-'; ?></td>
					<td align="center"><?php echo ($load !== FALSE && $profiles[$row->id]->modified != 0) ? date(Lib::config('admin.date_format'), $profiles[$row->id]->modified) : '-'; ?></td>
					<td align="center">
						<a href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><img src="<?php echo url::base(); ?>images/cms/icon/page_white.png" alt="View" />View</a>
						<a href="<?php echo URL::site(ADMIN.$class_name.'/update/' . $row->id); ?>"><img src="<?php echo url::base(); ?>images/cms/icon/pencil.png" alt="Edit" />Edit</a>
						<!--a href="<?php echo URL::site(ADMIN.$class_name.'/delete/' . $row->id); ?>" class="delete" title="Delete">Delete/a-->
					</td>
				</tr>
			<?php
					$i++;
				endforeach;
			?>
		</tbody>
		<tfoot>
			<tr>
				<!--td id="corner"><img src="<?php echo url::base(); ?>images/list-corner.gif" alt="&nbsp;" /></td-->
				<td id="corner">&nbsp;</td>
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
<?php endif; ?>
</div>