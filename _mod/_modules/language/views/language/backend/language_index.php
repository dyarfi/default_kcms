<?php defined('SYSPATH') or die('No direct script access.'); 

$arr_lang = i18n::get('system_language');

?>
<h2><?php echo $module_menu; ?></h2>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls5"></div>
<div>
<?php
	if (Session::instance()->get('level_id') == 1):?>
	<form id="listing_add" action="<?php echo URL::site() .ADMIN. $class_name; ?>/add" method="get">
		<input type="submit" class="btn btn-primary" value="Add" />
	</form>
	<?php
	endif;
?>
</div>
<div class="ls5"></div>
<?php if (!isset($listings)) : ?>
    <div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
<?php else: ?>
<?php		
echo Form::open(ADMIN.$class_name.'/change',array('method'=>'post','class'=>'form_details')); 
echo Form::hidden('order',$order);
echo Form::hidden('sort',$sort);
?> 	
<table class="listing_data listing_table">
 <thead>
			<tr>
				<th>
					<?php if (Session::instance()->get('user_id') == 1): ?>
					<input type="checkbox" name="check_all" id="check_all" />
					<?php endif;?>
				</th>
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
			$i = $pagination->current_first_item;
			$index = 1;
			foreach ($listings as $row) :
				$class      = $index % 2 == 0 ? 'even' : 'odd';
				//if ($row->status == 'verified' ) { $class = 'green'; }
				if ($row->status == 'inactive' ) { $class = 'yellow'; }
				if ($row->status == 'blocked' ) { $class = 'red_row'; }
			?>
            <tr id="row_<?php echo $row->id; ?>" class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?> <?php echo ($row->status != 1) ? 'red_row' : ''; ?>">
                <td class="center">
					<?php 
						if (Session::instance()->get('user_id') == 1):
							if (empty($row->default)): ?>
								<!--input type="checkbox" class="ipt_tocheck" pid="<?php echo $row->id;?>" hash="<?php echo Helper_Common::encript($row->id);?>"/-->
								<input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" />
							<?php 
							else:
								echo '-';
							endif;
						endif;	
					?>
							
                </td>
                <td class="center"><?php echo $i;?></td>
                <td class="bold"><?php echo ucfirst(@$row->name);?></td>
				<td><?php echo @$row->prefix;?></td>
				<td>
				<?php 
				if (!empty($row->is_system)):
					if (!empty($row->status)):
						echo Form::radio('default', $row->default, $row->default ? true : false,
								array(
									'class' => 'default_listing_one_click', 
									'urlto' => $class_name . '/aupdate_all', 
									'pid' => $row->id, 
									'hash' => Helper_Common::encript($row->id)
								));
						echo '&nbsp;';
						echo Arr::get(i18n::get('default_value'), $row->default);
					endif;
				else:
					echo '-';
				endif;
				?>
				</td>
				<td class="center">
				<?php
					if (!empty($row->file_name)):
						?>
						<a href="<?php echo IMG.'languages/'.$row->file_name; ?>" rel="Shadowbox">
							<img src="<?php echo IMG;?>admin/preview_button.png"/>
						</a>
					<?php
					endif;
				?>
                </td>
                <td><?php echo Helper_Common::convertTime(@$row->added,"d M Y");?></td>
				<td class="center"><?php echo ucfirst($statuses[$row->status]); ?></td>
				<td>
					<?php 
					if (!empty($row->is_system)) {
						echo 'Is System';
					} else {
						echo '-';	
					}
					?>
                </td>
                <td>
					<a class="view" href="<?php echo Kohana::$base_url.ADMIN.$class_name.'/view/'.$row->id;?>"></a>
					<?php if (empty($row->is_system) && Session::instance()->get('user_id') == 1): ?>
						<a class="edit" href="<?php echo Kohana::$base_url.ADMIN.$class_name.'/edit/'.$row->id;?>"></a>
					<?php endif; ?>
					<!--a class="delete language" pid="<?php echo $row->id;?>"></a-->
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
			<td colspan="<?php echo (count($table_headers) + 3); ?>">
				<div id="selection">
					<?php if (Session::instance()->get('user_id') == 1):?>
					Change status :							
					<select name="select_action" id="select_action">
						<option value="">&nbsp;</option>
						<?php foreach ($statuses as $row => $val) : ?>
						<option value="<?php echo $row; ?>"><?php echo ucfirst($val); ?></option>
						<?php endforeach; ?>
					</select>
					<?php endif;?>
				</div>
				<div id="table_pagination"><?php echo $pagination->render(); ?></div>
			</td>
		</tr>
	</tfoot>
</table>
<?php echo Form::close();?>	
<div class="ls4"></div>
<div>Total Records : <?php echo $total_record;?></div>

<div class="ls10"></div>
<?php endif; ?>
<div class="ls10"></div>
<div class="bar"></div>