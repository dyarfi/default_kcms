/*
$(document).bind( 'pageinit', function() {
	var version = $.mobile.version || "dev",
		words = version.split( "-" ),
		ver = words[0],
		str = (words[1] || "Final"),
		html = ver,
		foothtml = "Version " + ver;

	if( str.indexOf( "rc" ) == -1 ){
		str = str.charAt( 0 ).toUpperCase() + str.slice( 1 );
	} else {
		str = str.toUpperCase().replace(".", "");
	}

	if ( $.mobile.version && str ) {
		html += " <b>" + str + "</b>";
		foothtml += " " + str;
	}
	
	alert('asdf');
	
	$( ".type-home .ui-content p.jqm-version" ).html( html );
	$( ".footer-docs p.jqm-version" ).html( foothtml );
});
*/

$(document).ready(function(){
	
		//alert('foobar');
	/*	
	$('.href_button').click(function(){
		url = $(this).attr('href');
		//rel = $(this).attr('rel');
		//alert('foobar');
		$.ajax({
			type: "GET",
			url: url,
			//data: rel,
			datatype: "JSON",
			async: false,
			success: function(msg){
				if (msg == 0) {
					alert('No data Available');    
				}                        
			},
			error: function (msg){

			}
		});
		return true;
	});
	*/
   
   /*
   	$('.jp-play, .jp-pause').tipsy({
		title: 'title', 
		gravity: 's',
		fade: true,
		offset: 2,
		opacity: 1
	});
	
	$('.download_file').tipsy({
		title: 'title', 
		gravity: 's',
		fade: true,
		offset: 2,
		opacity: 1
	});

	$('.btnMPAM').tipsy({
		title: 'title', 
		gravity: 'e',
		fade: true,
		offset: 2,
		opacity: 1
	});
	*/
   
	$('.popup_selector').click(function(){
		//alert('foobar');
	});
	
	$(".popup_selector").bind({
		popupbeforeposition: function() {
			$.ajax({
				type: "GET",
				transition: "slideup",
				data: 'ajax=true&rel='+rel/*,
				datatype: "JSON",
				async: false,
				success: function(msg){
					if (msg == 0) {
						alert('No data Available');    
					}                        
				},
				error: function (request,setting){

				}*/
			});	
		},
		popupafteropen: function(event, ui) { 
			alert('foobar');
		}
	});

	//$( ".popup_selector" ).popup({ history: true });
	
});
