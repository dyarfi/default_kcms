<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?php echo BS_URL;?>favicon.ico" type="image/x-icon"/>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<meta name="robots" content="noindex, nofollow" />
<meta name="google" content="notranslate"/>
<meta name="keywords" content="<?php echo $meta_keywords;?>" />
<meta name="description" content="<?php echo $meta_description;?>" />
<meta name="copyright" content="<?php echo $meta_copyright;?>" />
<script type="text/javascript"> var base_URL = '<?php echo BS_URL; ?>';</script>
<?php foreach($styles as $key => $val) { echo HTML::style(CSS.$key, array('media'=>$val), TRUE, TRUE), "\n"; }?>
<?php foreach($scripts as $file) { echo HTML::script(JS.$file, NULL, TRUE), "\n"; }?>
<title><?php echo $page_title .' | '. SITE_NAME; ?></title>
<!--
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Developed by <?php echo DEVELOPER_NAME;?> ( <?php echo DEVELOPER_URL;?> )
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
Generated : <?php echo date('d/m/Y H:m:s') . "\n";?>
-->
</head>
<body class="<?php echo (!ACL::instance()->user) ? '' : ''; ?> ">
<div id="container">
	<div id="<?php echo (!ACL::instance()->user) ? '' : 'header'; ?>" class="row-fluid">	
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
					<span>
						<a class="brand" title="<?php echo I18n::get('admin_panel');?>" href="<?php echo URL::site(ADMIN);?>">
							<?php echo I18n::get('admin_panel');?> - <?php echo HTML::chars(COMPANY_NAME); ?>
						</a>
					</span>
				<?php if (ACL::instance()->user != '') : ?>
				<!--div class="spaceR10"-->
				
					<div class="rightDiv15"> 
						<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-list icon-white"></span>
						</button>
					</div>	
					<div class="nav-collapse collapse">
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<?php echo __(I18n::get('welcome_admin'),array('%admin'=> ucfirst(ACL::instance()->user->name))); ?>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo URL::site(ADMIN);?>/userdashboard/index" title="<?php echo I18n::get('dashboard');?>" class="">
											<i class="icon-tasks"></i>
											<?php echo I18n::get('dashboard');?></a>
									</li>	
									<li><a href="<?php echo URL::site(ADMIN);?>/user/view/<?php echo ACL::instance()->user->id;?>" title="<?php echo I18n::get('view_profile');?>" class="">
											<i class="icon-user"></i>
											<?php echo I18n::get('view_profile');?></a>
									</li>
									<li><a href="<?php echo URL::site(ADMIN);?>/authentication/logout" title="Logout" class="">
											<i class="icon-off"></i>
											<?php echo I18n::get('logout');?></a>
									</li>
									<li class="divider"></li>
									<li class="disabled">
										<a href="#">Last login : 
											<?php echo date('D, d M Y, H:i:s',ACL::instance()->user->last_login); ?>
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				<!--/div-->
				<?php endif;?>
			</div>
		</div>
		<div id="single_header">
			<?php if (ACL::instance()->user != '') : ?>
			<div class="logo_holder">
				<?php if(is_file(DOCROOT.'assets/images/themes/content/logo.png')) { ?>
				<a href="<?php echo URL::base(); ?>" title="<?php echo HTML::chars(COMPANY_NAME); ?>" target="_blank"><img src="<?php echo IMG . THM; ?>content/logo.png" title="<?php echo HTML::chars(COMPANY_NAME); ?>" alt="<?php echo HTML::chars(COMPANY_NAME); ?>" /></a>
				<?php } else { ?>
				<h2><?php echo HTML::chars(COMPANY_NAME); ?></h2>
				<?php } ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php if (ACL::instance()->user != '') { ?>
	<div class="container-fluid" id="admin_module">
		<div class="row-fluid">
			<div class="span3" id="side_menu">	
				<h3><i class="icon icon-list"></i>&nbsp;<?php echo I18n::get('menu_panel');?></h3>
				<div class="accordion" id="accordion">
					<?php
						$k=1;
						foreach (ACL::instance()->admin_system_modules() as $name => $functions) : ?>
						<?php if (is_array($functions) && count($functions) != 0) : ?>
						<div class="accordion-group">
							<div class="accordion-heading">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $k;?>">
								<?php echo $name; ?>
								</a>
							</div>
							<div id="collapse<?php echo $k;?>" class="accordion-body collapse <?php echo (stristr(Request::$current->controller(),strtolower(str_replace(' ', '_', $name)))) ? 'in' : ''; ?>">
								<div class="accordion-inner" id="menu_holder">
									<ul class="nav nav-list">
										<?php foreach ($functions as $row_function => $row_label) : ?>
											<?php if(ACL::instance()->user->level_id != 1 && $row_label == 'Levels') continue; ?>
											<li class="<?php echo preg_match('/\b'.Request::$current->controller().'\b/i', substr($row_function, 0, strpos($row_function, '/'))) ? 'current' : ''; ?>">
												<a href="<?php echo URL::site(ADMIN . $row_function); ?>"><?php echo $row_label; ?></a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php 
						$k++;
						endforeach; 
					?>
				</div>		
			</div>
			<div class="row-fluid">
				<div class="span9" id="admin_content">
					<?php if (isset($content)): echo $content; endif; ?>
				</div>
			</div>
		</div>	
	</div>
	<?php } else { ?>
	<div class="container-fluid grid_bg">
		<div id="login_content">
			<?php if (isset($content)): echo $content; endif; ?>
		</div>
	</div>
	<?php } ?>
	<div class="navbar navbar-fixed-bottom">
		<div class="navbar-inverse">
			<div class="row-fluid footer_holder">
				<div class="pull-left leftDiv20">
					<div class="left">
						<a href="#"><?php echo HTML::chars(COMPANY_NAME);?></a>
						<?php echo date('Y').' - All Right Reserved'; ?>
					</div>
				</div>
				<div class="pull-right rightDiv20">
				<?php if(Lib::config('admin.show_developed')) { ?>
					<div class="right">
						Content Management System developed by <a href="<?php echo HTML::chars(DEVELOPER_URL); ?>" target="_blank"><?php echo HTML::chars(DEVELOPER_NAME); ?></a>
					</div>
				<?php } ?>
				</div>
			</div>	
		</div>
	</div>	
</div>
<?php 
/** This is for loading the css and js from separated controller **/
// Load CSS
if (!empty($css)) {
	foreach( $css as $a) { echo HTML::style(ASSETS . $a, NULL, TRUE, TRUE), "\n"; }
}
// Load JAVASCRIPT
if (!empty($js)) {
	foreach( $js as $b) { echo HTML::script(ASSETS . $b, NULL, TRUE), "\n"; }
}
?>
<script type="text/javascript">
//<![CDATA[
var server = "<?php echo Kohana::$base_url; ?>";
var base_url="<?php echo URL::base(); ?>", site_url="<?php echo URL::site(); ?>";
<?php if (Session::instance()->get("acl_error") != '') : ?>
	jAlert("<?php echo Session::instance()->get_once("acl_error"); ?>", "Alert!");
<?php endif; ?>
<?php if (Session::instance()->get("auth_error") != '') : ?>
	jAlert("<?php echo Session::instance()->get_once("auth_error"); ?>", "Alert!");
<?php endif; ?>		
$(".auth_error, .acl_error").fadeIn(2000, function(){
	$(this).fadeOut(4000);
});
//]]>
</script>
</body>
</html>
