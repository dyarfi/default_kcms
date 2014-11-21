<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
<title>Contact Form Public Document</title>
<style type="text/css">
body { }
</style>
</head>
<body style="
	font-family: Georgia, Tahoma, Verdana;
    background: transparent url('<?php echo IMG;?>themes/material/bg_body.jpg') no-repeat top center;
	margin: 0 auto; 
	padding: 8px 0 35px 0;
	text-align: center; 
	color:#333;
	font-size:0.78em;
	line-height:1.5em">
<table width="80%" border="0" style="margin:20px auto 0 auto;">
  <tr>
    <th scope="row" style="padding:0px 0px 5px 0px">
	<a href="<?php echo URL::site();?>" title="<?php echo $site_name; ?>" style="width:auto;text-align:center;margin:0px auto 0px auto; color:#FFFFFF; text-decoration:none; font-weight:normal; text-shadow: 1.0px 1.0px 0.5px #966b19;">
  	<img alt="<?php echo $site_name; ?>" style="width:auto;max-width:100%;margin:0 auto;border:0;display:block;outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;" src="<?php echo IMG; ?>themes/content/logo.png"/>
    <?php echo $site_name; ?>
    </a> 
    </th>
  </tr>
  <tr>
    <th scope="row">
	<div style="
	background: #FFFFFF !important; 
   	height: auto;
	overflow: hidden;
    padding: 10px 20px;
	border-top-left-radius: 5px 5px !important;
	border-bottom-right-radius: 5px 5px !important;
	border-top-right-radius: 5px 5px !important;
	border-bottom-left-radius: 5px 5px !important;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<th scope="row" style="color:#666;font-size:10px;font-style:italic;font-weight:normal;float:none;text-align:right;position:relative;display:block;padding:0px 0 5px 0;"><?php echo date('l, F Y');?></th>
			</tr>
			<tr>
				<th scope="row" style="text-align:left;
										width:100%;
										background:#fff;
										margin:0px 0 0px 0; 
                                        padding:0px">
					<h2 style="
                    font-family: Georgia, 'Times New Roman', Times, serif;
                    font-size:19px;
					font-weight:normal;
					color:#666;
					float:none;
					margin: 0px;
					padding:0px;
					width:100%;
					text-transform:uppercase;">Contact us form</h2>
					</th>
			</tr>
			<tr>
				<th scope="row" style="text-align:left;padding:10px 0;margin:12px 0 0 0;color:#666;font-size:11px;font-weight:normal;display:block;clear:both;border-top:1px dotted #cccccc;">
				<!-- start #mainContent -->
				Dear Administrator, there is someone contacting from the web<br/><br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name : <?php echo $name; ?>,<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email : <?php echo $email; ?>,<br/>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Message : <?php echo $message; ?><br/><br/><br/>
				Thank you for your attention.
				<!-- end #mainContent -->
				</th>
			</tr>
			<tr>
				<th scope="row" style="
								display:block;
								clear:both;
								padding:10px 0 0px 0;
								border-top:1px dotted #ccc;
								color:#aaa;
								text-align:center;
								line-height:normal;
								font-size:0.900em;
								font-style:normal;
                                font-weight:normal;                                
                                ">
					<?php echo $site_name; ?>
				</th>
			</tr>
			</table>
		</div>
        </th>
  </tr>
  <tr>
	<th style="color:#FFFFFF; font-size:10px;text-shadow: 1.0px 1.0px 0.5px #966b19;">Design by			&nbsp;
	</th>
  </tr>
</table>
</body>
</html>