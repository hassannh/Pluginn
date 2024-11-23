(function( $ ) {
	//'use strict';
	$('#woocommerce_wf_generate_for_orderstatus_st').on('change',function(){
		var invoice_creation_for = $(this).val();
		$.each(wf_woocommerce_packing_list_invoice_common_param.order_statuses,function(key,val){
			if($.inArray(key,invoice_creation_for) !== -1){
				if ($('#woocommerce_wf_add_invoice_in_customer_mail_st').find("option[value='" + key + "']").length){
					// already there
				}else{
					var newOption = new Option(wf_woocommerce_packing_list_invoice_common_param.order_statuses[key], key, false, false);
				    // Append it to the select
				    $('#woocommerce_wf_add_invoice_in_customer_mail_st').append(newOption);
				}
			}else{
				if ($('#woocommerce_wf_add_invoice_in_customer_mail_st').find("option[value='" + key + "']").length) {
					$("#woocommerce_wf_add_invoice_in_customer_mail_st option[value='" + key + "']").remove();
				}
			}
		});
	});
})( jQuery );