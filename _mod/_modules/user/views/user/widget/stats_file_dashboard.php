<?php if (!empty($data_rows)):?>
<fieldset>
<legend><h3><a href="javascript:void(0);" class="open_table"><?php echo $data_name;?></a></h3></legend>	
<table class="listing_data table table-condensed" style="display: none">
	<thead>
	<tr>
		<th>#</th>
		<?php foreach ($data_head as $key => $value) { ?>
		<th><?php echo $value;?></th>
		<?php } ?>
	</tr>
	</thead>
	<tbody>
	<?php 
	$i = 1;
	foreach ($data_rows as $val) { ?>
		<tr class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?>">
			<td align="center"><?php echo $i;?></td>
			<td><?php echo $val->hits;?></td>			
			<td><?php echo $val->file_name;?></td>
		</tr>	
	<?php 
	$i++;
	}
	?>
	</tbody>
	<tfoot>
        <tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tfoot>
</table>
</fieldset>
<?php endif;?>