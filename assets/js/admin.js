var cur_order = 0, order_buffer = new Array(), top_menu = new Array(), top_menu_w = 0, cur_menu_pos = 0, is_animated = false;

$(document).ready(function() {
	
	jQuery(function(){jQuery(document).pngFix();});
	
	//$('#editor_one,#editor_two').ckeditor();
	// Ckeditor updating element instances
	// To avoiding autovalid submitted twice
	// Draggable and Droppable continues development
	/**************************************
	$(".listing_data tr").draggable({ revert: true, cursor: "move" });
	$(".listing_data tr").droppable({
            activeClass: "ui-state-hover",
            hoverClass: "ui-state-active",
            drop: function( event, ui ) {
                //$( this ).addClass( "ui-state-highlight" ).find( "p" ).html( "Dropped!" );
				alert('hi five');
            }
        });
	// Sort and drag tr table data
	$( "#sortable" ).sortable({
		revert: true
	});
	$( ".draggable" ).draggable({
		cursor: "move",
		connectToSortable: "#sortable",
		// helper: "clone",
		revert: "invalid"
	});
	$( "#sortable tr" ).disableSelection();
	//$( "ul, li" ).disableSelection();	
	**************************************/
	
	//$('.logo img').fadeIn();
	/*
	$('.typeahead').typeahead({
		items:10,
		minLength:1,
		source: function(query, process) {			
			return $.ajax({
				url: $(this)[0].$element[0].dataset.link,
				type: 'get',
				data: {query: query},
				dataType: 'json',
				success: function (result) {

					var resultList = result.map(function (item) {
						//alert(JSON.stringify(item));
						//var aItem = { id: item.Id, name: item.Name };
						//alert(item);
						//return JSON.stringify(aItem);
						//return JSON.stringify(item);
						return item;
					});

					return process(resultList);

				}
			});
		}
	});
	*/
   
   	$(".iframebox").colorbox();

	$('#file_upload').fileupload({
		dataType: 'json',
		done: function (e, data) {
            $.each(data.result.files, function (index, file) {
				$('<div class="pull-left img-thumbnail"/>')
				.html('<img src="'+base_URL + file.thumbnailUrl+'"//>'
					 +'<a href="'+file.deleteUrl+'" onclick="javascript:void(0);" title="Delete" class="label label-default">x</a>')
				.appendTo('.img_holder_xhr');
            });
        }
    });
	
   $('.toggletablechart').toggle(
		function () {
			$(this).closest('fieldset').find('table').fadeOut('fast');
		},
		function () {
			$(this).closest('fieldset').find('table').fadeIn('fast');
		}
	);	
   
	$('.listing_data tr').hover(
		function() {$(this).css('background','#a1dbff');},
		function() {$(this).css('background','');}
	);
		
	$('.listing_data tr').click( function() {	
		 // $(this).css('background','#a1dbff');
		 // alert('foobar');
	});
	
	$(".listing_data tr").mouseup(function(){
      $(this).css('background','#d5ffd5');
    }).mousedown(function(){
      $(this).css('background','#a1dbff');
    });
	
   /*
   	$('.listing_data tr').tooltip({
            position: {
                my: "center bottom-10",
                at: "center top",
                using: function( position, feedback ) {
                    $( this ).css( position );
                }
            }
        });
	*/
   
	//************************************** Text Editor *******************************************//
	$('.general').submit(function() {
        for (var i in CKEDITOR.instances) {
            CKEDITOR.instances[i].updateElement();
        }
    });
	//************************************** Ajax Requests *****************************************//
	
	$('.odr_up, .odr_down').click(function(){
		url = $(this).attr('href');		
		$.ajax({
			//url: base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
			type: 'GET',
			url:url,
			datatype: "JSON",
			async: false,
			success: function(msg){
				if (msg != '') {
					//jAlert("Please wait while updating data " + msg);
					url = location.pathname + '?current=' + msg;
					window.location = url;
				}                        
			},
			error: function (request,setting){
				jAlert('Failed to update..');    
			}
		});
		return false;
	});

	$('.odr_get').click(function(e){
		// Prevent Default
		e.preventDefault();
		
		// Get variable
		var url = $(this).attr('href');
		
		//alert(url);
		
		// Show preloader activities
		$.fancybox.showActivity();
		
		$.ajax({
			type: "POST",
			url: url,
			//data: "mods_id="+mods+"&lvel_id="+levl,
			cache: false,
			async: false,	
			success: function(msg){
				if (msg != undefined) {
					//conlose.log(msg);
					//item.parent().find('.result').show().html(msg);
					$.fancybox(msg).option({'padding':0});
				}
			},
			complete: function(msg) { },
			error: function(msg) {}
		});		
			
		// Hide preloader activities
		$.fancybox.hideActivity();
		return false;
	});
	
	
	//$('.order_set').click(function(){
		//alert($(this).attr('rel'));
	//});
	
	// Object File statistical hover
	$('.object_title').hover(function() {
		ttl = $(this).attr('alt');
		obj = $(this).attr('class');
		rel = $(this).attr('rel');
		//$(this).find('.floating_box').stop().hide('fast');
		//$(this).closest("a").find('.floating_box').hide('fast'); 
		$(this).stop().show('slow').append('<div class="floating_box"><img src="'+base_URL+'assets/images/system/ajax-loader.gif" /><div>');
		$.ajax({
			type: 'GET',
			url:rel,
			datatype: "JSON",
			async: false,
			cache: true,
			beforeSend: function() {
				// $('.object_title').closest("td a").find('.floating_box').stop().hide('fast'); 
				$('.floating_box').css('margin','-132px -200px 0 0 !important').empty();
			},
			success: function(result){
				if (result != '') {
					file = jQuery.parseJSON(result); 
					//alert(file.file_name);
					i = 1;
					$.each(file, function(i,file) {
						var div_data =
						"<div>"+file.file_name+ " | Hits : "+file.count+"</div>";
						$(div_data).appendTo(".floating_box");
						//$(div_data).appendTo.closest(".draggable");
					});
				}                        
			},
			error: function (request,setting){
				//alert('Failed to update..');    
			}
		});
		//$(this).append('<div class="floating_box"> File Name : '+file.file_name+'</div>');
	},function(){
		$(this).closest("td").find('.floating_box').stop().hide('fast');
		$(this).find('.floating_box').stop().hide('fast');
	});
	
	$('.ckeditor').ckeditor({ 
		// apply_source_formatting : true, //verify_html : true
	});
	
	$('.ckeditor400').ckeditor({
		width:800
		// apply_source_formatting : true,
		// verify_html : true
	});
	
	$('.ckeditorsmall').ckeditor({
		toolbar : 'Basic', 
		toolbar_Basic : [['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-','Source']]
//		apply_source_formatting : true,
//		verify_html : true
	});
	
	$('.zoom,a.zoom').each(function () {
		$(this).fancyZoom({directory: base_URL + 'assets/images/fancy_zoom/', scaleImg: true});
	});
	
    var currentYear = (parseInt)((new Date).getFullYear());
	Shadowbox.init({handleOversize: "drag", modal: true});
	
	// Function Listings
	/********* Tipsy Hover - start ********/
	$('.view, .edit, .delete, .list, .functions').tooltip({
		title: 'title', 
		placement: 'top',
		animation: true,
		offset: 2,
		delay: 100
		//trigger: 'hover'
	});
	
	$('.odr_up, .odr_down, .odr_get').tooltip({
		title: 'title', 
		placement: 'left',
		animation: true,
		offset: 2,
		delay: 100
		//trigger: 'hover'
	});	
	
	$('.download, .trash').tooltip({
		title: 'title', 
		placement: 'left',
		animation: true,
		offset: 2,
		delay: 100
		//trigger: 'hover'
	});	
   
	$('.autovalid').validate(/*{
		invalidHandler: function(form, validator) {
		  //var errors = validator.numberOfInvalids();
		  //if (errors) {
		  //if (validator.numberOfInvalids()) {
			//$(".lang_field_err").show();
		  //} else {
			//$(".lang_field_err").hide();
		  //}
		}
	 }*/);
	
	//$('.mod_id').click( function () {
		//var classname = $(this).closest('table').attr('id');
		//alert(classname);
		//$('table#'+classname).toggle();
	//});
	
	// --------------------- Used in Module Listing
	/*
	$('.lvl_id').change( function () {		
		var selected = $(this).parent().find('table tr td input[type="checkbox"]');
		if ($(this).attr('checked') === 'checked') {selected.attr('checked','checked');} 
		if ($(this).attr('checked') !== 'checked') {selected.removeAttr('checked');}
	});
	
	$('input[name^="module_permission"]').change( function () {
		if ($(this).attr('checked') !== 'undefined') {
			$(this).closest("table").parent("td").find('input.lvl_id').attr('checked','checked');	
		} else {
			$(this).closest("table").parent("td").find('input.lvl_id').attr('checked','');
		}
	});
	*/
	// --------------------- Used in Module Listing
	
	// --------------------- start --- Used in Module Listing
	$('.mod_func').each(function() {
		if ($(this).find('input[type="checkbox"]:checked').length == 0) {
			$(this).parent('.select_holder').find('.module_menu').prop('checked','');
		} 
	});	

	$('.module_menu').change( function () {		
		var selected = $(this).parent().find('table tr td input[class="check_hidden_mplr"]');
		if ($(this).attr('checked') === 'checked') { selected.prop('checked','checked'); } 
		if ($(this).attr('checked') !== 'checked') { selected.prop('checked',''); }	
	});
	
	$('input[class^="check_hidden_mplr"]').change( function () {
		if ($(this).prop('checked') == true) {
			$(this).closest("table").parent("td").find('input[name^="module_menu[level_id]"]').prop('checked','checked');	
		} else {
			$(this).closest("table").parent("td").find('input[name^="module_menu[level_id]"]').prop('checked','');
		}
	});	
	$('.fadeOut').fadeOut(3000);
	// --------------------- end --- Used in Module Listing
	
	// =========== Input Type Lookup Function =============
	/*
	$('#name').keyup(function() { 
		var title = $(this).val();
		var link = $(this).attr('rel');
		$.ajax({
			type: 'GET',
			url: base_URL+'admin-cp/'+link+'/check_title/'+title,
			data: 'ajax=true&field=title&hash='+title,
			datatype: "JSON",
			async: false,
			success: function(msg){
				//alert(msg); //if (msq == 0) { alert('Please wait while updating data'); }                        
			},
			error: function (request,setting){
			}
		});
		//var pid = $(this).attr('pid');
		//var hash = $(this).attr('hash');
		//
		//$.ajax({
			//type: 'GET',
			//url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
			//data: 'ajax=true&field=status&hash='+hash,
			//datatype: "JSON",
			//async: false,
			//success: function(msg){
				//if (starter == 0) {
					//alert('Please wait while updating data');    
				//}                        
			//},
			//error: function (request,setting){}
		//});
	});
	*/
   // =========== Input Type Lookup Function =============
   
    //$('form.auto_valid').validate(); 
	// Validate Your Form;
	//getErrorMessages
	//if ($('.error') == 'object') { alert($('.error')); }
	
    $('input.disactive').focus(function(){$(this).removeClass('disactive');});    
    /* Drop Down Helper */ /* */
	
    $('ul.main_navi li.dd').click(function(e){
        var id = $(this).attr('id');
        var options = {};
        if (id=='nexthide'){
            $('ul.main_navi li#nexthide ul').hide();
            $(this).removeAttr('id');
        }else{
            $('ul.main_navi li#nexthide ul').hide();
            $('ul.main_navi li#nexthide').removeAttr('id');
            $('ul.main_navi li:hover ul').slideDown('normal');
            $(this).attr('id','nexthide');
        }
    });
	
    $('ul.main_navi li.dl').mouseover(function(){$(this).css('color','#fff') ;});
	
    $('ul.main_navi li.dl').mouseout(function(){$(this).css('color','#000') ;});
	
    $('img.close_flying').click(function() {
        var target = $(this).attr('target');
        $('.'+target).fadeOut(300);
    });
	
    $('input[type="checkbox"]#ipt_checkall').click(function(){
        var value = $(this).attr('checked');
        if (value==true){
            $('input[type="checkbox"].ipt_tocheck').each(function(){
               $(this).attr('checked','true').addClass('activecheck'); 
            });
        }else{
            $('input[type="checkbox"].ipt_tocheck').each(function(){
               $(this).removeAttr('checked').removeClass('activecheck');
            });
        }
    });
	
    $('input[type="checkbox"].ipt_tocheck').click(function(){
        var value = $(this).attr('checked');
        if (value==true){
            $(this).attr('checked','true').addClass('activecheck');     
        }else{
            $(this).removeAttr('checked').removeClass('activecheck');
        }
    });
	
    $('select[name="status_listing_one_click"]').change(function(){
        var val  = $(this).val();
        var link = $(this).attr('url');
        var lastpid = $(this).attr('lastpid');
        if (val != 'select'){
            var starter = 0;
            var lengthtoupdate = parseInt($('input[type="checkbox"].ipt_tocheck.activecheck').length);
            $('input[type="checkbox"].ipt_tocheck.activecheck').each(function(){
                var pid = $(this).attr('pid');
                var hash = $(this).attr('hash');
                $.ajax({
                    type: 'GET',
                    url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                    data: 'ajax=true&field=status&hash='+hash,
                    datatype: "JSON",
                    async: false,
                    success: function(msg){
                        if (starter == 0) {
                            alert('Please wait while updating data');    
                        }                        
                    },
                    error: function (request,setting){
                    }
                });
                starter++; 
            });        
            if(starter==lengthtoupdate){
                //window.location.reload();
				window.location = window.location;
            };             
            //window.location.reload();
            /*
            obj.each(function(){
                var pid = $(this).attr('pid');
                var hash = $(this).attr('hash');
                var value = $(this).attr('checked');
                if (value==true) {
                    $.ajax({
                        type: 'GET',
                        url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                        data: 'ajax=true&field=status&hash='+hash,
                        datatype: "JSON",
                        success: function(msg){
                            if (lastpid == pid) { window.location.href=window.location.href }
                        },
                        error: function (request,setting){ }
                    });
                }
                index++;
            }); 
            */           
        }
    });
	
	$('input[type="radio"].default_listing_one_click').change(function(){
        var val  = $(this).attr('checked') ? 1 : 0;
        var link = $(this).attr('url');
		//alert(window.location);
        if (link){
                var pid = $(this).attr('pid');
                var hash = $(this).attr('hash');
                $.ajax({
                    type: 'GET',
                    url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                    data: 'ajax=true&field=default&hash='+hash,
                    datatype: "JSON",
                    async: false,
                    success: function(msg){
                        if (msg) {
                            alert('Please wait while updating data');    
                        }                        
                    },
                    error: function (request,setting){
                    }
                });
		  window.location = window.location;
          //window.location.reload();
        }
    });
	
	$('select[class="default_status_one_click"]').change(function(){
		   var pid = $(this).attr('pid');
           var val = $(this).val();
           var hash = $(this).attr('hash');
		   var link = $(this).attr('url');
           $.ajax({
                type: 'GET',
                url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                data: 'ajax=true&field=status&hash='+hash+'&id='+val,
                datatype: "html",
                success: function(msg){
					window.location = window.location;
                },
                error: function (request,setting){
                    alert(request+ ' ' + setting);
                }
            });
    });
	
	$('select[class="default_status_one_click_activated"]').change(function(){
		   var pid = $(this).attr('pid');
           var val = $(this).val();
           var hash = $(this).attr('hash');
		   var link = $(this).attr('url');
           $.ajax({
                type: 'GET',
                url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                data: 'ajax=true&field=activated&hash='+hash+'&id='+val,
                datatype: "html",
                success: function(msg){
					window.location = window.location;
                },
                error: function (request,setting){
                    alert(request+ ' ' + setting);
                }
            });
    });
	
	$('select[class="default_delete_one_click"]').change(function(){
		   var pid = $(this).attr('pid');
           var val = $(this).val();
           var hash = $(this).attr('hash');
		   var link = $(this).attr('url');
           $.ajax({
                type: 'GET',
                url:base_URL+'admin-cp/'+link+'/'+pid+'/'+val,
                data: 'ajax=true&field=is_deleted&hash='+hash+'&id='+val,
                datatype: "html",
                success: function(msg){
					window.location = window.location;
                },
                error: function (request,setting){
                    alert(request+ ' ' + setting);
                }
            });
    });
	
    $('.title').alphanumeric({allow:'-.,! '});
	
    $('textarea[name="description_model"]').counter({
            type: 'char',
            goal: 250,
            count: 'down'   
        }); 
	// if(typeof $('#title') == 'object' && $('#title').length != 0) {
		// if(typeof $('#subject') == 'object' && $('#subject').length != 0) {
			// //$('#name').parent('div').hide();
			// $('#subject').blur(function() {
				// var value = $(this).val();
				// //$('#name').val($.replace_permalink(value));
			// });
		// }
		// if(typeof $('#title') == 'object' && $('#title').length != 0) {
			// //$('#name').parent('div').hide();
			// $('#title').blur(function() {
				// var value = $(this).val();
				// //$('#name').val($.replace_permalink(value));
			// });
		// }			
		// $('#title').blur(function() {
			// var value = $('#title').val();
			// $('#title').val($.replace_permalink_dash(value));
		// });
	// }	
	
	// --------------------- imported from kohana 2
	$('.simpledate').datepicker({dateFormat: 'yy-mm-dd'});
	
	$('#validAfterDatepicker,#validBeforeDatepicker').datepicker({
		duration: '',
		showTime: false,
		constrainInput: true,
		stepMinutes: 1,
		stepHours: 1,
		dateFormat: 'yy-mm-dd',
		altTimeField: '',
		time24h: false
	});

	$('#validateFormDate').validate({ 
		errorPlacement: $.datepicker.errorPlacement, 
		rules: { 
				start_date: { 
					required: true,
					dpCompareDate: ['before', '#validAfterDatepicker'] 
				}, 
				end_date: { 
					required: true,
					dpCompareDate: {after: '#validBeforeDatepicker'} 
				}
			}, 
			messages: { 
				validFormatDatepicker: 'Please enter a valid date (yyyy-mm-dd)', 
				validRangeDatepicker: 'Please enter a valid date range', 
				validAfterDatepicker: 'Please enter a date after the previous value' 
			}
		}	
	);

/*
	if (typeof $('#type') == 'object') {
		$('#type').change(function () {
			if ($("select option:selected").val() == 'event') {
				$('.parent_event').attr('disabled','').show().parent('div').show();
				var parent_event = $('.parent_event').find('option'), id = '', item_parent = '';
				jQuery.each(parent_event, function (idx, val) {
					item_parent = $(this).attr('class').replace('parent_', '');
					if (item_parent == '') return;
					if ($(this).parent().find('option[value=' + item_parent + ']').attr('disabled'))
						$(this).attr('disabled', 'disabled');
				});
				$(this).parent().parent().parent().find('input[type="file"]').each(function(index) {
					//if($(this).attr('name') != 'image_1') {
						$(this).parent().parent('fieldset').show();$(this).attr('disabled','');
					//}
				});
			} else if ($("select option:selected").val() == 'announcement') {
				$('.parent_event').attr('disabled','').hide().parent('div').hide();
				// Find more than 1 input file, if find just used 1
				$(this).parent().parent().parent().find('input[type="file"]').each(function(index) {
					if($(this).attr('name') != 'image_1') {
						$(this).parent().parent('fieldset').hide();
						$(this).attr('disabled','disabled');
					}
				});
			} else {
				$('.parent_event').attr('disabled','disabled').hide().parent('div').hide();
			}
        })
        .trigger('change');
	}
	*/
   
	$('.parent_menu a.parent').each(function() {
		$(this).click(function() {
			if(!$(this).hasClass('active')) {
				$(this).addClass('active');
				$(this).parent().children('ul').slideDown('normal');
			} else {
				$(this).removeClass('active');
				$(this).parent().children('ul').slideUp('normal');
			}
			return false;
		});
		if($(this).hasClass('active')) {
			$(this).parent().children('ul').css('display', 'block');
		}
	});
	
	/*
	$('.parent_menu ul li a').each(function() {
		$(this).click(function() {
			$.ajax({
				type: "POST",
				url: base_URL + "admin-cp/ajaxmenu",
				data: "menu=" + $(this).parent().parent().parent().children('a').text() +"&current="+ $(this).text(),
				async: false
			});		
			//return false;
		});	
	});
	*/
   
	/** EOF **/
	if(typeof $('#name') == 'object' && $('#name').length != 0) {
		if(typeof $('#subject') == 'object' && $('#subject').length != 0) {
			//$('#name').parent('div').hide();
			$('#subject').blur(function() {
				var value = $(this).val();
				$('#name').val($.replace_permalink(value));
			});
		}
		if(typeof $('#title') == 'object' && $('#title').length != 0) {
			//$('#name').parent('div').hide();
			$('#title').blur(function() {
				var value = $(this).val();
				$('#name').val($.replace_permalink(value));
			});
		}
	}
	
	//================== Checking names lookup in server 
	
	/*
	$('.name').blur(function(){ 
		var name = $(this).val();
		var module = $(this).attr('rel');
		$.ajax({
			type: 'POST',
			url: base_URL+'admin-cp/'+module+'/check_name/',
			data: 'ajax=true&field=name&mod='+module+'&name='+name,
			datatype: "JSON",
			async: false,
			success: function(msg){
				//alert(msg);
				if (msg == 1) {
					$('.name').parent().find('span.red').html('Not Available');
					$('.name').parent().parent().find('input:image, input:submit').attr("disabled", true);
					//return false;
				} 
				if (msg == 0) {
					$('.name').parent().find('span.red').html('');
					$('.name').parent().parent().find('input:image, input:submit').attr("disabled", '');
					//return false;
				} 
			},
			error: function (request,setting){
			}
		});
	});
	*/
	
    /*
	if (typeof $('#drag_listing') == 'object') {
		$("#drag_listing").tableDnD({
			onDrop: function(table, row) {
				$.ajax({
					type: "POST",
					url: base_URL + 'admin-cp/content_page_ajax/',
					data: $.tableDnD.serialize(),
					success: function(msg){
						// success message
					}
				});			
			},
			dragHandle: "dragHandle"
		});
		$('#drag_listing tbody tr td.dragHandle').mousedown(function() {
			$(this).parent().css('background', '#D1D1D1').fadeTo('fast', 0.5);	
		});
		$('#drag_listing tbody tr td.dragHandle').mouseup(function() {
			$(this).parent().css('background', '#d5f6ff').fadeTo('fast', 1);	
		});
	}
	*/
   
	//====== Maintenance mode in setting listing
	$('form#maintenance_form').submit(function(){
		//$(":checked").val()
		var val  = $(this).find('input:checked').val();
        var link = $(this).attr('action');
		//alert(window.location);
		//alert(val);
        if (link){
                var pid = $(this).attr('pid');
                var hash = $(this).attr('hash');
                $.ajax({
                    type: 'POST',
                    url: link,
					data: 'ajax=true&field=default&mode='+val,
                    datatype: "JSON",
                    async: false,
                    success: function(msg){
                        if (msg == 1) {
                            alert('Please wait while updating data');    
                        }                        
						setInterval(window.location.reload(),3000);
                    },
                    error: function (request,setting){
                    }
                });
		  //window.location = window.location;
          //window.location.reload();
        }	
		return false;
	});
	
	if ($.getUrlVar('current')) {
		current_tr = $.getUrlVar('current');
		if (typeof($('.listing_data').find('#'+'row_' + current_tr)) == 'object') {
			$('.listing_data')
			.find('#'+'row_' + current_tr)
			.removeClass("listing_odd listing_even")
			.hide('fast')
			.show('fast')
			.addClass("green_row");
		}
	}
	
		// Emptied history data 
	$('.trash').click(function () {
		var uri		= $(this).attr('href');
		var ttl		= $(this).attr('rel');
		//var ttl		= $(this).attr('title');
		
		//return confirm('Are you sure want to delete this item?');
		
		jConfirm('Are you sure want to '+ttl+'?', 'Confirmation', function(r) {
			if(r) {
				$.ajax({
					type:'POST',
					url: uri,
					datatype: "JSON",
					async: false,
					error: function() {
						jAlert('Error while '+ttl+' data', 'Error');
					},
					success: function(result){
						//alert(result);
						if(result == 1) {
							jAlert('Data Emptied', 'Result');
							//jAlert("Please wait while updating data " + msg);
							url = uri.replace(/empty/gi, "");
							window.location = url;
						} else if(result == 0) {

							jAlert('Error while '+ttl+' data', 'Error');
							
						} else {
							jAlert(result, 'Error');

						}
					}
				});				
			} else {
				//if($(id).hasClass('red_row')) {
					//$(id).removeClass('red_row').fadeTo('fast', 1);
				//}
			}
			//jAlert('Confirmed: ' + r, 'Confirmation Results');
		});
		return false;
	});
	
	$('.delete, .delete_function').click(function () {
		var id		= jQuery($(this).parent().parent());
		var ttl		= $(this).attr('title') ? $(this).attr('title') : $(this).text();
		var uri		= $(this).attr('href');
		$(id).addClass('red_row').fadeTo('fast', 0.5);
//		return confirm('Are you sure want to delete this item?');
		jConfirm('Are you sure want to '+ttl+' this item?', 'Confirmation', function(r) {
			if(r) {
				$.ajax({
					type:'POST',
					url: uri,
					datatype: "JSON",
					async: false,
					error: function() {
						jAlert('Error while '+ttl+' data', 'Error');
						if($(id).hasClass('red_row')) {
							$(id).removeClass('red_row').fadeTo('fast', 1);
						}
					},
					success: function(result){
						//alert(result);
						if(result == 1) {
							$(id).remove();
							window.location.reload();
						} else if(result == 0) {
							//jAlert('Error while deleting data', 'Error');
							jAlert('Error while '+ttl+' data', 'Error');
							if($(id).hasClass('red_row')) {
								$(id).removeClass('red_row').fadeTo('fast', 1);
							}
						} else {
							jAlert(result, 'Error');
							if($(id).hasClass('red_row')) {
								$(id).removeClass('red_row').fadeTo('fast', 1);
							}
							//return false;
						}
					}
				});				
			} else {
				if($(id).hasClass('red_row')) {
					$(id).removeClass('red_row').fadeTo('fast', 1);
				}
			}
			//jAlert('Confirmed: ' + r, 'Confirmation Results');
		});
		return false;
	});
	
	$('.maintenance_mode').change(function(){
		if ($(this).val() == 1) 
			jAlert("The site will be temporary off in maintenance mode. Click Save to continue ", "Alert!");
	});
	
	$('#check_all').click(function () {
		var $checkboxes = $(this).parents('table').find('tbody').find(':checkbox');
		$checkboxes.attr('checked', this.checked);
	});
	
	$('#select_action').change(
		function () {
			$(this).parents('form').submit();
		}
	);
		
	if (typeof $('.form_details') == 'object' && $('.form_details').length != 0) {
		if (typeof $('.required') == 'object') {
			$('.required').each(function() {
				$(this).prev('label').append(' <span style="color:#f00">*</span>');
			});
		}
		var validate = $('.form_details').validate({
			rules: {
				confirm_password: {
					equalTo: "#password"
				}
			},
			messages: {
				confirm_password: {
					equalTo: "Please enter the same password as above"
				}
			}
		});
		if( typeof(accepted_type) != 'undefined' ) {
			accepted_type = accepted_type.replace(/,/gi, "|");
			$('#' + file_fields).rules("add", {
				accept: accepted_type,
				messages: {
					accept: jQuery.format("Please enter a value with {0} extension. ")
				}
 			});
		}
		if( typeof(audio_accepted_type) != 'undefined' ) {
			audio_accepted_type = audio_accepted_type.replace(/,/gi, "|");
			$('#' + audio_file_fields).rules("add", {
				accept: audio_file_fields,
				messages: {
					accept: jQuery.format("Please enter a value with {0} extension. ")
				}
 			});
		}
	}
	
	if (typeof $('.datepicker') == 'object' && $('.datepicker').attr('class') != null && $('.datepicker').length != 0) {
//		$('.datepicker').attr('readonly', true);
		$('.datepicker').datepicker({
			refresh: true,
			showTime: false,
			constrainInput: true,
			dateFormat: 'yy-mm-dd'
		 });
		 //$('.datepicker').datepicker();
	}
	
	if (typeof $('.timepicker') == 'object' && $('.timepicker').attr('class') != null && $('.timepicker').length != 0) {
		$('.timepicker').attr('readonly', true);
		$('.timepicker').datepicker({
			duration: '',
			showTime: true,
			constrainInput: false,
			stepMinutes: 1,
			stepHours: 1,
			dateFormat: 'yy-mm-dd',
			altTimeField: '',
			time24h: true
		 });
	}
	
	$('a.zoom').each(function () {
		$(this).fancyZoom({
			directory: base_URL + 'images/fancy_zoom/',
			scaleImg: true
		});
	});
	
	$('a.expand_fields_link').toggle(function () {
		var warper	= $(this).parent().parent().parent().find('.expand_fields');
		warper.slideDown('slow');
		$(this).html('Less');
	}, function () {
		var warper	= $(this).parent().parent().parent().find('.expand_fields');
		warper.slideUp('slow');
		$(this).html('More');
	});
	
	// print
	$('#print').click(function(e) {e.preventDefault();$("div#printArea").printArea();});
	if (typeof $('#order') == 'object' && $('#order').attr('id') != null) {
		cur_order = $('#order').val();
	}
	
	if (typeof $('#order') == 'object' && $('#order').attr('id') != null) {
		var categories = $('#order').find('option'), id = '', item_parent = '', i = 0, classes = new Array(), order_buffer = new Array();
		jQuery.each(categories, function (idx, val) {
				if (typeof($(this).attr('class')) !== 'undefined') {
				classes	= $(this).attr('class').split(' ');
				item_parent = classes[0].replace('category_', '');
				if ($(this).attr('selected'))
					cur_order = $(this).val();
				if (item_parent.length < 2 && classes.length < 2) return;
				if ($(this).parent().find('option[value=' + item_parent + ']').attr('disabled'))
					$(this).attr('disabled', 'disabled');
				order_buffer[i]	= {id : classes[1].replace('article_', ''),
									category_id : item_parent,
									subject : $(this).html(),
									order : $(this).val(),
									disabled : $(this).attr('disabled')
								};
				i++;
			}
		});
		if (typeof $('#order') == 'object' && $('#order').attr('id') != null) {
			$('#order').html('<option value="">&nbsp;</option>');
		}
		$.populate_order();
	}
	
	$('#category_id').change(
		function () {
			var options = $('#order').find('option');
			jQuery.each(options, function (idx, val) {
				item_parent = $(this).attr('class').replace('category_', '');
				if (item_parent == '') return;
				if ($(this).parent().find('option[value=' + item_parent + ']').attr('disabled'))
					$(this).attr('disabled', 'disabled');
			});
			$.populate_order();
		}
	);
		
	if (typeof $('#parent_id') == 'object' && $('#parent_id').attr('id') != null) {
		var parent_id = $('#parent_id').find('option'), id = '', item_parent = '';
		jQuery.each(parent_id, function (idx, val) {
			if(typeof($(this).attr('class')) !== 'undefined') {
				item_parent = $(this).attr('class').replace('parent_', '');
				if (item_parent == '') return;
				if ($(this).parent().find('option[value=' + item_parent + ']').attr('disabled'))
					$(this).attr('disabled', 'disabled');
			}
		});
		$.populate_order();
	}
	
	$('#parent_id').change(
		function () {
			$.populate_order();
		}
	);
	// --------------------- end imported from kohana 2
}) ;

// Getting with javascript url from
$.extend({
	populate_order: function () {
		var categories = '', parent_id = '', order = new Array(), item_parent = '', num = 1, html = '';
		if (typeof $('#parent_id') == 'object' && $('#parent_id').attr('id') != null) {
			categories = $('#parent_id').find('option');
			parent_id = $('#parent_id').val();
			html = '<option value="">&nbsp;</option>'
			if (parent_id == '') {
				$('#order').html(html);
				return;
			}
			order[0] = 'At the beginning';
			jQuery.each(categories, function (idx, val) {
				if(typeof($(this).attr('class')) !== 'undefined')
				if(item_parent == $(this).attr('class').replace('parent_', '')) {
					if ($(this).val() != parent_id && item_parent != '' && item_parent == parent_id) {
						order[num] = {id : $(this).val(),
									title : $(this).html().replace(/&nbsp;/g, ''),
									disabled : $(this).attr('disabled')
									}
						num++;
					}
				}
			});
		} else {
			categories = order_buffer;
			parent_id = $('#category_id').val();
			html = '<option value="">&nbsp;</option>'
			if (parent_id == '') {
				$('#order').html(html);
				return;
			}
			order[0] = 'At the beginning';
			jQuery.each(categories, function (idx, val) {
				if (val.category_id == parent_id) {
					order[num] = {id : val.id,
								title : val.subject,
								disabled : val.disabled
								};
					num++;
				}
			});
		}
		if (order.length == 1) {
			html += '<option value="1" ' + ((cur_order == 1) ? 'selected="selected"' : '') + '>At the beginning</option>';
		} else if (order.length > 1) {
			num = 1;
			jQuery.each(order, function (idx, val) {
				if (num == 2)
					html += '<optgroup label="After ...">';
				if (num == 1)
					html += '<option value="' + num + '" ' + ((num == cur_order) ? 'selected="selected"' : '') + '>' + val + '</option>';
				else
					html += '<option value="' + num + '" ' + ((num == cur_order) ? 'selected="selected"' : '') + ((val.disabled) ? ' disabled="disabled"' : '') + '>' + val.title + '</option>';
				num++;
			});
			html += '</optgroup>';
		}
		if (typeof $('#order') == 'object' && $('#order').attr('id') != null)
			$('#order').html(html);
	},
	replace_permalink: function(value) {
		var re 	= /[^a-z0-9]+/gi;
		var re2 = /^-*|-*$/g;
		value	= value.replace(re2, '').toLowerCase();
		value 	= value.replace(re, '-');
		return value;
	}
	,
	replace_permalink_dash: function (value) {
		var re 	= /[^a-z0-9]+/gi;
		var re2 = /^-*|-*$/g;
		value	= value.replace(re2, '').toLowerCase();
		value 	= value.replace(re, '_');
		return value;
	},
	getUrlVars: function(){
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
		}
		return vars;
	},
	getUrlVar: function(name){
		return $.getUrlVars()[name];
	}
});