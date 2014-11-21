<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2><?php echo $module_menu; ?></h2>
<div class="form_wrapper">
    <div id="forms_warper">
        <form id="listing_search" action="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/index'); ?>" method="post">
            <select name="field" id="field">
                <?php foreach ($search_keys as $row => $label) : ?>
                <option value="<?php echo $row; ?>" <?php echo ($row == $field) ? 'selected="selected"' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="keyword" id="keyword" value="<?php echo $keyword; ?>" />
            <input type="submit" name="find" id="find" class="btn_find" value="" />
        </form>
        <form id="listing_add" action="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/add'); ?>" method="get">
            <input type="submit" class="btn_add" value="" />
        </form>
    </div>

<?php if (count($listings) == 0) :?>
<h3>There is not available data to display</h3>
<?php else : ?>
<form class="form_details" action="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/index/select_action'); ?>" method="post">
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
					<a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/index/sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col"><?php echo $value.$order_sign; ?></a>
					<?php else : ?>
					<?php echo $value; ?>
					<?php endif; ?>
				</th>

				<?php else : ?>

				<th>
					<?php if (!is_numeric($key)) : ?>
					<a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/index/sort/'.$key.'/order/asc/'.$page_url); ?>"><?php echo $value; ?></a>
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

				$i	= $page_index + 1;

				foreach ($listings as $row) :

				$load = isset($profiles[$row->id]) ? $profiles[$row->id] : FALSE;

			?>

				<tr class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?> <?php echo ($load != FALSE) ? ($profiles[$row->id]->status != $statuses[0]) ? 'red_row' : '' : ''; ?>">
					<td align="center">
						<?php if (!$row->is_system && $this->session->get('user_id') != $row->id) { ?>
							<input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" />
						<?php } ?>
					</td>
					<td><?php echo $i; ?></td>
					<td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
					<td><strong><a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/view/' . $row->id); ?>"><?php echo $row->name; ?></a></strong></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->country : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->state : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->city : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->phone : '-'; ?></td>
					<td><?php echo ($load != FALSE) ? $profiles[$row->id]->status : '-'; ?></td>
					<td align="center"><?php echo ($load !== FALSE) ? date(Lib::config('admin.date_format'), $profiles[$row->id]->added) : '-'; ?></td>
					<td align="center"><?php echo ($load !== FALSE && $profiles[$row->id]->modified != 0) ? date(Lib::config('admin.date_format'), $profiles[$row->id]->modified) : '-'; ?></td>
					<td align="center">
						<a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/view/' . $row->id); ?>"><img src="<?php echo url::base(); ?>images/cms/icon/page_white.png" alt="View" /></a>
						<a href="<?php echo URL::site(ADMIN.$this->uri->segment(2).'/update/' . $row->id); ?>"><img src="<?php echo url::base(); ?>images/cms/icon/pencil.png" alt="Edit" /></a>
					</td>

				</tr>
			<?php
					$i++;
				endforeach;
			?>
		</tbody>

		<tfoot>
			<tr>
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