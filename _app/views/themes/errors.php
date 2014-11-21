<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo i18n::$lang;?>">
<head>
<title><?php echo $page_title; ?></title>
<link rel="shortcut icon" href="<?php echo BS_URL;?>favicon.ico" type="image/x-icon" />
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<meta name="keywords" content="<? echo $meta_keywords; ?>" />
<meta name="description" content="<?php echo $meta_description;?>" />
<meta name="copyright" content="<?php //echo $meta_copyright;?>" />
<meta name="robots" content="all,index,follow" />
<meta name="googlebot" content="all,index,follow" />
<meta name="allow-search" content="yes" />
<meta name="audience" content="all" />
<meta name="revisit" content="2 days" />
<meta name="revisit-after" content="2 days" />
<meta name="author" content="Web Architect"/>
<!--meta name="copyright" content="&copy; 2011 Web Architect Jakarta, Indonesia" /-->
<meta name="creator" content="Web Architect indonesia" />
<meta http-equiv="Reply-to" content="info@webarq.com" />
<meta name="distribution" content="global" />
<meta name="document-classification" content="general" />
<meta name="rating" content="general" />
<!--
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Developed by <?php echo DEVELOPER_NAME;?> ( <?php echo DEVELOPER_URL;?> )
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 
Generated : <?php echo date('d/m/Y H:m:s') . "\n";?>
-->
<script type="text/javascript">
	var server = '<?php echo BS_URL;?>';
</script>
<?php foreach($styles as $file => $type) { echo HTML::style($file, array('media' => $type)), "\n"; }?>
<?php foreach($scripts as $file) { echo HTML::script($file, NULL, TRUE), "\n"; }?>

<!--[if IE 7]> <link href="css/styleIE7.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 8]> <link href="css/styleIE8.css" rel="stylesheet" type="text/css"> <![endif]-->
<!--[if IE 9]> <link href="css/styleIE9.css" rel="stylesheet" type="text/css"> <![endif]-->
</head>
<?php $current_page = Request::factory()->current()->controller(); ?>
<body id="<?php echo ($current_page != 'home') ? '' : 'homePage';?>">
		
	<div id="container">
		<div id="topPanel">
			<a href="index.php" class="logo" title="Logo Multi Bintang"><img src="<?php echo IMG; ?>themes/material/logo.png"/></a> 
			
			<div class="topPanelRight">
				<div class="linkToMainSite">
					<a href="#">PT Multi Bintang Indonesia Tbk</a>
				</div>
				
				<div class="mainMenu">
					<div class="navLeftBg">
						<div class="navMiddleBg">
							<ul>
								<li><a href="index.php" <?php //if($page=="page1") echo ' class="active"'?>>Home</a></li>
								<li><a href="discoverValue.php" <?php //if($page=="page2") echo ' class="active"'?>>Discover Multi Bintang</a></li>
								<li><a href="ourStar.php" <?php //if($page=="page3") echo ' class="active"'?>>Our Stars</a></li>
								<li style="padding-right:0px;"><a href="career.php" <?php //if($page=="page4") echo ' class="active"'?>>Career Opportunities</a></li>
							</ul>
						</div>
						<div class="navRightBg"></div>
					</div>
				</div>
			</div>
		</div>
				
<!-- Start Header Default Themes -->
<?php echo isset($header) ? $header : ''; ?>
<!-- End Header Default Themes -->

<!-- Start Content Body Default Themes -->
<?php echo $body; ?>
<!-- End Content Body Default Themes -->

<!--middlePanel-->
            <div id="middlePanel">
		<div class="contentTop"></div>
		
		<div class="contentMiddle">
			<div class="contentInside">
				<div class="homeLeft">
					<img src="<?php echo IMG; ?>themes/content/homeBanner.jpg" style="margin-top:-10px;"/>
					<p>We have been brewing a star quality beer since 1931. The principles of its success have remained the same: quality ingredients and a perfectly controlled brewing process.</p>
					<p>To establish the sustainable and winning organization, the best people is our most important ingredient. Providing rich experiences, diverse individual development and wide opportunity to grow are the way we are benefited from our people.</p>
					<p>The company’s approach towards sustainability is built on <br/> <span style="color:#dc021b; text-decoration:underline;">Multi Bintang Indonesia’s Values</span> – Passion for Quality, the Responsible Enjoyment of our Brands, Innovative Spirit  and a Respect for People, the Environment and the Society in which we live and work.</p>
					<p>We are passionate to make Multi Bintang Indonesia a great company, and also an enjoyable working experience to stimulate performance. We are fostering an effective leadership, as well as expect people to conduct in line with the <br/><span style="color:#dc021b; text-decoration:underline;">Multi Bintang Indonesia Leadership Competencies.</span></p>
					<p><br/><img src="<?php echo IMG; ?>themes/content/together.jpg"/></p>
				</div>
				
				<div class="homeRight">
					<div class="quotes">
						<div class="textQuote">
							"Kami merasa puas dengan hasil keuangan tahun ini, meskipun kami menghadapi lingkungan yang penuh tantangan. Tim Multi Bintang Indonesia dibangun untuk pertumbuhan masa depan yang sehat bagi Perseroan." 
							<p style="text-align:right;">Leo Evers - President Director</p>
						</div>
					</div>
			
                                        <!-- Latest News -->
					<div class="widgetRight" style="margin-bottom:10px;">
						<div class="LittleRoundedTop"></div>
						
						<div class="LittleRoundedMiddle">
							<div class="titleWidget"><h2>Latest News</h2></div>
							<ul class="listLatestNews">
								<li>
									<a href=""><img src="<?php echo IMG; ?>themes/content/imeNews01.jpg"/></a><br/>
									<a href="">Rumah Nongkrong Virtual Pertama di Indonesia</a>
									<p><span class="date">8 February 2012</span></p>
								</li>
								<li>
									<a href=""><img src="<?php echo IMG; ?>themes/content/imeNews02.jpg"/></a><br/>
									<a href="">Multi Bintang masih kuasai pasar</a>
									<p><span class="date">8 February 2012</span></p>
								</li>
								<li>
									<a href=""><img src="<?php echo IMG; ?>themes/content/imeNews03.jpg"/></a><br/>
									<a href="">Bir Bintang Raih Medali Emas </a>
									<p><span class="date">8 February 2012</span></p>
								</li>
								<li>
									<a href=""><img src="<?php echo IMG; ?>themes/content/imeNews04.jpg"/></a><br/>
									<a href="">IHSG Turun 17,779 poin Saham Multi Bintang MLBI naik Rp 9.000 </a>
									<p><span class="date">8 February 2012</span></p>
								</li>								
							</ul>
							<div class="moreBg"><a href="#">More news</a></div>
						</div>				
						<div class="LittleRoundedBottom"></div>
					</div> <!-- End Latest News -->
					
                                        <!-- Career -->
					<div class="widgetRight">
						<div class="LittleRoundedTop"></div>
						
						<div class="LittleRoundedMiddle">
							<div class="titleWidget"><h2>MBI Career</h2></div>
								<div class="insideCareer">
									<span style="font-size:14px; color:#333;">We are hiring! Please click on the position to view details of the job</span>
									<ul>
										<li>
											<a href="#">CORPORATE COMMUNICATIONS EXECUTIVE</a>
											<div class="compName">PT Multi Bintang Indonesia Jakarta		<span>31-Mar-12</span></div>
										</li>
										<li>
											<a href="#">SENIOR ACCOUNTANT</a>
											<div class="compName">PT Multi Bintang Indonesia Jakarta		<span>31-Mar-12</span></div>
										</li>
										<li>
											<a href="#">CORPORATE COMMUNICATIONS EXECUTIVE</a>
											<div class="compName">PT Multi Bintang Indonesia Jakarta		<span>31-Mar-12</span></div>
										</li>
										<li>
											<a href="#">SENIOR ACCOUNTANT</a>
											<div class="compName">PT Multi Bintang Indonesia Jakarta		<span>31-Mar-12</span></div>
										</li>										
									</ul>
								</div>
							<div class="moreBg"><a href="#">More Vacancies</a></div>
						</div>				
						<div class="LittleRoundedBottom"></div>
					</div> <!-- End Career -->
					
				</div>
			</div>
		</div>
<!-- Start Footer Default Themes -->		
		<div class="contentBottom"></div>
                <div class="hakcipta">
                        <ul class="bottomMenu">
                                <li><a href="index.php">Legal Notice</a></li>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">News</a></li>
                                <li style="border-right:none;"><a href="#">APB Career</a></li>
                        </ul><br/>

                        <p>Copyright 2012 © PT Multi Bintang Indonesia Tbk.  All Rights Reserved.</p>
                        <a title="Web Design Company Jakarta, Indonesia" href="http://www.webarq.com" target="_blank">Design by WEBARQ</a>
                </div>
<!-- End Footer Default Themes -->				
		<div class="clear"></div>
	</div>
         <!--middlePanel-->
            
	<script type="text/javascript">
		$(document).ready(function() {			
			$(".inline").colorbox({inline:true, width:"50%"});
			
			 $("h2.expand").toggler();
			 $(".collapse").first().css('display','block');
		});
	 </script>
</body>
</html>
