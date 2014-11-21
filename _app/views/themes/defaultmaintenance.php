<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width">
<link rel="shortcut icon" href="<?php echo BS_URL;?>favicon.ico" type="image/x-icon">
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="<?php echo $meta_keywords; ?>">
<meta name="description" content="<?php echo $meta_description;?>">
<meta name="copyright" content="<?php echo $meta_copyright;?>">
<meta name="robots" content="all,index,follow">
<meta name="googlebot" content="all,index,follow">
<meta name="allow-search" content="yes">
<meta name="audience" content="all">
<meta name="revisit" content="2 days">
<meta name="revisit-after" content="2 days">
<meta name="author" content="">
<meta name="creator" content="">
<meta http-equiv="Reply-to" content="">
<meta name="distribution" content="global">
<meta name="document-classification" content="general">
<meta name="rating" content="general">
<!--<link rel="canonical" href="http://www.example.com">-->
<!--
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Developed by <?php echo DEVELOPER_NAME;?> ( <?php echo DEVELOPER_URL;?> )
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
Generated : <?php echo date('d/m/Y H:m:s') . "\n";?>
-->
<script type="text/javascript"> var base_URL = '<?php echo BS_URL; ?>';</script>
<?php foreach($styles as $file => $type) { echo HTML::style(CSS.$file, array('media' => $type)), "\n"; }?>
<?php foreach($scripts as $file) { echo HTML::script(JS.$file, NULL, TRUE), "\n"; }?>
<!--[if IE 7]> <link href="css/styleIE7.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 8]> <link href="css/styleIE8.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 9]> <link href="css/styleIE9.css" rel="stylesheet" type="text/css"> <![endif]-->
<title><?php echo $page_title; ?></title>
</head>
<body>
<div class="container maintenance" id="wrapper">
	<!-- start container-fluid -->
	<div class="container-fluid">
		<!-- start row -->
			<div class="row-fluid">
				<div id="header">
					<div class="span6">
						<h2>
							<a class="logo" href="<?php echo URL::site(); ?>" title="<?php echo $title_default;?>">
							<img src="<?php echo IMG; ?>themes/logo.png" alt="<?php echo $title_name;?>"/>
							</a>
						</h2>
						<?php //echo @$site_name;?>
						<?php //echo @$site_quote; ?>
					</div>			
					<div class="pull-right">
						<div class="socialMedia">
							<ul class="ulSocial pull-right">
								<?php if (!empty($socmed_facebook)):?>
								<li>
									<!--script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script-->
									<a href="<?php echo $socmed_facebook->value;?>" title="<?php echo $socmed_facebook->alias;?>" target="_blank">
									<img class="icon-facebook" alt="<?php echo $socmed_facebook->alias;?>" src="<?php echo IMG; ?>themes/glyphicons/png/white/glyphicons_390_facebook.png"/></a> 	
								</li>
								<?php endif; ?>
								<?php if (!empty($socmed_twitter)):?>
								<li>
									<a href="<?php echo $socmed_twitter->value;?>" data-lang="en" title="<?php echo $socmed_twitter->alias;?>" target="_blank">
									<img class="icon-twitter" alt="<?php echo $socmed_twitter->alias;?>" src="<?php echo IMG; ?>themes/glyphicons/png/white/glyphicons_391_twitter_t.png"/></a>
								</li>
								<?php endif; ?>
								<?php if (!empty($socmed_gplus)):?>
								<li><a href="<?php echo $socmed_gplus->value;?>" title="<?php echo $socmed_gplus->alias;?>" target="_blank"><img class="icon-gplus" alt="<?php echo $socmed_gplus->alias;?>" src="<?php echo IMG; ?>themes/glyphicons/png/white/glyphicons_386_google_plus.png"/>
									</a>
								</li>
								<?php endif; ?>								
								<?php if (!empty($socmed_linkedin)):?>
								<li><a href="<?php echo $socmed_linkedin->value;?>" title="<?php echo $socmed_linkedin->alias;?>" target="_blank">
									<img class="icon-linkin" alt="<?php echo $socmed_linkedin->alias;?>" src="<?php echo IMG; ?>themes/glyphicons/png/white/glyphicons_377_linked_in.png"/>
									</a>
								</li>
								<?php endif; ?>								
								<?php if (!empty($socmed_pinterest)):?>
								<li><a href="<?php echo $socmed_pinterest->value;?>" title="<?php echo $socmed_pinterest->alias;?>" target="_blank">
										<img class="icon-pinterest" alt="<?php echo $socmed_pinterest->alias;?>" src="<?php echo IMG; ?>themes/glyphicons/png/white/glyphicons_360_pinterest.png"/>
									</a>
								</li>
								<?php endif; ?>								
							</ul>
						</div>
					</div>
				</div>
		</div>
	
		<div class="row-fluid">
			<!--div class="well content"-->
			<div class="well content-single">
			<!-- start main content -->
			<?php //echo isset($content) ? $content : '';?>
			<h2>The site is off for <span><h1>MAINTENANCE</h1></span></h2>
			<!-- end main content -->
			</div>
		</div>
		
		<div class="row-fluid center-text topBotDiv30 footer">
			<div class="row">				
				<?php echo @$title_name;?></span>
				<!--span><?php echo @$registered;?></span-->
				<span><?php echo @$copyright;?></span>
				<div class="row"><small>Visitors : <?php echo @$counter;?></small></div>
			</div>
		</div>

	</div>	
	<!-- end container-fluid -->
</div>
<!-- end container -->
<script type="text/javascript">
$(document).ready(function() {	
<?php if (Session::instance()->get_once("result") != '') : ?> 
	//$('#message_modal').text(<?php //echo Session::instance()->get("result");?>);
	$('#myModal').modal('show');
<?php endif; ?>	
<?php if (Session::instance()->get_once("flash") != '') : ?> 
	//$('#message_modal').text(<?php //echo Session::instance()->get("flash");?>);
	$('#myModal').modal('show');
<?php endif; ?>		
<?php if (Session::instance()->get_once("register_info") != '') : ?> 
	//$('#message_modal').text(<?php //echo Session::instance()->get("register_info");?>);
	$('#myModal').modal('show');	
<?php endif; ?>			
<?php if (Session::instance()->get_once("auth_error") != '') : ?> 
	//$('#message_modal').text(<?php //echo Session::instance()->get("auth_error");?>);
	$('#myModal').modal('show');	
<?php endif; ?>			
});	
</script>
</body>
</html>