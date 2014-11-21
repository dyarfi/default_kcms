<?php defined('SYSPATH') OR die('No direct access allowed.'); 
// ===================== Setup Environment Data === START
// ===================== 403 - Access Forbidden ===================== //

// Get Site Counter 
$counter = Model_Setting::instance()->load_by_parameter('counter');
$data['counter']	= trim(strip_tags(@$counter->value),"\n\r\x00..\x1F");	

// Site Title Default
$titledefault = Model_Setting::instance()->load_by_parameter('title_default');
$data['title_default']	= trim(@strip_tags($titledefault->value),"\n\r\x00..\x1F");

// Set Site Copyright
$copyright = Model_Setting::instance()->load_by_parameter('copyright');
$data['copyright']	= trim(strip_tags(@$copyright->value),"\n\r\x00..\x1F");	

// Set Site Copyright
$registered = Model_Setting::instance()->load_by_parameter('registered_mark');
$data['registered']	= trim(strip_tags(@$registered->value,'<br><em><strong>'),"\n\r\x00..\x1F");
// ========================================= Setup Environment Data === END
?>
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
<meta name="keywords" content="<?php //echo $meta_keywords; ?>">
<meta name="description" content="<?php //echo $meta_description;?>">
<meta name="copyright" content="<?php //echo $meta_copyright;?>">
<meta name="robots" content="all,index,follow">
<meta name="googlebot" content="all,index,follow">
<meta name="google" content="notranslate">
<meta name="allow-search" content="yes">
<meta name="audience" content="all">
<meta name="revisit" content="2 days">
<meta name="revisit-after" content="2 days">
<meta name="distribution" content="global">
<meta name="document-classification" content="general">
<meta name="rating" content="general">
<meta name="author" content="Web Architect">
<meta name="creator" content="PT. Web Architect Technology (Web Agency in Jakarta)">
<meta http-equiv="Reply-to" content="info@webarq.com">
<!--
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Developed by WEBARQ ( http://www.webarq.com )
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
Generated : <?php echo date('d/m/Y H:m:s') . "\n";?>
-->
<script type="text/javascript"> var base_URL = '<?php echo BS_URL; ?>';</script>
<?php // foreach($styles as $file => $type) { echo HTML::style(CSS.$file, array('media' => $type)), "\n"; } ?>
<?php // foreach($scripts as $file) { echo HTML::script(JS.$file, NULL, TRUE), "\n"; } ?>
<!--[if IE 7]> <link href="css/styleIE7.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 8]> <link href="css/styleIE8.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 9]> <link href="css/styleIE9.css" rel="stylesheet" type="text/css"> <![endif]-->
<title><?php echo !empty($page_title) ? $page_title : ''; ?></title>
</head>
<body>
<div class="container" id="wrapper"><!-- start container -->
	<div class="container-fluid"><!-- start container-fluid -->
			<div class="row-fluid"><!-- start row-fluid -->
				<div id="header"><!-- start header -->
					<div class="span6">
						<h2>
							<a class="logo" href="<?php echo URL::site(); ?>" title="<?php echo @$data['title_default'];?>">
							<img src="<?php echo IMG; ?>themes/logo.png" alt="<?php echo @$data['title_name'];?>"/>
							<small><span class="webarq">WEBARQ.CO</span><!--&nbsp;&laquo;&nbsp;<span style="font-size:13px;vertical-align: baseline">URL Shortener</span>--></small>
							</a>
						</h2>
					</div>
				<div><!-- end header -->
				<div class="row-fluid">
					<div class="well content-single"><!-- start content-single -->
						<h1 style="font-size: 10.8em;margin: 46px auto 70px auto; color:#ccc; text-align: center"><?php echo Request::$current->action();?>
</h1>
						<div style="color:#ccc">
							<h2>So Sorry, Access Forbidden...</h2>
							<h3>The page that you are looking for is not found</h3>
							<span class="boxSend" style="margin: 0px auto 0 auto !important; display: block; text-align: center; text-decoration: underline">
								<h4><?php echo HTML::anchor(URL::site(), URL::site(), array('class'=>'btn_send','title'=>URL::site()));?></h4>
							</span>
						</div>
					</div><!-- end content-single -->
				</div><!-- end row-fluid -->
			</div>
			<!-- end centerWrap -->
			<div class="row-fluid center-text topBotDiv30 footer">
				<div class="row topBotDiv30">
					<span> Design by <a href="http://www.webarq.com" target="blank" title="http://webarq.com">WEBARQ</a> <br/>
					<?php echo @$data['title_name'];?></span>
					<!--span><?php echo @$data['registered'];?></span-->
					<span><?php echo @$data['copyright'];?></span>
					<div class="row"><small>Visitors : <?php echo @$data['counter'];?></small></div>
				</div>
			</div>
			<div class="clear"></div>
			</div><!-- end container -->
		</div><!-- end row-fluid -->
	</div><!-- end container-fluid -->
</div><!-- end container -->
<script type="text/javascript">
<?php if (Session::instance()->get("result") != '') : ?> 
	jAlert("<?php echo Session::instance()->get_once("result"); ?>", "Alert!");
<?php endif; ?>
<?php if (Session::instance()->get("flash") != '') : ?> 
	jAlert("<?php echo Session::instance()->get_once("flash"); ?>", "Alert!");
<?php endif; ?>
<?php if (Session::instance()->get("register_info") != '') : ?> 
	jAlert("<?php echo Session::instance()->get_once("register_info"); ?>", "Alert!"); 
<?php endif; ?>
<?php if (Session::instance()->get("auth_error") != '') : ?> 
	jAlert("<?php echo Session::instance()->get_once("auth_error"); ?>", "Alert!");
<?php endif; ?>
</script>
</body>
</html>