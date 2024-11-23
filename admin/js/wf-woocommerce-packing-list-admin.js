(function( $ ) {
	'use strict';

	$(function() {

		$(".wt-tips").tipTip({'attribute': 'data-wt-tip'});

		/* filter documentation  */
		$('.wf_filters_doc_detail').filter(function(){ return $(this).find('.wf_filter_doc_eg').length>0; }).find('.wt_is_code_eg').css({'cursor':'pointer'});
		$('.wt_is_code_eg').on('click',function(e){
			e.stopPropagation();
			var eg_elm=$(this).parents('.wf_filters_doc_detail').find('.wf_filter_doc_eg');
			if(eg_elm.is(':visible'))
			{
				eg_elm.hide();
			}else
			{
				eg_elm.show();
			}
		});

		/* load address from wooo*/
		var load_address_from_woo_on_prg=0;
		$('.wf_pklist_load_address_from_woo').on('click',function(){	
			if(load_address_from_woo_on_prg==1){ return false; }
			load_address_from_woo_on_prg=1;
			var html_bck=$(this).html();
			$(this).html('<span class="dashicons dashicons-update-alt"></span> '+wf_pklist_params.msgs.please_wait+'...');
			$.ajax({
				type:'get',
				url:wf_pklist_params.ajaxurl,
				data:{'action':'wf_pklist_load_address_from_woo','_wpnonce':wf_pklist_params.nonces.wf_packlist},
				dataType:'json',
				success:function(data)
				{
					load_address_from_woo_on_prg=0;
					$('.wf_pklist_load_address_from_woo').html(html_bck);
					if(1 === data.status || "1" === data.status)
					{
						$('[name="woocommerce_wf_packinglist_sender_address_line1"]').val(data.address_line1);
						$('[name="woocommerce_wf_packinglist_sender_address_line2"]').val(data.address_line2);
						$('[name="woocommerce_wf_packinglist_sender_city"]').val(data.city);
						$('[name="wf_country"]').val(data.country);
						$('[name="woocommerce_wf_packinglist_sender_postalcode"]').val(data.postalcode);
					}else
					{
						wf_notify_msg.error(wf_pklist_params.msgs.error); 
					}
				},
				error:function()
				{
					load_address_from_woo_on_prg=0;
					$('.wf_pklist_load_address_from_woo').html(html_bck);
					wf_notify_msg.error(wf_pklist_params.msgs.error);
				}
			});
		});
		/* load address from wooo*/
		
		
		$('.wf_pklist_notice').on('click',function(){
			var ntce_vl=$(this).attr('data-pklist-notice-option');
			$.ajax({
				type:'get',
				data:{'wf_pklist_notice_dismiss':ntce_vl},
			});
		});

		/* documents tab settings button */
		$('.wf_pklist_dashboard_checkbox input[type="checkbox"]').change(function(){
			/* wf_documents_settings_toggle($(this)); */
		});
		$('.wf_pklist_dashboard_checkbox input[type="checkbox"]').each(function(){
			wf_documents_settings_toggle($(this));
		});

		function wf_documents_settings_toggle(elm)
		{
			var settings_btn=elm.parents('.wfte_doc_outter_div').find('.doc_module_link');
			if(elm.is(':checked'))
			{
				settings_btn.attr('href',settings_btn.attr('data-href')).css({'opacity':'1','cursor':'pointer'});
			}else
			{
				settings_btn.removeAttr('href').css({'opacity':'.5','cursor':'not-allowed'});	
			}
		}

		/* bulk action */
		$("#doaction, #doaction2").on('click',function(e){
			var actionselected=$(this).attr("id").substr(2);
	        var action = $('select[name="' + actionselected + '"]').val();
	        if($.inArray(action,wf_pklist_params.bulk_actions) !== -1 ) 
	        {
	        	e.preventDefault();
	        	var checked_orders=$('tbody th.check-column input[type="checkbox"]:checked');
	        	if(0 === checked_orders.length)
	        	{
	        		alert(wf_pklist_params.msgs.select_orders_first);
	        		return false;
	        	}else
	        	{
	        		var order_id_arr=new Array();
	        		checked_orders.each(function(){
	        			order_id_arr.push($(this).val());
	        		});

					var template_type = action.replace('print_','');
					template_type = template_type.replace('download_','');
					var confirmation_needed_ord = $('.wt_pklist_empty_number[data-template-type="'+template_type+'"]');
	        		var is_confirmation_needed=false;
	        		if(confirmation_needed_ord.length>0)
	        		{
	        			confirmation_needed_ord.each(function(){ /* check the confirmation needed orders are checked */
	        				var id=$(this).attr('data-id');
	        				if($.inArray(id,order_id_arr)!==-1)
	        				{
	        					is_confirmation_needed=true;
	        				}
	        			});
	        		}
					var action_url = wf_pklist_params.print_action_url + '&type=' + action + '&post=' + (order_id_arr.join(',')) + '&_wpnonce=' + wf_pklist_params.nonces.wf_packlist + '&wt-pdf-bulk=1';
					var is_this_print_button = ( ( -1 !== action_url.indexOf('type=print_') ) && ( -1 === action_url.indexOf( 'type=print_ubl') )  );
	        		if(is_confirmation_needed)
	        		{
	        			if(confirm(wf_pklist_params.msgs.invoice_not_gen_bulk))
						{
							if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
								window.open(action_url,'_blank');
							} else {
								do_print_document_in_admin_page( action_url, true);
							}
							setTimeout(function(){
								window.location.reload(true);  
							},1000);
                  		}
	        		}else
	        		{
						if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
							window.open(action_url,'_blank');
						} else {
							do_print_document_in_admin_page( action_url, true);	
						}	
	        		}
	        	}
	        }

		});

		/* Box packing - --------------------- */
		$('.woocommerce_wf_packinglist_boxes .insert').on('click',function () {
        var tbody = $('.woocommerce_wf_packinglist_boxes').find('tbody');
        var size = tbody.find('tr').size();
        var dimension_unit = $('#dimension_unit').val();
        var weight_unit = $('#weight_unit').val();
        var code = '<tr class="new">'
			+'<th class="check-column" style="padding: 0px; vertical-align: middle;"><input type="checkbox" /></th>'
	                +'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][name]" />'  + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][length]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][width]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][height]" /> ' + dimension_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][box_weight]" /> ' + weight_unit + '</td>'
				+'<td><input type="text" name="woocommerce_wf_packinglist_boxes[' + size + '][max_weight]" /> ' + weight_unit + '</td>'
				+'<td><input type="checkbox" name="woocommerce_wf_packinglist_boxes[' + size + '][enabled]" /></td>'
			+'</tr>';
	        tbody.append(code);
	        return false;
	    });
	    
	    $('.woocommerce_wf_packinglist_boxes .remove').on('click',function () {
	        var tbody = $('.woocommerce_wf_packinglist_boxes').find('tbody');
	        tbody.find('.check-column input:checked').each(function () {
	            $(this).closest('tr').hide().find('input').val('');
	        });
	        return false;
	    });
	    /* Box packing - --------------------- */


		$('#reset_invoice_button').on('click',function () {
	        $('[name=woocommerce_wf_invoice_start_number]').prop("readonly", false).css({'background':'#fff','width':'100%'});
	        var vl=$('[name=woocommerce_wf_invoice_start_number]').val()-1;
	        $('.wf_current_invoice_number').val(vl);
	        $(this).hide();
	    });
	    $('[name=woocommerce_wf_invoice_start_number]').on('input change',function(){
	    	var vl=$('[name=woocommerce_wf_invoice_start_number]').val()-1;
	    	$('.wf_current_invoice_number').val(vl);
	    });

	    /* hide tooltip menu on body click */
	    $('body').on('click',function(e){
	    	if(false === $(e.target).hasClass('wf_pklist_print_document'))
	    	{
	    		$('.wf-pklist-print-tooltip-order-actions').hide();
	    	}
	    });

	    /* tooltip action buttons in order listing page */
	    $('.wf_pklist_print_document').on('click',function(e){
	    	e.preventDefault();
	    	$('.wf-pklist-print-tooltip-order-actions').hide();
	    	var trgt=$(this).attr('href')
	    	trgt=trgt.replace('#','-');
	    	var trgt_elm=$('#wf_pklist_print_document'+trgt);
	    	if(trgt_elm.length>0)
	    	{
	    		var pos=$(this).position();
	    		var post=pos.top;
	    		var posl=pos.left;
	    		var w=(trgt_elm.width()+2)*-1;
	    		trgt_elm.css({'left':posl,'top':post,'margin-left':w+'px'}).show();
	    	}
	    });

	    var wf_tab_view=
	    {
	    	Set:function()
	    	{
	    		this.subTab();
	    		var wf_nav_tab=$('.wf-tab-head .nav-tab');
			 	if(wf_nav_tab.length>0)
			 	{
				 	wf_nav_tab.on('click',function(){
				 		var wf_tab_hash=$(this).attr('href');
				 		wf_nav_tab.removeClass('nav-tab-active');
				 		$(this).addClass('nav-tab-active');
				 		wf_tab_hash=wf_tab_hash.charAt(0)=='#' ? wf_tab_hash.substring(1) : wf_tab_hash;
				 		var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
				 		$('.wf-tab-content').hide();
				 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
				 		{	 			
				 			wf_tab_elm.fadeIn();
				 		}
				 	});
				 	$(window).on('hashchange', function (e) {
					    var location_hash=window.location.hash;
					 	if("" !== location_hash)
					 	{
					    	wf_tab_view.showTab(location_hash);
					    }
					}).trigger('hashchange');

				 	var location_hash=window.location.hash;
				 	if("" !== location_hash)
				 	{
				 		wf_tab_view.showTab(location_hash);
				 	}else
				 	{
				 		wf_nav_tab.eq(0).trigger('click');
				 	}		 	
				}
	    	},
	    	showTab:function(location_hash)
	    	{
	    		var wf_tab_hash = ('#' === location_hash.charAt(0)) ? location_hash.substring(1) : location_hash;
		 		if("" !== wf_tab_hash)
		 		{
		 			var wf_tab_hash_arr=wf_tab_hash.split('#');
		 			wf_tab_hash=wf_tab_hash_arr[0];
		 			var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
			 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
			 		{	 			
			 			$('a[href="#'+wf_tab_hash+'"]').trigger('click');
			 			if(wf_tab_hash_arr.length>1)
				 		{
				 			var wf_sub_tab_link=wf_tab_elm.find('.wf_sub_tab');
				 			if(wf_sub_tab_link.length>0) /* subtab exists  */
				 			{
				 				var wf_sub_tab=wf_sub_tab_link.find('li[data-target='+wf_tab_hash_arr[1]+']');
				 				wf_sub_tab.trigger('click');
				 			}
				 		}
			 		}
		 		}
	    	},
	    	subTab:function()
	    	{
	    		$('.wf_sub_tab li').on('click',function(){
					var trgt=$(this).attr('data-target');
					var prnt=$(this).parent('.wf_sub_tab');
					var ctnr=prnt.siblings('.wf_sub_tab_container');
					prnt.find('li a').css({'color':'#0073aa','cursor':'pointer'});
					$(this).find('a').css({'color':'#ccc','cursor':'default'});
					ctnr.find('.wf_sub_tab_content').hide();
					ctnr.find('.wf_sub_tab_content[data-id="'+trgt+'"]').fadeIn();
				});
				$('.wf_sub_tab').each(function(){
					var elm=$(this).children('li').eq(0);
					elm.trigger('click');
				});
	    	}
	    }
	    wf_tab_view.Set();

	});

})( jQuery );

var wf_settings_form=
{
	FormSubmission:function(form_elm)
	{
		if(!wf_settings_form.validate(form_elm))
		{
			return false;
		}
		proceed_form = true;
		if( true === proceed_form) {
			var settings_base	= form_elm.find('.wf_settings_base').val();
			var settings_action	= form_elm.find('.wf_settings_action').val();
			var data			= form_elm.serialize();
			var submit_btn		= form_elm.find('input[type="submit"]');
			var spinner			= submit_btn.siblings('.spinner');
			var current_form 	= form_elm;
			spinner.css({'visibility':'visible'});
			submit_btn.css({'opacity':'.5','cursor':'default'}).prop('disabled',true);	
			
			jQuery.ajax({
				url:wf_pklist_params.ajaxurl,
				type:'POST',
				dataType:'json',
				data:data+'&wf_settings_base='+settings_base+'&action='+settings_action+'&_wpnonce='+wf_pklist_params.nonces.wf_packlist,
				cache: false,
				success:function(data)
				{
					setTimeout(function() {
						spinner.css({'visibility':'hidden'});
						submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
						if(true === data.status)
						{
							if("invoice" === settings_base){
								jQuery("#reset_invoice_button").show();
								jQuery('[name=woocommerce_wf_invoice_start_number]').prop("readonly", true).css({'background':'#eee','width':'60%'});
							}
							
								wf_notify_msg.success(data.msg);
						
							wf_pklist_params.wt_plugin_data = data.saved_data;
						}else
						{
							wf_notify_msg.error(data.msg);
						}
					}, 2000);
				},
				error:function () 
				{
					spinner.css({'visibility':'hidden'});
					submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
					wf_notify_msg.error(wf_pklist_params.msgs.settings_error, false);
				}
			});
		}
	},
	Set:function()
	{
		jQuery('.wf_settings_form').find('[required]').each(function(){
			jQuery(this).removeAttr('required').attr('data-settings-required','');
		});

		jQuery('.wt_pklist_update_settings_btn').on('click',function(e) {
			e.preventDefault();
			var form_elm 		= jQuery(this).closest('.wf_settings_form');
			var template_type 	= form_elm.find('.wf_settings_base').val();
			var proceed 		= true;
			if ('invoice' === template_type && 'function' === typeof invoiceModuleFormCheck) {
				if( false === invoiceModuleFormCheck( form_elm )) {
					proceed = false;
				}
			}

			if( true === proceed ) {
				wf_settings_form.FormSubmission(form_elm);
			}
		});
	},
	validate:function(form_elm)
	{
		var is_valid=true;
		form_elm.find('[data-settings-required]').each(function(){
			var elm=jQuery(this);
			if("" === elm.val().trim())
			{
				var prnt=elm.parents('tr');
				var label=prnt.find('th label');
				
				var temp_elm=jQuery('<div />').html(label.html());
				temp_elm.find('.wt_pklist_required_field').remove();
				wf_notify_msg.error('<b><i>'+temp_elm.text()+'</i></b>'+wf_pklist_params.msgs.is_required);
				is_valid=false;
				return false;
			}			
		});

		form_elm.find('[min]').each(function () { 
			var elm = jQuery(this);
			if(elm.is(':visible') && typeof elm.attr('min') !== 'undefined' && elm.attr('min').trim() !== '' ) {
				if( elm.attr('min') > elm.val().trim() )
				{
					var prnt=elm.parents('tr');
					var label=prnt.find('th label');
					
					var temp_elm=jQuery('<div />').html(label.html());
					temp_elm.find('.wt_pklist_required_field').remove();
					wf_notify_msg.error('<b><i>'+temp_elm.text()+'</i> </b>'+wf_pklist_params.msgs.min_value_error+' '+elm.attr('min'));
					is_valid=false;
					return false;
				}	
			}
		});
		return is_valid;
	}
}

var wf_form_toggler=
{
	Set:function()
	{
		this.runToggler();
		jQuery('select.wf_form_toggle').change(function(){
			wf_form_toggler.toggle(jQuery(this));
		});
		jQuery('input[type="radio"].wf_form_toggle').on('click',function(){
			if(jQuery(this).is(':checked'))
			{
				wf_form_toggler.toggle(jQuery(this));
			}
		});
		jQuery('input[type="checkbox"].wf_form_toggle').on('click',function(){
			wf_form_toggler.toggle(jQuery(this),1);
		});
	},
	runToggler:function(prnt)
	{
		prnt=prnt ? prnt : jQuery('body');
		prnt.find('select.wf_form_toggle').each(function(){
			wf_form_toggler.toggle(jQuery(this));
		});
		prnt.find('input[type="radio"].wf_form_toggle, input[type="checkbox"].wf_form_toggle').each(function(){
			if(jQuery(this).is(':checked'))
			{
				wf_form_toggler.toggle(jQuery(this));
			}
		});
		prnt.find('input[type="checkbox"].wf_form_toggle').each(function(){
			wf_form_toggler.toggle(jQuery(this),1);
		});
	},
	toggle:function(elm,checkbox)
	{
		var vl=elm.val();
		var trgt=elm.attr('wf_frm_tgl-target');
		if("checkbox" === elm.attr('type')){
			if (elm.prop('checked'))
			{	
				var mrgin=15;
				jQuery('[wf_frm_tgl-id="'+trgt+'"]').show();
				jQuery('[wf_frm_tgl-id="'+trgt+'"]').find('th label').animate({'margin-left':mrgin+'px'});	
			}else{
				jQuery('[wf_frm_tgl-id="'+trgt+'"]').hide();
				jQuery('[wf_frm_tgl-id="'+trgt+'"]').find('th label').animate({'margin-left':'0px'});
			}
		}else{
			jQuery('[wf_frm_tgl-id="'+trgt+'"]').hide();
			jQuery('[wf_frm_tgl-id="'+trgt+'"]').find('th label').animate({'margin-left':'0px'});
		}

		if("none" !== elm.css('display')) /* if parent is visible. `:visible` method. it will not work on JS tabview */
		{
			var elms=this.getElms(elm,trgt,vl,checkbox);
			elms.show().find('th label').css({'margin-left':'0px'})
			elms.each(function(){
				var lvl=jQuery(this).attr('wf_frm_tgl-lvl');
				var mrgin=15;
				if (typeof lvl!== typeof undefined && lvl!== false) {
				    mrgin=lvl*mrgin;
				}
				jQuery(this).find('th label').animate({'margin-left':mrgin+'px'});
			});
		}

		/* in case of greater than 1 level */
		jQuery('[wf_frm_tgl-id="'+trgt+'"]').each(function(){
			wf_form_toggler.runToggler(jQuery(this));
		});
	},
	getElms:function(elm,trgt,vl,checkbox)
	{
		
		return jQuery('[wf_frm_tgl-id="'+trgt+'"]').filter(function(){
				if(jQuery(this).attr('wf_frm_tgl-val')==vl)
				{
					if(checkbox)
					{
						if(elm.is(':checked'))
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='true')
							{
								return true;
							}else
							{
								return false;
							}
						}else
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='false')
							{
								return true;
							}else
							{
								return false;
							}
						}
					}else
					{
						return true;
					}
				}else
				{
					return false;
				}
			});
	}
}
var wf_file_attacher={

	Set:function()
	{
		var file_frame;
		jQuery(".wf_file_attacher").on('click',function(event){
			event.preventDefault();
			if(jQuery(this).data('file_frame'))
			{
				
			}else
			{
				// Create the media frame.
				var file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery( this ).data( 'invoice_uploader_title' ),
					button: {
						text: jQuery( this ).data( 'invoice_uploader_button_text' ),
					},
					// Set to true to allow multiple files to be selected
					multiple: false
				});

				jQuery(this).data('file_frame',file_frame);
				var wf_file_target=jQuery(this).attr('wf_file_attacher_target');
				var wf_file_preview=jQuery(this).siblings('.wf_file_attacher_inner_dv').children('.wf_image_preview_small');
				var elm=jQuery(this);

				// When an image is selected, run a callback.
				jQuery(this).data('file_frame').on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment =file_frame.state().get('selection').first().toJSON();
					// Send the value of attachment.url back to shipment label printing settings form
					jQuery(wf_file_target).val(attachment.url);
					jQuery(".wt_logo_dismiss").show();
					if(wf_file_preview.length>0)
					{
						wf_file_preview.attr('src',attachment.url);
					}
				});
				// Finally, open the modal				
			}
			jQuery(this).data('file_frame').open();
		});

		jQuery('.wt_logo_dismiss').on('click',function(){
			var wf_file_target=jQuery(this).parents('.wf_file_attacher_inner_dv').siblings('.wf_file_attacher').attr('wf_file_attacher_target');
			jQuery(wf_file_target).val("");
			var wf_file_preview = jQuery(this).siblings('.wf_image_preview_small');
			wf_update_preview_img(wf_file_target,wf_file_preview);
		});

		function wf_update_preview_img(wf_file_target,wf_file_preview)
		{
			if("" === jQuery(wf_file_target).val() || "" === jQuery(wf_file_target).attr('value'))
			{ 	
				wf_file_preview.siblings(".wt_logo_dismiss").hide();
				wf_file_preview.attr('src',wf_pklist_params.no_image);
			}else
			{	
				wf_file_preview.siblings(".wt_logo_dismiss").show();
				wf_file_preview.attr('src',jQuery(wf_file_target).val());
			}
		}
		jQuery(".wf_file_attacher").each(function(){
			var wf_file_target=jQuery(this).attr('wf_file_attacher_target');
			var wf_file_preview=jQuery(this).siblings('.wf_file_attacher_inner_dv').children('.wf_image_preview_small');
			if(wf_file_preview.length>0)
			{ 
				wf_update_preview_img(wf_file_target,wf_file_preview);
				jQuery(wf_file_target).on('change',function(){
					wf_update_preview_img(wf_file_target,wf_file_preview);
				});
			}
		});
	}
}
var wf_notify_msg=
{
	error:function(message, auto_close)
	{
		var auto_close=(auto_close!== undefined ? auto_close : true);
		var er_elm=jQuery('<div class="notify_msg notify_msg_error"><div class="notify_msg_content"><svg class="notify_msg_content_icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="10" fill="#D63638"/><path d="M10.0996 5V11" stroke="white" stroke-width="2.2" stroke-linecap="round"/><circle cx="10.2" cy="15.2" r="1.2" fill="white"/></svg><span>'+message+'</span></div>');				
		this.setNotify(er_elm, auto_close);
	},
	success:function(message, auto_close)
	{
		var auto_close=(auto_close!== undefined ? auto_close : true);
		var suss_elm=jQuery('<div class="notify_msg notify_msg_success"><div class="notify_msg_content"><svg class="notify_msg_content_icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="10" r="10" fill="#20B93E"/><path d="M14.0931 7.21515L8.29143 13.0168L5.6543 10.3797" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>'+message+'</span></div>');				
		this.setNotify(suss_elm, auto_close);
	},
	setNotify:function(elm, auto_close)
	{
		jQuery('body').append(elm);
		elm.on('click',function(){
			wf_notify_msg.fadeOut(elm);
		});
		elm.stop(true,true).animate({'opacity':1,'top':'50px'},1000);
		if(auto_close)
		{
			setTimeout(function(){
				wf_notify_msg.fadeOut(elm);
			},5000);
		}else
		{
			jQuery('body').on('click',function(){
				wf_notify_msg.fadeOut(elm);
			});
		}
	},
	fadeOut:function(elm)
	{
		elm.animate({'opacity':0,'top':'100px'},1000,function(){
			elm.remove();
		});
	}
}

var wf_accord=
{
	Set:function()
	{
		jQuery('.wf_side_panel .wf_side_panel_hd').on('click', function (e) { 
			e.stopPropagation();

			// customizer promotion popup trigger.
			if ( jQuery(this).parents().hasClass('wt_pro_customizer_element') ) {
				jQuery('.wt_customizer_promotion_popup_btn').trigger('click');
			}

			if("wf_side_panel_hd" === e.target.className || 'dashicons dashicons-arrow-right' === e.target.className || 'dashicons dashicons-arrow-down' === e.target.className)
			{ 
				var elm = jQuery(this);
				var prnt_dv=elm.parents('.wf_side_panel');
				var cnt_dv = prnt_dv.find('.wf_side_panel_content');
				
				if(1 === prnt_dv.attr('data-disabled') || "1" === prnt_dv.attr('data-disabled'))
				{
					cnt_dv.hide();
					return false;
				}				
				if(cnt_dv.is(':visible'))
				{
					elm.find('.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
					cnt_dv.hide();
				}else
				{
					elm.find('.dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
					cnt_dv.show().css({'opacity':0}).stop(true,true).animate({'opacity':1});
				}
			}
		});
	}
}
var wf_color=
{
	Set:function()
	{
		jQuery('.wf-color-field').wpColorPicker({
	 		'change':function(event, ui)
	 		{
	 			jQuery(event.target).val(ui.color.toString());
	 			jQuery(event.target).trigger('click');
	 		}
	 	});
		jQuery('.wf-color-field').each(function(){
			jQuery('<input type="button" class="button button-small wf-color-default" value="Default">').insertAfter(jQuery(this).parents('.wp-picker-container').find('.wp-picker-clear'));
		});
		jQuery('.wf-color-default').on('click',function(){
			var inpt_fld=jQuery(this).parents('.wp-picker-container').find('.wf-color-field');
			var def_val=inpt_fld.attr('data-default');
			inpt_fld.val(def_val);
			inpt_fld.iris('color',def_val);
		});
	}
}
var wf_slideSwitch=
{
	Set:function()
	{
		jQuery('.wf_slide_switch').each(function(){
			jQuery(this).wrap('<label class="wf_switch"></label>');
			jQuery('<span class="wf_slider wf_round"></span>').insertAfter(jQuery(this));
		});
	}
};

var wt_pdf_field_group =
{
	Set:function()
	{
		//jQuery('.wt_iew_field_group_children').hide();
		jQuery('.wt_pklist_field_group_hd .wt_pklist_field_group_toggle_btn').each(function(){
			var group_id = jQuery(this).attr('data-id');
			var group_content_dv = jQuery(this).parents('tr').find('.wt_pklist_field_group_content');
			var visibility = jQuery(this).attr('data-visibility');
			jQuery('.wt_pklist_field_group_children[data-field-group="'+group_id+'"]').appendTo(group_content_dv.find('table'));
			if(1 === visibility || "1" === visibility)
			{
				group_content_dv.show();
			}
		});

		jQuery('.wt_pklist_field_group_hd').unbind('click').on('click',function(){

			var toggle_btn=jQuery(this).find('.wt_pklist_field_group_toggle_btn');
			var visibility=toggle_btn.attr('data-visibility');
			var group_content_dv=toggle_btn.parents('tr').find('.wt_pklist_field_group_content');
			if(1 === visibility || "1" === visibility)
			{
				toggle_btn.attr('data-visibility',0);
				toggle_btn.find('.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
				group_content_dv.hide();
			}else
			{
				toggle_btn.attr('data-visibility',1);
				toggle_btn.find('.dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
				group_content_dv.show();
			}
		});
	}
};

function wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(given_url,a)
{
	var url = '';
	url = given_url;
	
	var is_this_print_button = (-1 !== url.indexOf('type=print_') && (-1 === url.indexOf('type=print_ubl')));
	/*
	1 - invoice/proforma invoice number
	2 - invoice for free order
	3 - empty from address for invoice
	11 - creditnote number
	
	*/
    if((1 === a || "1" === a) || (2 === a || "2" === a) || (3 === a || "3" === a) || ("11" === a || 11 === a))
    {
    	if("2" === a || 2 === a){
    		var invoice_prompt = wf_pklist_params.msgs.invoice_number_prompt_free_order;
    	}else if("11" === a || 11 === a){
    		var invoice_prompt = wf_pklist_params.msgs.creditnote_number_prompt;
    	}else if("3" === a || 3 === a){
    		var invoice_prompt = wf_pklist_params.msgs.invoice_number_prompt_no_from_addr;
    		alert(invoice_prompt);
    		return false;
    	}else{
    		var msg_title=((1 === a || "1" === a) ? wf_pklist_params.msgs.invoice_title_prompt : a);
    		var invoice_prompt = msg_title+' '+wf_pklist_params.msgs.invoice_number_prompt;
    	}
		
		if(true === wf_pklist_params.msgs.pop_dont_show_again){
			url = url+'&wt_dont_show_again=1';
			window.open(url, '_blank');
			setTimeout(function () {
				window.location.reload(true);
			}, 1000);   
		}else{
			var elm=jQuery('.wt_doc_create_confirm_popup');
			if(jQuery('.wt_doc_create_confirm_popup').length === 0){
				if(confirm (invoice_prompt))
				{                         
					if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
						window.open(url, '_blank');
						setTimeout(function () {
							window.location.reload(true);
						}, 1000);   
					} else {
						do_print_document_in_admin_page(url,false,true);  
					}	
				} else {
					return false;
				}
			} else {
				
				// admin - order edit page print triggers
				elm.children().find('.message').html(invoice_prompt);
				elm.children().find('.wt_doc_create_confirm_popup_main,.wt_doc_create_confirm_popup_footer').show();
				wf_popup.showPopup(elm);

				jQuery('.wt_doc_create_confirm_popup_yes').on('click', function () {
					if(jQuery('#wt_dont_show_again_doc_create').is(':checked')){
						url = url+'&wt_dont_show_again=1';
					}

					if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) {
						jQuery('.wf_pklist_popup_cancel').trigger('click');
						window.open(url, '_blank');
						setTimeout(function () {
							window.location.reload(true);
						}, 1000);     
					} else {
						jQuery('.wf_pklist_popup_cancel').trigger('click');
						do_print_document_in_admin_page(url,false,true);  
					}
				});
			}
		}
    }
    else
	{
		if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
			window.open(url, '_blank');
			setTimeout(function () {
				window.location.reload(true);
			}, 1000);   
		} else {
			do_print_document_in_admin_page(url);
		}           
	}
    return false;
};

function wf_Confirm_Notice_for_Manually_Creating_Ubl_Invoicenumbers(given_url_ublinvoice,a)
{
	var url = '';
	url = given_url_ublinvoice;
	var is_this_print_button = (-1 !== url.indexOf('type=print_') && (-1 === url.indexOf('type=print_ubl')));
	/*
	1 - invoice/proforma invoice number
	2 - invoice for free order
	3 - empty from address for invoice
	11 - creditnote number
	
	*/
    if((1 === a || "1" === a) || (2 === a || "2" === a) || (3 === a || "3" === a) || ("11" === a || 11 === a))
    {
    	if("2" === a || 2 === a){
    		var invoice_prompt = wf_pklist_params.msgs.invoice_number_prompt_free_order;
    	}else if("11" === a || 11 === a){
    		var invoice_prompt = wf_pklist_params.msgs.creditnote_number_prompt;
    	}else if("3" === a || 3 === a){
    		var invoice_prompt = wf_pklist_params.msgs.invoice_number_prompt_no_from_addr;
    		alert(invoice_prompt);
    		return false;
    	}else{
    		var msg_title=((1 === a || "1" === a) ? wf_pklist_params.msgs.invoice_title_prompt : a);
    		var invoice_prompt = msg_title+' '+wf_pklist_params.msgs.invoice_number_prompt;
    	}
		
		if(true === wf_pklist_params.msgs.pop_dont_show_again){
			url = url+'&wt_dont_show_again=1';
			window.open(url, '_blank');
			setTimeout(function () {
				window.location.reload(true);
			}, 1000);   
		}else{
			var elm=jQuery('.wt_doc_create_confirm_popup_ublinvoice');
			if(jQuery('.wt_doc_create_confirm_popup_ublinvoice').length === 0){
				if(confirm (invoice_prompt))
				{                         
					if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
						window.open(url, '_blank');
						setTimeout(function () {
							window.location.reload(true);
						}, 1000);   
					} else {
						do_print_document_in_admin_page(url,false,true);  
					}	
				} else {
					return false;
				}
			} else {
				
				// admin - order edit page print triggers
				elm.children().find('.message').html(invoice_prompt);
				elm.children().find('.wt_doc_create_confirm_popup_main_ublinvoice,.wt_doc_create_confirm_popup_ublinvoice_footer').show();
				wf_popup.showPopup(elm);

				jQuery('.wt_doc_create_confirm_popup_yes_ublinvoice').on('click', function () {
					if(jQuery('#wt_dont_show_again_doc_create_ublinvoice').is(':checked')){
						url = url+'&wt_dont_show_again=1';
					}

					if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) {
						jQuery('.wf_pklist_popup_cancel').trigger('click');
						window.open(url, '_blank');
						setTimeout(function () {
							window.location.reload(true);
						}, 1000);     
					} else {
						jQuery('.wf_pklist_popup_cancel').trigger('click');
						do_print_document_in_admin_page(url,false,true);  
					}
				});
			}
		}
    }
    else
	{
		if (false === is_this_print_button || 'Yes' === wf_pklist_params.show_document_preview) { 
			window.open(url, '_blank');
			setTimeout(function () {
				window.location.reload(true);
			}, 1000);   
		} else {
			do_print_document_in_admin_page(url);
		}           
	}
    return false;
};

wf_doc_create = {
	Set:function(){
		jQuery('.wc-action-button').each(function(){
			if(jQuery(this).hasClass('wf_pklist_print_document_invoice_not_yet')){
				var url = jQuery(this).attr('href');
				jQuery(this).attr('onclick', "return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers('"+url+"','1')");
				jQuery(this).removeAttr('href');
			}else if(jQuery(this).hasClass('wf_pklist_print_document_invoice') || jQuery(this).hasClass('wf_pklist_print_document_packinglist')){
				jQuery(this).attr('target','_blank');
			}
		});
	}
}

wf_popup={
	Set:function()
	{
		this.regPopupOpen();
		this.regPopupClose();
		jQuery('body').prepend('<div class="wf_cst_overlay"></div>');
	},
	regPopupOpen:function()
	{
		jQuery('[data-wf_popup]').on('click',function(e){			
			var elm_class=jQuery(this).attr('data-wf_popup');
			var elm=jQuery('.'+elm_class);
			if(elm.length>0)
			{
				e.preventDefault();
				wf_popup.showPopup(elm);
			}
		});
	},
	showPopup:function(popup_elm)
	{
		var pw=popup_elm.outerWidth();
		var wh=jQuery(window).height();
		var ph=wh-150;
		popup_elm.css({'margin-left':((pw/2)*-1),'display':'block','top':'20px'}).animate({'top':'50px'});
		popup_elm.find('.wf_pklist_popup_body').css({'max-height':ph+'px','overflow':'auto'});
		jQuery('.wf_cst_overlay').show();
	},
	hidePopup:function()
	{
		jQuery('.wf_pklist_popup_close').trigger('click');
	},
	regPopupClose:function(popup_elm)
	{
		jQuery(document).keyup(function(e){
			if(27 === e.keyCode || "27" === e.keyCode)
			{
				wf_popup.hidePopup();
			}
		});
		jQuery('.wf_pklist_popup_close, .wf_pklist_popup_cancel').unbind('click').on('click',function(){
			jQuery('.wf_cst_overlay, .wf_pklist_popup').hide();
		});
	}
}

var wt_pklist_conditional_help_text=
{
	Set:function(prnt)
	{
		prnt=prnt ? prnt : jQuery('body');
		const regex = /\[(.*?)\]/gm;
		let m;
		prnt.find('.wt_pklist_conditional_help_text').each(function()
		{
			var help_text_elm=jQuery(this);
			var this_condition=jQuery(this).attr('data-wt_pklist-help-condition');
			if("" !== this_condition)
			{
				var condition_conf=new Array();
				var field_arr=new Array();
				while ((m = regex.exec(this_condition)) !== null)
				{
					/* This is necessary to avoid infinite loops with zero-width matches */
				    if(m.index === regex.lastIndex)
				    {
				        regex.lastIndex++;
				    }
				    condition_conf.push(m[1]);
				    condition_arr=m[1].split('=');
				    if(condition_arr.length>1) /* field value pair */
				    {
				    	field_arr.push(condition_arr[0]);
				    }
				}
				if(field_arr.length>0)
				{					
					var callback_fn=function()
					{
						var is_hide=true;
						var previous_type='';
						for(var c_i=0; c_i<condition_conf.length; c_i++)
						{
							var cr_conf=condition_conf[c_i]; /* conf */
							var conf_arr=cr_conf.split('=');
							if(conf_arr.length>1) /* field value pair */
							{ 
								if("field" !== previous_type)
								{
									previous_type='field';
									var elm=jQuery('[name="'+conf_arr[0]+'"]');
									var name_arr = 0;
									if(0 === elm.length)
									{
										elm=jQuery('#'+conf_arr[0]);
										if(0 === elm.length)
										{
											var elm=jQuery('[name="'+conf_arr[0]+'[]"]');
											name_arr = 1;
										}
									}
									var vl='';
									if("input" === elm.prop('nodeName').toLowerCase() && "radio" === elm.attr('type'))
									{	
										vl=jQuery('[name="'+conf_arr[0]+'"]:checked').val();
										if(1 === name_arr || "1" === name_arr){
											vl=jQuery('[name="'+conf_arr[0]+'[]"]:checked').val();
										}
									}
									else if("input" === elm.prop('nodeName').toLowerCase() && "checkbox" === elm.attr('type'))
									{
										if(elm.is(':checked'))
										{
											vl=elm.val();
										}
									}else
									{
										vl=elm.val();
									}
									is_hide=(vl==conf_arr[1] ? false : true);
								}
							}else /* glue */
							{
								if("glue" !== previous_type)
								{
									previous_type='glue';
									if("OR" === conf_arr[0])
									{
										if(false === is_hide) /* one previous condition is okay, then stop the loop */
										{
											break;
										}

									}else if("AND" === conf_arr[0])
									{
										if(true === is_hide && c_i>0) /* one previous condition is not okay,  then stop the loop */
										{
											break;
										} 
									}
								}
							}
						}
						if(is_hide)
						{
							help_text_elm.hide();
						}else
						{
							help_text_elm.css({'display':'inline-block'});
						}
					}
					callback_fn();
					for(var f_i=0; f_i<field_arr.length; f_i++)
					{
						var elm=jQuery('[name="'+field_arr[f_i]+'"]');
						if(0 === elm.length)
						{
							elm=jQuery('#'+field_arr[f_i]);
							if(0 === elm.length)
							{
								var elm=jQuery('[name="'+field_arr[0]+'[]"]');
							}
						}
						if("radio" === elm.prop('nodeName') || "checkbox" === elm.prop('nodeName'))
						{
							elm.on('click', callback_fn);
						}else
						{
							elm.on('change', callback_fn);
						}
					}
				}
			}
		});
	}
}

var wt_pklist_sys_info_table = {
	Set:function()
	{	
		jQuery('#sys_info_copy').on('click',function(){
			var temp = jQuery("<textarea>");
			var brRegex = /<br\s*[\/]?>/gi;
			jQuery("body").append(temp);
			temp.val(jQuery('#wt_sys_info_box').html().replace(brRegex, "\r\n")).select();
			document.execCommand("copy");
			temp.remove();
			jQuery("#wt_sys_info_copied").fadeIn().delay(3000).fadeOut();
		});
	}
}

var wt_pklist_filter_document_search = {
	Set:function(){
		jQuery("#filter_search_input_updated").on("keyup", function() {
		    var value = jQuery(this).val().toLowerCase();
		    jQuery("#wf_filters_doc_table_updated tr.view").filter(function() {
		    	if(jQuery(this).text().toLowerCase().indexOf(value) > -1){
		    		jQuery('.filter_category').show();
		    		jQuery(this).show();
		    	}else{
		    		jQuery('.filter_category').hide();
		    		jQuery(this).hide();
		    	}
	    	});
	  	});
	}
}

var wt_pklist_filter_actions = {
	Set:function(){
		jQuery(".fold-table tr.view").on("click", function(){
			jQuery(this).toggleClass("open").next(".fold").toggleClass("open");
			jQuery(this).children('td.filter_actions').children().toggleClass("filter_hide");
		});
		
		jQuery('.wf_filter_code_div').on('mouseover',function(){
			jQuery(this).children('.filter_code_copy').css('opacity','1.0');
		});
		jQuery('.wf_filter_code_div').on('mouseout',function(){
			jQuery(this).children('.filter_code_copy').css('opacity','0.5');
		});
		jQuery('.filter_code_copy').on('click',function(){
			var elm = jQuery(this);
			var copy_element = elm.parent().parent().children('er').html();
			wt_pklist_filter_actions.do_copy(copy_element);
		});

		jQuery('.do_copy_element').on('click',function(){
			var coupon_elm = jQuery(this);
			var coupon_copy_element = coupon_elm.attr('data-copy-text');
			wt_pklist_filter_actions.do_copy(coupon_copy_element);
		});
	},

	do_copy:function(text_to_be_copied){
		if(navigator.clipboard){
			navigator.clipboard.writeText(text_to_be_copied)
			.then(function() {
				wf_notify_msg.success(wf_pklist_params.msgs.fitler_code_copied);
			})
			.catch(function(error) {
				console.error(error);
			});
		}else{
			var tempInput = document.createElement('input');
			tempInput.setAttribute('value', text_to_be_copied);
			document.body.appendChild(tempInput);
			tempInput.select();

			try {
				document.execCommand('copy');
				wf_notify_msg.success(wf_pklist_params.msgs.fitler_code_copied);
			} catch (err) {
				console.error('Error copying text:', err);
			}
			document.body.removeChild(tempInput);
		}
	}
}

var wf_popover={
	Set:function()
	{
		this.remove_duplicate_content_container();
		jQuery('[data-wf_popover="1"]').on('click',function(){
			var cr_elm=jQuery(this);
			if(1 === cr_elm.attr('data-popup-opened') || "1" === cr_elm.attr('data-popup-opened'))
			{
				var pp_elm=jQuery('.wf_popover');
				var pp_lft=pp_elm.offset().left-50;
				jQuery('[data-wf_popover="1"]').attr('data-popup-opened',0);
				pp_elm.stop(true,true).animate({'left':pp_lft,'opacity':0}, 300,function(){
					jQuery(this).css({'display':'none'});
				});
				return false;
			}else
			{
				jQuery('[data-wf_popover="1"]').attr('data-popup-opened', 0);
				cr_elm.attr('data-popup-opened',1);
			}
			if(0 === jQuery('.wf_popover').length)
			{
				var template='<div class="wf_popover"><h3 class="wf_popover-title"></h3><span class="wt_popover_close_top popover_close" title="'+wf_pklist_params.msgs.close+'">X</span>'
				+'<form class="wf_popover-content"></form><div class="wf_popover-footer">'
				+'<button name="wt_pklist_custom_field_btn" type="button" id="wt_pklist_custom_field_btn" class="button button-primary">'+wf_pklist_params.msgs.save+'</button>'
				+'<button name="popover_close" type="button" class="button button-secondary popover_close">'+wf_pklist_params.msgs.close+'</button>'
				+'<span class="spinner" style="margin-top:5px"></span>'
				+'</div></div>';
				jQuery('body').append(template);
				wf_popover.regclosePop();
				wf_popover.sendData();
			}
			
			var pp_elm=jQuery('.wf_popover');
			var action_field='<input type="hidden" name="wt_pklist_settings_base" value="'+cr_elm.attr('data-module-base')+'"  />';
			var pp_html='';
			var pp_html_cntr=cr_elm.attr('data-content-container');
			if(typeof pp_html_cntr!==typeof undefined && pp_html_cntr!==false)
			{
				pp_html=jQuery(pp_html_cntr).html();
			}else
			{
				pp_html=cr_elm.attr('data-content');
			}
			pp_elm.css({'display':'block'}).find('.wf_popover-content').html(pp_html).append(action_field);
			pp_elm.find('.wf_popover-footer').show();
			var cr_elm_w=cr_elm.width();
			var cr_elm_h=cr_elm.height();
			var pp_elm_w=pp_elm.width();
			var pp_elm_h=pp_elm.height();
			var cr_elm_pos=cr_elm.offset();
			var cr_elm_pos_t=cr_elm_pos.top-((pp_elm_h-cr_elm_h)/2);
			var cr_elm_pos_l=cr_elm_pos.left+cr_elm_w;	    			
			pp_elm.find('.wf_popover-title').html(cr_elm.attr('data-title'));			
			pp_elm.css({'display':'block','opacity':0,'top':cr_elm_pos_t+5,'left':cr_elm_pos_l}).stop(true,true).animate({'left':cr_elm_pos_l+50,'opacity':1});
			jQuery('[name="wt_pklist_custom_field_btn"]').data({'select-elm' : cr_elm.parents('.wf_select_multi').find('select'), 'field-type':cr_elm.attr('data-field-type')});
		});
	},
	remove_duplicate_content_container:function()
	{
		jQuery('[data-wf_popover="1"]').each(function(){
			var cr_elm=jQuery(this);
			var pp_html_cntr=cr_elm.attr('data-content-container');
			var container_arr=new Array();
			if(typeof pp_html_cntr!==typeof undefined && pp_html_cntr!==false)
			{
				if(jQuery.inArray(pp_html_cntr, container_arr)==-1)
				{
					container_arr.push(pp_html_cntr);
					if(jQuery(pp_html_cntr).lenth>1)
					{
						jQuery(pp_html_cntr).not(':first-child').remove();
					}
				}			
			}
		});
	},
	sendData:function()
	{	    		
		jQuery('[name="wt_pklist_custom_field_btn"]').on('click',function(){
	        
	        var empty_fields=0;
	        jQuery('.wf_popover-content input[type="text"]').each(function(){
	        	if((1 === jQuery(this).attr('data-required') || "1" === jQuery(this).attr('data-required')) && "" === jQuery(this).val().trim())
	        	{
					empty_fields++;
	        	}
	        });
	        jQuery('.wf_popover-content select').each(function(){
	        	if((1 === jQuery(this).attr('data-required') || "1" === jQuery(this).attr('data-required')) && "" === jQuery(this).val().trim())
	        	{
					empty_fields++;
	        	}
	        });
	        if(empty_fields>0){
	        	alert(wf_pklist_params.msgs.enter_mandatory_fields);
	        	jQuery('.wf_popover-content input[type="text"]:eq(0)').focus();
	        	return false;
	        }
	        var elm=jQuery(this);
	        var sele_elm=elm.data('select-elm');
	        jQuery('.wf_popover-footer .spinner').css({'visibility':'visible'});
	        jQuery('.wf_popover-footer .button').attr('disabled','disabled').css({'opacity':.5});
	        var data=jQuery('.wf_popover-content').serialize();

	        data+='&action=wf_pklist_advanced_fields&_wpnonce='+wf_pklist_params.nonces.wf_packlist+'&wt_pklist_custom_field_btn&wt_pklist_custom_field_type='+elm.data('field-type');	
	        jQuery.ajax({
				url:wf_pklist_params.ajaxurl,
				data:data,
				dataType:'json',
            	type: 'POST',
				success:function(data)
				{
					jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
					jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
					if(true === data.success || "true" === data.success)
					{
						if("" !== data.old_meta_key){
							sele_elm.select2("destroy");
							sele_elm.find('option[value="'+data.old_meta_key+'"]').text(data.val);
							sele_elm.find('option[value="'+data.old_meta_key+'"]').val(data.key);
							sele_elm.find('option[value="'+data.key+'"]').prop('selected',true);
							sele_elm.select2();
						}else{
							var newOption = new Option(data.val,data.key, true, true);
							sele_elm.append(newOption).trigger('change');
						}
						jQuery(document).find('.wt_pklist_custom_field_form').find('[name="wt_pklist_new_custom_field_title"]').attr('value',data.new_meta_label);
						jQuery(document).find('.wt_pklist_custom_field_form').find('[name="wt_pklist_new_custom_field_key"]').attr('value',data.key);
						jQuery(document).find('.wt_pklist_custom_field_form').find('.wfte_custom_field_tab_head_title').text(wf_pklist_params.msgs.buy_pro_prompt_edit_order_meta);
						jQuery(document).find('.wt_pklist_custom_field_form').find('.wt_add_new_pro_tab').show();
						jQuery(document).find('.wf_popover-content').find('.wfte_custom_field_tab_head_title').text(wf_pklist_params.msgs.buy_pro_prompt_edit_order_meta);
						jQuery(document).find('.wf_popover-content').find('.wt_add_new_pro_tab').show();
						jQuery(document).find('.wt_pklist_custom_field_form').find('.wt_pklist_custom_field_form_notice').text(wf_pklist_params.msgs.buy_pro_prompt_edit_order_meta_desc);
						jQuery(document).find('.wf_popover-content').find('.wt_pklist_custom_field_form_notice').text(wf_pklist_params.msgs.buy_pro_prompt_edit_order_meta_desc);
					}else
					{
						alert(data.msg);
						jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
						jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
					}
				},
				error:function()
				{
					jQuery('.wf_popover-footer .spinner').css({'visibility':'hidden'});
					jQuery('.wf_popover-footer .button').removeAttr('disabled').css({'opacity':1});
				}
			});
		});
	},
	regclosePop:function()
	{
		jQuery('.nav-tab ').on('click',function(){
			jQuery('.wf_popover').css({'display':'none'});
		});
		jQuery('.popover_close').on('click',function(){
			wf_popover.closePop();
		});
	},
	closePop:function()
	{
		var pp_elm=jQuery('.wf_popover');
		if(pp_elm.length>0)
		{
			var pp_lft=pp_elm.offset().left-50;
			jQuery('[data-wf_popover="1"]').attr('data-popup-opened',0);
			pp_elm.stop(true,true).animate({'left':pp_lft,'opacity':0},300,function(){
				jQuery(this).css({'display':'none'});
			});
			jQuery('.wfte_pro_order_meta_alert_box').hide();
		}
	}
}

var wt_save_button_fixed = {
	Set:function(){
		if("ltr" === wf_pklist_params.is_rtl){
			wt_save_button_fixed.checkOffset();
			jQuery(document).scroll(function() {
			    wt_save_button_fixed.checkOffset();
			});
		}
	},
	checkOffset:function(){

		if(jQuery(document).find('.nav-tab-active').length !== 0){
			var wf_tab_hash = jQuery(document).find('.nav-tab-active').attr('href');
			wf_tab_hash=wf_tab_hash.charAt(0)=='#' ? wf_tab_hash.substring(1) : wf_tab_hash;
			var wf_tab_elm=jQuery('div[data-id="'+wf_tab_hash+'"]');
			
			if(wf_tab_elm.length>0)
	 		{
 				if(wf_tab_elm.find(".wf-plugin-toolbar").length != 0) {

				  	if(wf_tab_elm.find('.wf-plugin-toolbar').offset().top + wf_tab_elm.find('.wf-plugin-toolbar').height() >= wf_tab_elm.find('.end_wf_setting_form').offset().top){
						wf_tab_elm.find(".wf-plugin-toolbar.bottom").css({'right':'0','bottom':'0','position':'relative','background':'#f5f5f5','border-top': '1px solid #ddd'});
						wf_tab_elm.find(".wf-plugin-toolbar").children().find(".button-primary").css({'box-shadow':'none'});
					}
					
				    if(jQuery(document).scrollTop() + window.innerHeight < wf_tab_elm.find('.end_wf_setting_form').offset().top){
				    	if(wf_tab_elm.parents().find('.wf_general_settings_form').length !== 0){
				    		wf_tab_elm.find(".wf-plugin-toolbar.bottom").css({'right':'2%'});
				    	}else{
				    		wf_tab_elm.find(".wf-plugin-toolbar.bottom").css({'right':'0%'});
				    	}
				        wf_tab_elm.find(".wf-plugin-toolbar.bottom").css({'bottom':'0','position':'fixed','background':'transparent','border':'none','width':'auto'});
				        wf_tab_elm.find(".wf-plugin-toolbar").children().find(".button-primary").css({'box-shadow':' 0px 4px 10px rgba(122, 141, 159, 0.7)'});
				    }
				}
	 		}
		}
	}
}

function order_meta_add_buy_pro(){

	jQuery('.wfte_pro_order_meta_alert_box').show(function(){
		jQuery('.wf_popover').addClass('wf_popover_expand');
	});

	setTimeout( function(){
		jQuery('.wfte_pro_order_meta_alert_box').hide();
		jQuery('.wf_popover').removeClass('wf_popover_expand');
	}, 1000*10);
}

function do_auto_complete(){
	var order_meta_val_arr = JSON.parse(wf_pklist_params.order_meta_autocomplete);
	jQuery(document).find(".wt_pklist_new_custom_field_key").autocomplete({
	   	source: order_meta_val_arr,
		autoFill: true,
        select: function (event, ui) {
        	console.log(ui.label);
        },
	});
}

var wt_pklist_module_enable_disable ={
	Set:function(){
		jQuery('.wt_document_module_enable').on('change',function(){
			var elm = jQuery(this);

			if(elm.is(':checked')){
				var doc_module_set = 1;
			}else{
				var doc_module_set = 0;
			}
			var doc_module_name = elm.attr('id');
			jQuery.ajax({
				type:'POST',
				url:wf_pklist_params.ajaxurl,
				data:{'action':'wf_document_module_enable_disable','_wpnonce':wf_pklist_params.nonces.wf_packlist,'doc_module_name':doc_module_name,'doc_module_set':doc_module_set},
				dataType:'json',
				success:function(data)
				{
					if((true === data.status || "true" === data.status) && (1 === data.doc_set || "1" === data.doc_set)){
						var settings_btn = elm.parents('.wfte_doc_outter_div').find('.doc_module_link');
						if(1 === doc_module_set || "1" === doc_module_set){
							settings_btn.attr('href',settings_btn.attr('data-href')).css({'opacity':'1','cursor':'pointer'});
						}else{
							settings_btn.removeAttr('href').css({'opacity':'.5','cursor':'not-allowed'});	
						}
					}else{
						alert(data.message);
					}
					console.log(data);
				}
			});
		});
	}
}

var wt_pklist_pro_addon_show_more_less = {
	Set:function(){
		jQuery(".wt_pro_addon_show_more").on('click',function(){
			jQuery(this).hide();
			jQuery(this).next('.wt_pro_addon_show_less').show();
			jQuery(this).parent().prev().children('.wt_pro_addon_widget .wt_pro_addon_features_list').find('ul li').slice(3).show('slide');
		});

		jQuery(".wt_pro_addon_show_less").on('click',function(){
			jQuery(this).hide();
			jQuery(this).prev('.wt_pro_addon_show_more').show();
			jQuery(this).parent().prev().children('.wt_pro_addon_widget .wt_pro_addon_features_list').find('ul li').slice(3).hide('slide');
		});

		jQuery(".wt_pro_addon_show_more_doc").on('click',function(){
			jQuery(this).hide();
			jQuery(this).next('.wt_pro_addon_show_less_doc').show();
			jQuery(this).parent().parent().children('.wt_pro_addon_widget_doc .wt_pro_addon_features_list_doc').find('ul li').slice(3).show('slide');
		});

		jQuery(".wt_pro_addon_show_less_doc").on('click',function(){
			jQuery(this).hide();
			jQuery(this).prev('.wt_pro_addon_show_more_doc').show();
			jQuery(this).parent().parent().children('.wt_pro_addon_widget_doc .wt_pro_addon_features_list_doc').find('ul li').slice(3).hide('slide');
		});

		jQuery(".wt_pro_addon_show_more_pro_ad").on('click',function(){
			jQuery(this).hide();
			jQuery(this).next('.wt_pro_addon_show_less_pro_ad').show();
			jQuery(this).parent().parent().children('.wt_pro_addon_widget_pro_ad .wt_pro_addon_features_list_pro_ad').find('ul li').slice(3).show('slide');
		});

		jQuery(".wt_pro_addon_show_less_pro_ad").on('click',function(){
			jQuery(this).hide();
			jQuery(this).prev('.wt_pro_addon_show_more_pro_ad').show();
			jQuery(this).parent().parent().children('.wt_pro_addon_widget_pro_ad .wt_pro_addon_features_list_pro_ad').find('ul li').slice(3).hide('slide');
		});

		jQuery('.pro_list_tab').on('click',function(){
			jQuery('.pro_list_tab').addClass('wt_pro_ad_tab_hide').removeClass('wt_pro_ad_tab_show');
			jQuery(this).addClass('wt_pro_ad_tab_show').removeClass('wt_pro_ad_tab_hide');
			var target_div = jQuery(this).attr('data-tab-target');
			jQuery('.pro_list_div').hide();
			jQuery('.'+target_div).show();
			jQuery('.wt_heading_section_pro_ad').hide();
			jQuery('.'+target_div+'_head').show();
		});
	}
}

var wt_pklist_free_vs_pro = {
	Set:function(){
	 	jQuery(".fold-table-free-pro tr.view").on("click", function(){
	 		var banner_id = jQuery(this).attr('data-banner-id');

	 		if("" !== banner_id.trim()){
	 			jQuery('.freevspro_side_banners').hide();
	 			jQuery('#'+banner_id+'_side_banner').show();
	 		}
	 		if(jQuery(this).hasClass("open")){
	 			jQuery(this).parents().children('tr').find('.free_pro_show_more').show();
	 			jQuery(this).parents().children('tr').find('.free_pro_show_less').hide();
	 			jQuery(this).parents().children('tr').removeClass("open",3000);
	 		}else{
	 			jQuery(this).siblings('tr').find('.free_pro_show_more').show();
	 			jQuery(this).siblings('tr').find('.free_pro_show_less').hide();
	 			jQuery(this).siblings('tr').removeClass("open",3000);
	 			jQuery(this).toggleClass("open").next(".fold").toggleClass("open",3000);
	 			jQuery(this).children().find('.free_pro_show_more').hide();
	 			jQuery(this).children().find('.free_pro_show_less').show();
	 		}
		});
	},
}

var wt_pklist_cta_banner_dismiss = {
	Set:function(){
		jQuery('.banner_dismiss').on('click',function(){

			var elm = jQuery(this);
			var banner_class = jQuery(this).attr('data-banner-class');
			var banner_interval = jQuery(this).attr('data-banner-interval');
			var banner_action = jQuery(this).attr('data-banner-action');
			jQuery.ajax({
				type:'POST',
				url:wf_pklist_params.ajaxurl,
				data:{'action':'wt_pklist_cta_banner_dismiss','_wpnonce':wf_pklist_params.nonces.wf_packlist,'banner_class':banner_class,'banner_interval':banner_interval,'banner_action':banner_action},
				dataType:'json',
				success:function(result)
				{
					if(true === result.status){
						elm.parents().find('.wt_pklist_dismissible_banner_div').remove();
					}else{
						alert(result.message);
					}
				}
			});
		});
	}
}

var wt_pklist_settings_debug = {
	Set:function(){
		jQuery('.wt_pklist_export_settings').on('click',function(){
			var export_nonce = jQuery('#wtpdf_debug_settings_export_nonce_id').val();
			jQuery.ajax({
				type:'POST',
				url:wf_pklist_params.ajaxurl,
				data:{'action':'wt_pklist_settings_json','_wpnonce':export_nonce},
				dataType:'json',
				success:function(result)
				{
					if ( null !== result && typeof result === 'object' ) {
						if ( 'success' in result && 'response' in result ) {
							wf_notify_msg.success( result.success );
							wt_pklist_settings_debug.downloadJSON(result.response)
						} else {
							wf_notify_msg.error( result.error );
						}
					}
				},
				error: function(xhr, status, error) {
					console.error('Error:', error);
				}
			});
		});

		jQuery('.wt_pklist_import_settings').on('click',function(e){
			e.preventDefault();
			var imp_file_length = jQuery("#wt_pklist_import_setting_file")[0].files.length;
			if(0 === imp_file_length){
				alert(jQuery(this).attr('data-popup-alert'));
			}else{
				var import_popup = jQuery(this).attr('data-popup-id');
				var elm=jQuery('.'+import_popup);
				wf_popup.showPopup(elm);
			}
		});

		jQuery('.wt_pklist_import_settings_popup_yes').on('click',function(){
			var imp_confirm = jQuery('#wt_pklist_settings_import_confirm_text').val();
			if("confirm" === imp_confirm){
				jQuery('#wt_pklist_import_settings_form').trigger("submit");
			}else{
				jQuery('#wt_pklist_import_settings_popup_error').text('Incorrect');
			}
		});

		jQuery('.wt_pklist_reset_settings').on('click',function(e){
			e.preventDefault();
			var reset_popup = jQuery(this).attr('data-popup-id');
			var elm=jQuery('.'+reset_popup);
			wf_popup.showPopup(elm);
			
		});

		jQuery('.wt_pklist_reset_settings_popup_yes').on('click',function(){
			var reset_confirm = jQuery('#wt_pklist_settings_reset_confirm_text').val();
			if("confirm" === reset_confirm){
				jQuery('#wt_pklist_reset_settings_form').trigger("submit");
			}else{
				jQuery('#wt_pklist_reset_settings_popup_error').text('Incorrect');
			}
		});
	},

	downloadJSON: function(data) {
		var jsonStr = JSON.stringify(data);
		var blob = new Blob([jsonStr], { type: 'application/json' });
	  
		// Create a temporary anchor element
		var downloadLink = document.createElement('a');
		downloadLink.href = URL.createObjectURL(blob);
	  
		// Set the filename for the downloaded file
		var file_name = 'wt_pklist_'+Date.now()+'.json';
		downloadLink.download = file_name;
	  
		// Append the anchor element to the document body
		document.body.appendChild(downloadLink);
	  
		// Simulate a click on the anchor element to initiate the download
		downloadLink.click();
	  
		// Remove the temporary anchor element from the document
		document.body.removeChild(downloadLink);
	  }
}

var wt_pklist_temp_files = {
	Set: function(){
		jQuery('.wt_pklist_temp_files_btn').on('click',function(){
			wt_pklist_temp_files.sendRequest(jQuery(this));
		});
	},
	sendRequest:function(elm)
	{
		var btn_action = elm.attr('data-action');
		var doc_type = elm.attr('data-document');
		var ajx_data='action=wt_pklist_'+btn_action+'&doc_type='+doc_type+'&_wpnonce='+wf_pklist_params.nonces.wf_packlist;
		jQuery.ajax({
			url:wf_pklist_params.ajaxurl,
			data:ajx_data,
			dataType:'json',
			type: 'POST',
			success:function(result)
			{
				if("download_all_temp" === btn_action)
				{
					window.location.href = result.fileurl;
				}else if("delete_all_temp" === btn_action)
				{
					wf_notify_msg.success(result.msg);
					jQuery('.wt_pklist_temp_table_body').html(result.table_html);
					wt_pklist_temp_files.Set();
				}
			},
			error:function()
			{
				wf_notify_msg.error(wf_pklist_params.msgs.error);
			}
		});
	},
}
	
var wt_customizer_pro_fields_popup = {
	Set: function () {
		jQuery('.wt_customizer_promotion_popup_btn').on('click', function () {
			var elm = jQuery('.wt_pklist_customizer_promotion');
			wf_popup.showPopup(elm)
		});
	}
}


jQuery(document).ready(function(){
	wf_popup.Set();
	wf_doc_create.Set();
	wf_file_attacher.Set();
	wf_form_toggler.Set();
	wf_settings_form.Set();
	wf_accord.Set();
	wf_color.Set();
	wf_slideSwitch.Set();
	wt_pklist_conditional_help_text.Set();
	wt_pdf_field_group.Set();
	wt_pklist_sys_info_table.Set();
	wt_pklist_filter_document_search.Set();
	wt_pklist_filter_actions.Set();
	wt_save_button_fixed.Set();
	wt_pklist_module_enable_disable.Set();
	wt_pklist_pro_addon_show_more_less.Set();
	wt_pklist_free_vs_pro.Set();
	wt_pklist_cta_banner_dismiss.Set();
	wt_pklist_settings_debug.Set();
	wt_pklist_temp_files.Set();
	wt_customizer_pro_fields_popup.Set();
});

// javascript
// Document print button in order edit page.
handlePrintButtonClicked();
function handlePrintButtonClicked() {
	document.addEventListener('DOMContentLoaded', function () {
		var printButtons = document.querySelectorAll('.wt_pklist_admin_print_document_btn');
		printButtons.forEach(function (button) {
			if (false === button.classList.contains("class-name")) {
				button.addEventListener('click', function (e) {
					e.preventDefault();
					var action_url = this.getAttribute('href');
					if ( 'Yes' === wf_pklist_params.show_document_preview || ( "logged_in" === wf_pklist_params.document_access_type && '' === wf_pklist_params.is_user_logged_in ) ) {
						window.open(action_url, '_blank');
					} else if ( window.innerWidth <= 768 ) { // check for the mobile device
						do_print_document_in_admin_page_in_mobile_device( action_url );
					} else {
						// window.open(action_url, '_blank');
						do_print_document_in_admin_page(action_url);
					}
				});
			}
		});
	});
}

function do_print_document_in_admin_page_in_mobile_device( url ) {
	var newWindow = window.open(url, '_blank');
	// Once the new window has loaded, trigger the print dialog
	newWindow.onload = function() {
		newWindow.focus();  // Focus the new window before printing
		newWindow.print();

		// Optionally close the window after printing (mobile browsers may block this)
		setTimeout(function() {
			newWindow.close();
		}, 2000);  // Adjust the delay if necessary
	};
}

function do_print_document_in_admin_page( url, is_bulk_print = false, reload_page = false ) {
	var newWindow = window.open('', '_blank');
	if (newWindow) {
		newWindow.document.open();
		newWindow.document.write(wf_pklist_params.msgs.generating_document_text);
		newWindow.document.close();
		newWindow.document.body.style.cursor = 'progress';
	}
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.onload = function () {
		if (200 === this.status) {
			var responseText = xhr.responseText;
			var contentType = xhr.getResponseHeader("Content-Type");
			if (contentType && contentType.includes("text/plain")) {
				// Close the new window immediately if the content is plain text
				if (newWindow) {
					newWindow.close();
				}
				// Show the alert message after closing the window
				setTimeout(function () {
					alert(responseText);
				}, 100); // A short delay to ensure the window closes before alert
				return;
			}
			
			if (newWindow) {
				// Write an iframe to the new tab
				newWindow.document.open();
				newWindow.document.write('<html><head><title>'+wf_pklist_params.msgs.generating_document_text+'</title></head><body><iframe id="printIframe" style="width: 100%; height: 100%; border: none;"></iframe></body></html>');
				newWindow.document.close();
				
				// Get the iframe element
				var printIframe = newWindow.document.getElementById('printIframe');
				printIframe.style.display = 'none';
				// Write the response to the iframe
				var iframeDoc = printIframe.contentDocument || printIframe.contentWindow.document;
				iframeDoc.open();
				iframeDoc.write(xhr.responseText);
				iframeDoc.close();
	
				// Set the title of the new window from the iframe content
				var iframeTitle = iframeDoc.title || 'Document';
				newWindow.document.title = iframeTitle;

				setTimeout(function() {
					printIframe.contentWindow.focus();
					printIframe.contentWindow.print();
					newWindow.document.body.style.cursor = 'auto';

					// Remove the iframe after printing
					newWindow.document.body.removeChild(printIframe);
					newWindow.close();
					if (true === is_bulk_print) {
						// here comes the bulk print.
					} else if ( true === reload_page ) {
						window.location.reload(true);
					}
				}, 500);
	
			} else {
				alert(wf_pklist_params.msgs.new_tab_open_error);
			}
		} else {
			if (newWindow) {
				newWindow.document.body.style.cursor = 'auto'; // Reset cursor on error
			}
			alert(wf_pklist_params.msgs.error_loading_data);
		}
	};

	xhr.onerror = function () {
		if (newWindow) {
			newWindow.document.body.style.cursor = 'auto'; // Reset cursor on request error
		}
		alert(wf_pklist_params.msgs.request_error);
		setTimeout(function () { 
			jQuery('.wf_cst_overlay, .wf_pklist_popup').hide();
		},1000);
	};
	xhr.send();
}