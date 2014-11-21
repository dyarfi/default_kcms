<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
<title>Application Form Admin Document</title>
</head>
<body style="font-family: Georgia, Tahoma, Verdana;/*	background: #a40305;*/	background:transparent url('<?php echo IMG;?>themes/material/bg_body.jpg') no-repeat top center;	margin: 0 auto; padding: 0 0 15px 0;text-align: center; color:#333;	font-size:0.88em;line-height:1.5em">
<div style="	min-width: 55%; 	width: 84%;	background: transparent /*#FFFFFF*/;	margin: 0 auto; 	text-align: left">
  <div style="background: transparent /*#dc021b*/; padding: 0 10px 0 20px;margin:0 auto;padding:10px;text-align:center;	overflow:hidden;color:#999;"><a href="<?php echo URL::site();?>" title="<?php echo $site_name; ?>" style="width:auto;text-align:center;margin:2px auto 2px auto;"> <img alt="<?php echo $site_name; ?>" style="width:auto;max-width:100%;margin:0 auto;border:0;display:block;outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;" src="<?php echo IMG; ?>themes/content/logo.png"/></a>
    <!-- end #header -->
  </div>
  <div style="	border-top-left-radius: 5px 5px;	border-bottom-right-radius: 0px 0px;	border-top-right-radius: 5px 5px;	border-bottom-left-radius: 0px 0px;	-moz-border-radius-topleft: 5px;	-moz-border-radius-topright: 5px;	-moz-border-radius-bottomright: 0px;	-moz-border-radius-bottomleft: 0px;	-webkit-border-top-left-radius: 5px;	-webkit-border-top-right-radius: 5px;	-webkit-border-bottom-right-radius: 0px;	-webkit-border-bottom-left-radius: 0px;   	background: #FFFFFF;	padding: 10px 20px; 	bottom:0px;	overflow:hidden;	top:0px;	bottom:0px;"><span style="color:#999;font-style:italic;float:none;text-align:right;position:relative;display:block;margin:0 0 5px 0;"><?php echo date('l, F Y');?></span>
    <div style="	width:100%;	background:#b1b1b1;	margin:10px 0 15px 0;">
      <h2 style="font-size:12px;	font-weight:bold;	color:#333;	background:#fff;	float:none;	margin-left:15px;	padding:3px 5px;	display:inline;	width:auto;	text-transform:uppercase;">Application Form</h2>
    </div>
    <p>Dear <?php echo ucfirst($name);?>, thank you for sending your job application form to us regarding <?php echo ucfirst($subject);?> vacancy, we will consider and look into your job application immediately.<br/>
      <br/>
      Thank you for the attention.<br/>
      <br/>
    </p>
    <!-- end #mainContent -->
  </div>
  <div style="	border-top-left-radius: 0px 0px;	border-bottom-right-radius: 5px 5px;	border-top-right-radius: 0px 0px;	border-bottom-left-radius: 5px 5px;	-moz-border-radius-topleft: 0px;	-moz-border-radius-topright: 0px;	-moz-border-radius-bottomright: 5px;	-moz-border-radius-bottomleft: 5px;	-webkit-border-top-left-radius: 0px;	-webkit-border-top-right-radius: 0px;	-webkit-border-bottom-right-radius: 5px;	-webkit-border-bottom-left-radius: 5px;	background: #fff;		   	padding: 0 10px; 	margin:0 auto 70px auto;	line-height:normal">
    <div style="	float:none;	display:block;	clear:both;	margin:0px auto 0 auto;	border-bottom:1px dotted #ccc;"></div>
    <span style="	color:#999;	padding:10px; 	margin:0 auto;	display:block;	font-style:italic;	text-align:center;	line-height:normal;	font-size:0.895em;"> <?php echo $site_name; ?> </span>
    <!-- end #footer -->
  </div>
  <!-- end #container -->
</div>
</body>
</html>
