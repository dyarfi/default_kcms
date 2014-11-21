<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<?php if ($product_id !='') : ?>
<h2><?php echo $module_menu; ?> on &quot;<?php echo HTML::chars($products[$product_id]->subject, TRUE); ?>&quot;</h2>
<?php else : ?>
<h2><?php echo $module_menu; ?></h2>
<?php endif; ?>
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
</div>

<?php if (count($listings) == 0) :?>
    <div class="ls15 clear"></div>
		<h3 class="warning3"><?php echo i18n::get('error_no_data'); ?></h3>
	<div class="ls15"></div>
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
							$order_sign	= '&nbsp;<img src="'.ASSETS.'images/order-asc.gif" alt="&or;" />';
						else :
							$order = 'asc';
							$order_sign	= '&nbsp;<img src="'.ASSETS.'images/order-desc.gif" alt="&and;" />';
						endif;
				?>
				<th><a href="<?php echo URL::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/'.$order.$page_url); ?>" id="active_col"><?php echo $value . $order_sign; ?></a></th>
				<?php else : ?>
				<th><a href="<?php echo URL::site(ADMIN.$class_name.'/index/sort/'.$key.'/order/asc/'.$page_url); ?>"><?php echo $value; ?></a></th>
				<?php endif; ?>
				<?php endforeach; ?>
				<th>Functions</th>
			</tr>
		</thead>

		<tbody>
			<?php
				$i = $page_index + 1;
				foreach ($listings as $row) :
				?>
				<tr id="row_<?php echo $row->id; ?>" class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?> <?php echo ($row->status != $statuses[0]) ? 'red_row' : ''; ?>">
					<td align="center"><input type="checkbox" name="check[]" id="check_<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" /></td>
					<td><?php echo $i; ?></td>
					<td>
						<strong><a href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><?php echo Text::limit_words(strip_tags($row->title),6).' ('.Text::limit_chars(strip_tags($row->name),16).')'; ?></a></strong>
						<?php if (in_array($row->file_type, $readable_mime)) : ?>
						<a href="#file_<?php echo $row->id; ?>" class="zoom">
							<img src="<?php echo ASSETS; ?>images/admin/<?php echo $mime_icon[$row->file_type]; ?>" alt="<?php echo $mime_icon[$row->file_type]; ?>" /></a>
						<a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.base64_encode($upload_url.$row->file_name)); ?>">
						<img src="<?php echo ASSETS; ?>images/admin/disk.png" alt="<?php echo $row->file_name; ?>" /></a>
						<div id="file_<?php echo $row->id; ?>" class="hide">
							<?php if (substr($row->file_type, 0, strlen('image/')) == 'image/') : ?>
							<img src="<?php echo URL::site().$upload_url.$row->file_name; ?>" alt="<?php echo URL::site().$upload_url.$row->file_name; ?>" />
							<?php elseif (substr($row->file_type, 0, strlen('audio/')) == 'audio/') : ?>
							<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="20" id="FLVPlayer">
								<param name="movie" value="<?php echo ASSETS; ?>flash/singlemp3player.swf" />

								<param name="quality" value="high" />
								<param name="wmode" value="opaque" />
								<param name="scale" value="noscale" />
								<param name="salign" value="lt" />
								<param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$row->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
								<param name="swfversion" value="8,0,0,0" />
								<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
								<param name="expressinstall" value="Scripts/expressInstall.swf" />
								<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->

								<!--[if !IE]>-->
								<object type="application/x-shockwave-flash" data="<?php echo ASSETS; ?>flash/singlemp3player.swf" width="320" height="20">
								<!--<![endif]-->
									<param name="quality" value="high" />
									<param name="wmode" value="opaque" />
									<param name="scale" value="noscale" />
									<param name="salign" value="lt" />
									<param name="FlashVars" value="file=<?php echo URL::site(ADMIN.$class_name.'/download/'.$row->file_name); ?>&amp;backColor=c2c2c2&amp;frontColor=666666&amp;showDownload=false&amp;repeatPlay=false&songVolume=100" />
									<param name="swfversion" value="8,0,0,0" />

									<param name="expressinstall" value="Scripts/expressInstall.swf" />
									<!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
									<div>
									<h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
									<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
									</div>
								<!--[if !IE]>-->
								</object>

								<!--<![endif]-->
							</object>
							<?php else : ?>
							<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="320" height="240" id="FLVPlayer">
								<param name="movie" value="<?php echo ASSETS; ?>flash/FLVPlayer_Progressive.swf" />

								<param name="quality" value="high" />
								<param name="wmode" value="opaque" />
								<param name="scale" value="noscale" />
								<param name="salign" value="lt" />
								<param name="FlashVars" value="skinName=<?php echo ASSETS; ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$row->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
								<param name="swfversion" value="8,0,0,0" />
								<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don�t want users to see the prompt. -->
								<param name="expressinstall" value="Scripts/expressInstall.swf" />
								<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->

								<!--[if !IE]>-->
								<object type="application/x-shockwave-flash" data="<?php echo ASSETS; ?>flash/FLVPlayer_Progressive.swf" width="320" height="240">
								<!--<![endif]-->
									<param name="quality" value="high" />
									<param name="wmode" value="opaque" />
									<param name="scale" value="noscale" />
									<param name="salign" value="lt" />
									<param name="FlashVars" value="skinName=<?php echo ASSETS; ?>flash/Corona_Skin_2&amp;streamName=<?php echo URL::site(ADMIN.$class_name.'/download/'.$row->file_name); ?>&amp;autoPlay=false&amp;autoRewind=false" />
									<param name="swfversion" value="8,0,0,0" />

									<param name="expressinstall" value="Scripts/expressInstall.swf" />
									<!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
									<div>
									<h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
									<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
									</div>
								<!--[if !IE]>-->
								</object>

								<!--<![endif]-->
							</object>
							<?php endif; ?>
						</div>
						<?php else: ?>
						<a href="<?php echo URL::site(ADMIN.$class_name.'/download/'.$row->file_name); ?>">
						<img src="<?php echo url::base(); ?>images/cms/icon/disk.png" alt="<?php echo $row->file_name; ?>" /></a>
						<?php endif; ?>
					</td>
					<td><?php echo ($row->product_id != 0 && isset($products[$row->product_id])) ? text::limit_words(HTML::chars($products[$row->product_id]->subject, TRUE),6) : 'No Album'; ?></td>
					<td align="center"><?php echo ucfirst($row->status); ?></td>
					<td align="center"><?php echo date(Lib::config('site.date_format'), $row->added); ?></td>
					<td align="center"><?php echo ($row->modified != 0) ? date(Lib::config('site.date_format'), $row->modified) : '-'; ?></td>
					<td width="12%">
						<a class="btn btn-mini functions" title="View" href="<?php echo URL::site(ADMIN.$class_name.'/view/' . $row->id); ?>"><div class="icon-eye-open indentMin">View</div></a>
						<a class="btn btn-mini functions" title="Edit" href="<?php echo URL::site(ADMIN.$class_name.'/edit/'.$row->id); ?>"><div class="icon-edit indentMin">Edit</div></a>
						<a class="btn btn-mini functions delete_function" title="Delete" href="<?php echo URL::site(ADMIN.$class_name.'/delete/' . $row->id); ?>"><div class="icon-remove-circle indentMin">Delete</div></a>
					</td>
				</tr>
			<?php
					$i++;
				endforeach;
			?>
		</tbody>

		<tfoot>
			<tr>
				<td id="corner"><img src="<?php echo ASSETS; ?>images/admin/list-corner.gif" alt="&nbsp;" /></td>
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
<?php echo Form::close();?>
<div class="ls4"></div>
<div>Total Records : <?php echo $total_record;?></div>
<?php endif; ?>
<div class="ls10"></div>
<div class="bar"></div>
<div class="ls10"></div>