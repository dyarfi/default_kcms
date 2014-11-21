<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<h2>
	Module Listings
	<?php
		// Alert users if module is updated
		if(Session::instance()->get('modulelist_edit') != '') {
		?>
			<small class="red fadeOut"> - <?php echo Session::instance()->get_once('modulelist_edit');?></small>
		<?php
		}
	?>
</h2>
<div class="bar"></div>
<?php if (count($listings) == 0) :?>
	<div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
<?php else : ?>
<?php		
echo Form::open(ADMIN . $class_name . '/edit',array('method'=>'post','class'=>'form_details')); 
?> 
<h5>Role Based Access Control :</h5>
<table class="listing_data" width="100%">
		<thead>
			<tr>
				<th><strong>#</strong></th>
				<?php foreach ($table_headers as $key => $value) : ?>
                <th><?php echo $value; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$i	= 1;
			foreach ($listings as $row) :
			?>
			<tr id="row_<?php echo $row->id; ?>" class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?>">
					<td align="center">
						<?php echo $i; ?>
					</td>
					<td align="left">
						<a href="javascript:void(0);" id="mod_id_<?php echo $row->id;?>" class="mod_id">
						<?php if (($row->parent_id != 0)): ?>
							<!--strong>
								<?php echo str_repeat('&nbsp;', 5) . ucwords($row->module_name); ?>
							</strong-->
						<?php else: ?>
							<?php echo ($row->parent_id != 0) ? str_repeat('&nbsp;', 5) : ''; ?>
							<h3><?php echo ucwords($row->module_name); ?></h3>
						<?php endif; ?>
						</a>
					</td>
                    <?php 
						foreach($user_level as $var) { 
							?>
							<td align="center" class="select_holder">
							<?php echo (($row->parent_id != 0)) ? str_repeat('&nbsp;', 5) : ''; ?>
							<input type="hidden" name="module_menu[level_id][<?php echo $var->id; ?>][<?php echo $row->id; ?>]" id="check_<?php echo $row->id; ?>" value="0"/>
							<input class="module_menu" type="checkbox" name="module_menu[level_id][<?php echo $var->id; ?>][<?php echo $row->id; ?>]" id="check_<?php echo $row->id; ?>" value="1" <?php echo ($var->id) ? 'checked="checked"' : ''; ?>/>
							<?php if (isset($module_permission)):?>
								<table width="100%" style="display:block" class="mod_func" id="mod_func_<?php echo $row->id;?>">
									<?php 
									$module_menus = Lib::config($row->module_name.'.module_menu');
									$module_funcs = Lib::config($row->module_name.'.module_function');
									$module_merge = Arr::merge($module_menus, $module_funcs);
									$j	= 1;
									foreach ($module_permission as $permission):
											if (!empty($module_merge[$permission->module_link]) && !empty($user_level_permission[$var->id][$permission->id]->id)): 
											?>
										<tr class="<?php echo ($j % 2) ? 'white_row' : 'green_row'; ?>" style="border:1px solid #eee !important">
											<td align="right" class="cb_holder">
											<?php 
												//echo $permission->module_name;
												//echo $user_level_permission[$var->id][$permission->id]->id; 
											?>
											<input type="hidden" name="module_permission[level_id][<?php echo $var->id; ?>][<?php echo $user_level_permission[$var->id][$permission->id]->id; ?>]" id="check_h_<?php echo $user_level_permission[$var->id][$permission->id]->id; ?>" class="check_hidden_mplh" value="0" />
											<input type="checkbox" name="module_permission[level_id][<?php echo $var->id; ?>][<?php echo $user_level_permission[$var->id][$permission->id]->id; ?>]" id="check_<?php echo $user_level_permission[$var->id][$permission->id]->id; ?>" class="check_hidden_mplr" value="1" <?php echo (@$user_level_permission[$var->id][$permission->id]->value==1) ? 'checked="checked"' : ''; ?>/>
											</td>
											<td align="left">
												<span><?php echo $module_merge[$permission->module_link];?></span>
											</td>
										</tr>
										<?php endif;?>
										<?php
									$j++;
									endforeach; 
									?>
								</table>	
							<?php endif; ?>
							</td>
					<?php
					} 
					?>
				</tr>
			<?php
			$i++;
			endforeach;
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="<?php echo (count($table_headers) + 1); ?>">
                	<div id="table_pagination"><input type="submit" class="btn btn-primary" value="Save" /></div>
                </td>
			</tr>
		</tfoot>
	</table>
<?php Form::close(); ?>
<?php endif; ?>
