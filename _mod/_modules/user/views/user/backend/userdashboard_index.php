<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script type="text/javascript">
$(document).ready(function(){	
	<?php 
	$buffers = array();
	foreach ($article_top_ten as $val1) { 
		$buffers[] = json_encode(array('&nbsp;' . Text::limit_words($val1->subject,4) .' - '. $val1->view, $val1->view / 100));
	};
	?>	
	var data1 = [		
			<?php echo implode(',', $buffers); ?>
	];
	var plot1 = jQuery.jqplot ('chart1', [data1],
		{
		sortData:true,
		grid: {
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
        },
        axesDefaults: {
             
        },
		seriesDefaults: {
			shadow:false,
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer,
			rendererOptions: {
				sliceMargin: 0,
                // rotate the starting position of the pie around to 12 o'clock.
                startAngle: -90,
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
				showDataLabels: true
			}
		},
		legend: { show:true, location: 'w', rowSpacing:0, placement:"outsideGrid", border:"0px", fontSize:'1.0em' },
		title: { show:true, text:'Top 10 Article Hits' }
		}
	);
		
	<?php 
	$buffers = array();
	foreach ($portfolio_top_ten as $val2) { 
		$buffers[] = json_encode(array('&nbsp;' . Text::limit_words($val2->subject,13,''), $val2->view / 100));
	};

	?>	
	var data2 = [		
			<?php echo implode(',', $buffers); ?>
	];
	var plot2 = jQuery.jqplot ('chart2', [data2],
		{
		sortData:true,
		grid: {
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
        },
        axesDefaults: {
             
        },
		seriesDefaults: {
			shadow:false,
			// Make this a pie chart.
			renderer: jQuery.jqplot.PieRenderer,
			rendererOptions: {
				sliceMargin: 0,
                // rotate the starting position of the pie around to 12 o'clock.
                startAngle: -90,
				// Put data labels on the pie slices.
				// By default, labels show the percentage of the slice.
			showDataLabels: true
			}
		},
		legend: { show:true, location: 'w', rowSpacing:0, placement:"outsideGrid", border:"0px", fontSize:'0.98em' },
		title: { show:true, text:'Top 10 Portfolio Hits' }
		}
	);
});
</script>
<h2><?php echo $module_menu;?></h2>
<div class="bar"></div>
<?php 
echo Form::open(URL::site(ADMIN.$class_name.'/index/select_action'),array('method'=>'post','class'=>'form_details')); 
?>
<div class="ls10"></div>
<div class="container-fluid">
	<div class="row-fluid">
		
		<?php if (!empty($article_top_ten)):?>
		<div class="row">
			<div class="pull-left chartbox" id="chart1" style="height:250px; width:600px;"></div>
			<fieldset class="clearboth"><legend>
					<h6><a href="javascript:void(0);" class="toggletablechart">Top 10 Article Hits</a></h6>
			</legend>
			<table class="listing_data table table-condensed" style="display:block">
				<thead>
				<tr>
					<th width="1%" align="center">#</th>			
					<th>Hits</th>
					<th>Title</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$i = 1;
				foreach ($article_top_ten as $top_ten): ?>
					<tr class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?>">
						<td align="center"><?php echo $i;?></td>
						<td><?php echo $top_ten->view;?></td>
						<td><?php echo $top_ten->subject;?></td>
					</tr>	
				<?php 
				$i++;
				endforeach;
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
			<div class="topBotDiv15 clearfix"></div>
		</div>
		<?php endif;?>
		
		<?php if (!empty($portfolio_top_ten)):?>
		<div class="row">
			<div class="pull-left chartbox" id="chart2" style="height:320px; width:700px;"></div>
			<fieldset class="clearboth"><legend>
					<h6><a href="javascript:void(0);" class="toggletablechart">Top 10 Portfolio Hits</a></h6>
			</legend>
			<table class="listing_data table table-condensed" style="display:block">
				<thead>
				<tr>
					<th width="1%" align="center">#</th>			
					<th>Hits</th>
					<th>Title</th>
				</tr>
				</thead>
				<tbody>
				<?php 
				$i = 1;
				foreach ($portfolio_top_ten as $top_ten): ?>
					<tr class="<?php echo ($i % 2) ? 'even_row' : 'odd_row'; ?>">
						<td align="center"><?php echo $i;?></td>
						<td><?php echo $top_ten->view;?></td>
						<td><?php echo $top_ten->subject;?></td>
					</tr>	
				<?php 
				$i++;
				endforeach;
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
			<div class="clearfix"></div>
		</div>
		<?php endif;?>

	</div>
</div>	

<?php 
echo Form::close();
?>
<div class="bar"></div>