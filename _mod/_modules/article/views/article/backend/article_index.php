<?php defined('SYSPATH') OR die('No direct access allowed.'); ?><?php if ($category_id !='') : ?><h2><?php echo $module_menu; ?> on &quot;<?php echo HTML::chars($categories[$category_id]->title, TRUE); ?>&quot;</h2><?php else : ?><h2><?php echo $module_menu; ?></h2><?php endif; ?><div class="form_wrapper">	<?php echo Form::open(ADMIN.$class_name.'/index',array('method'=>'post','autocomplete'=>"off")); ?><div class="col-xs-5">	<div class="row"><?php echo Form::select('field', $search_keys, $field, array('class'=>'form-control'));?></div></div><div class="col-xs-5">	<div class="input-group">		<?php echo Form::input('keyword',$keyword,array(				'id'=>'keyword',				'class'=>'typeahead form-control',				'placeholder'=>'',					'data-provide'=>'typeahead',				'data-source'=>''				));							?>		<span class="input-group-btn">			<?php echo Form::button('find','<span class="glyphicon glyphicon-search"></span> Search',					array('class'=>'btn btn-default btn','id'=>'find')); ?>		</span>	</div></div>			<?php echo HTML::anchor(ADMIN.$class_name.'/add', '<span class="glyphicon glyphicon-plus-sign"></span> Add',							array('class'=>'btn btn-default','id'=>'listing_add'));?>		<?php echo Form::close(); ?>     <?php if (count($listings) == 0) :?>    <div class="ls15 clear"></div>		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>	<div class="ls15"></div>    <?php else : ?>    <?php			echo Form::open(ADMIN.$class_name.'/change',array('method'=>'post','class'=>'form_details')); 	echo Form::hidden('page',$page_index);	echo Form::hidden('order',$order);	echo Form::hidden('sort',$sort);	?>         <table class="listing_data">            <thead>                <tr>                    <th><input type="checkbox" name="check_all" id="check_all" /></th>                    <th><strong>#</strong></th>                    <?php 					$category_sorts = $category_id ? $category_id . '/' : ''; 					foreach ($table_headers as $key => $value) : ?>					<?php						if ($sort == $key) :							if ($order == 'asc') :								$order = 'desc';								$order_sign	= '&nbsp;<img src="'.IMG.'admin/order-asc.gif" alt="&or;" />';							else :								$order = 'asc';								$order_sign	= '&nbsp;<img src="'.IMG.'admin/order-desc.gif" alt="&and;" />';							endif;					?>                    <th>						<a href="<?php echo URL::site(ADMIN.$class_name.'/index/'.$category_sorts.'sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col">							<?php echo $value . $order_sign; ?>						</a>					</th>                    <?php else : ?>                    <th>						<a href="<?php echo URL::site(ADMIN.$class_name.'/index/'.$category_sorts.'sort/'.$key.'/order/asc/'.$page_url); ?>">							<?php echo $value; ?>						</a>					</th>                    <?php endif; ?>                    <?php endforeach; ?>                    <th>Functions</th>                </tr>            </thead>            <tbody>			<?php				$i = $page_index + 1;				foreach ($listings as $row) : ?>				<tr id="row_<?php echo $row->id; ?>" class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?>  					<?php 					$class = '';					if ($row->status == $statuses[1])						$class = 'red_row';					elseif ($row->status == $statuses[2])						$class = 'blue_row';					echo $class;					?>">					<td align="center"><input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" /></td>					<td><?php echo $i; ?></td>					<td><strong><a href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><?php echo Text::limit_words(strip_tags(ucfirst($row->subject)),3,'').''; ?></a></strong></td>					<td align="right">						<?php if (!empty($categories[$row->category_id]) && $categories[$row->category_id]->status !='publish'): ?>							<?php 								echo isset($categories[$row->category_id]) ? 								Text::limit_words(strip_tags($categories[$row->category_id]->title, TRUE),2,'') . ' ('.$categories[$row->category_id]->status.')' : 								'No Parent'; ?>						<?php else: ?>							<?php 								echo isset($categories[$row->category_id]) ? 								Text::limit_words(strip_tags($categories[$row->category_id]->title, TRUE),2,'') : 								'No Parent'; ?>						<?php endif;?>					</td>					<td align="center" style="margin:0 auto; vertical-align: middle">						<div style="width: 34px;">							<?php 							if ($row->order >= 1 && $row->order != $max_order->last_order($row->category_id)):								echo '<a class="odr_up" title="Order Up" href="'.URL::site(ADMIN.$class_name).'/order/up/'.$row->id.'/'.$row->order.'/'.$row->category_id.'"></a>';							endif;							if ($row->order <= $max_order->last_order($row->category_id) && $row->order != $min_order->first_order($row->category_id) && $row->order != $max_order->last_order($row->category_id) + 1):								echo '<a class="odr_down" title="Order Down" href="'.URL::site(ADMIN.$class_name).'/order/down/'.$row->id.'/'.$row->order.'/'.$row->category_id.'"></a>';							endif;							?>						</div>					</td>						<td align="center"><?php echo $headline[$row->headline]; ?></td>					<td align="center"><?php echo Text::limit_chars($row->view,5); ?></td>										<td align="center"><?php echo Text::limit_chars($row->count,5); ?></td>										<td align="center"><?php echo ucfirst($row->status); ?></td>					<td align="center"><?php echo $row->publish_date; ?></td>					<td align="center"><?php echo date(Lib::config('admin.date_format'), $row->added); ?></td>					<td align="center"><?php echo ($row->modified != 0) ? date(Lib::config('admin.date_format'), $row->modified) : '-'; ?></td>					<td width="12%">						<a class="btn btn-default btn-xs functions" title="View" href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>">						<span class="glyphicon glyphicon-eye-open"></span>							</a>							<a class="btn btn-default btn-xs functions" title="Edit" href="<?php echo URL::site(ADMIN.$class_name.'/edit/'.$row->id); ?>"><span class="glyphicon glyphicon-edit"></span></a>							<a class="btn btn-default btn-xs functions delete_function" title="Delete" href="<?php echo URL::site(ADMIN.$class_name.'/delete/' . $row->id); ?>"><span class="glyphicon glyphicon-trash"></span></a>					</td>				</tr>			<?php					$i++;				endforeach;			?>            </tbody>            <tfoot>                <tr>                    <td id="corner"><img src="<?php echo IMG; ?>admin/list-corner.gif" alt="&nbsp;" /></td>                    <td colspan="<?php echo (count($table_headers) + 2); ?>">                        <div id="selection">                            Change status :                            <select name="select_action" id="select_action">                                <option value="">&nbsp;</option>                                <?php foreach ($statuses as $row) : ?>                                <option value="<?php echo $row; ?>"><?php echo ucfirst($row); ?></option>                                <?php endforeach; ?>                            </select>                        </div>                        <div id="table_pagination"><?php echo $pagination->render(); ?></div>                    </td>                </tr>            </tfoot>        </table><?php echo Form::close();?><div class="ls4"></div><div class="label label-default">Total Records : <?php echo $total_record;?></div><?php endif; ?></div>