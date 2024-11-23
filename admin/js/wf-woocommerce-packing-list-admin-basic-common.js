(function( $ ) {
	$(function() {
	});
}) (jQuery);

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
			if(typeof pp_html_cntr !== typeof undefined && pp_html_cntr !== false)
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
			if(typeof pp_html_cntr !== typeof undefined && pp_html_cntr !== false)
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
					if(true === data.success)
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
jQuery(document).ready(function(){
	wf_popover.Set();
});