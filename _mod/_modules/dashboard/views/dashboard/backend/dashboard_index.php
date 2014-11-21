<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script type="text/javascript">
$(document).ready(function(){
	<?php 
	$buffers = array();
	foreach ($url_top_ten_click as $val1) { 
		$buffers[] = json_encode(array($val1->keywords,$val1->clicks / 100),TRUE);
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
		legend: { show:true, location: 'w', rowSpacing:0, placement:"outsideGrid", border:"0px" },
		title: { show:true, text:'Top 10 Keywords' }
		}
	);
		
	<?php 
	$buffers = array();
	foreach ($url_top_ten_ip as $val2) { 
		$buffers[] = json_encode(array($val2->ip_address,$val2->count / 100),TRUE);
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
		legend: { show:true, location: 'w', rowSpacing:0, placement:"outsideGrid", border:"0px" },
		title: { show:true, text:'Top 10 Ip Address' }
		}
	);
			
		
});
</script>

<h2><?php echo Helper_Common::label(i18n::get('content_listing'),ucfirst($class_name));?></h2>

<div class="ls10"></div>
<div class="bar"></div>

<?php 
echo Form::open(URL::site(ADMIN . $class_name.'/index/select_action'),array('method'=>'post','class'=>'form_details')); 
?>

<?php
/*
print_r(Request::$user_agent);
// Load browsers
$browsers = Lib::config('user_agents')->browser;

$buffers = array();
$info	 = array();

	//$buffers = array($val->user_agent,$val->count);
	foreach ($browsers as $search => $name) {
		foreach($url_top_ten_browser as $val) { 
			if (stripos($val->user_agent, $search) !== FALSE) {
				// Set the browser name
				$info['browser'] = $name;

				if (preg_match('#'.preg_quote($search).'[^0-9.]*+([0-9.][0-9.a-z]*)#i', $val->user_agent, $matches))
				{
					// Set the version number
					$info['version'] = $matches[1];
				}
				else
				{
					// No version number found
					$info['version'] = FALSE;
				}

				$buffers[] = $info;
			}
		}
	} 
print_r(json_encode($buffers));
 * 
 */
?>

<div class="ls10"></div>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="pull-left">
			<span class="hide">Top 10 Keywords</span>
			<div id="chart1" style="height:280px; width:360px; padding:0px; margin: 0"></div>
		</div>
		<div class="pull-right">
			<span class="hide">Top 10 Ip Address</span>
			<div id="chart2" style="height:280px; width:360px; padding:0px; margin: 0"></div>
		</div>
	</div>
</div>	

Top 10 Url clicks
<table class="listing_data table table-condensed">
	<thead>
	<tr>
		<th>#</th>			
		<th>Clicks</th>
		<th>Keywords</th>
		<!--th>Url</th-->
	</tr>
	</thead>
	<tbody>
	<?php
	$i = 1;
	foreach ($url_top_ten_click as $top_ten_click): ?>
		<tr>
			<td align="center"><?php echo $i;?></td>
			<td><?php echo $top_ten_click->clicks;?></td>
			<td><?php echo $top_ten_click->keywords;?></td>
			<!--td><?php //echo $top_ten_click->url;?></td-->
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
			<!--td></td-->
		</tr>
	</tfoot>
</table>

Top 10 Browser User Agents
<table class="listing_data table table-condensed">
	<thead>
	<tr>
		<th>#</th>
		<th>Count</th>
		<th>Browser User Agents</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	$i = 1;
	foreach ($url_top_ten_browser as $top_ten_browser): ?>
		<tr>
			<td align="center"><?php echo $i;?></td>
			<td><?php echo $top_ten_browser->count;?></td>
			<td><?php echo $top_ten_browser->user_agent;?></td>
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

Top 10 IP Clickers
<table class="listing_data table table-condensed">
	<thead>
	<tr>
		<th>#</th>
		<th>Click Count</th>
		<th>Ip Address</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	$i = 1;
	foreach ($url_top_ten_ip as $top_ten_ip): ?>
		<tr>
			<td align="center"><?php echo $i;?></td>
			<td><?php echo $top_ten_ip->count;?></td>			
			<td><?php echo $top_ten_ip->ip_address;?></td>
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

<?php 
echo Form::close();
?>

<div class="ls10"></div>
<div class="bar"></div>