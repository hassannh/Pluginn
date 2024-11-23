(function ($) {
    'use strict';

    $(function () {
        var wt_pdf_request_feature = {
            init: function () { 
                $(document).on('click', '.wt-pdf-request-feature-button', function (e) { 
                    var popup_div = $(this).attr('data-wt-pdf-popup');
			        wf_popup.showPopup($('.'+popup_div))
                });

                /* Email field toggling */
                $('#wt_pdf_request_feature_customer_email_enable').on('click', function(){
                    if($(this).is(':checked')){
                        $('.wt_pdf_request_feature_email_container').slideDown('fast');
                        $('[name="wt_pdf_request_feature_customer_email"]').trigger('focus');
                    } else {
                        $('.wt_pdf_request_feature_email_container').slideUp('fast');
                    }
                });

                /* Reset the form and hide the email field on page load */
                jQuery('.wt-pdf-request-feature-button').on('click', function(){
                    jQuery('.wt_pdf_request_feature_popup form')[0].reset();
                    jQuery('.wt_pdf_request_feature_email_container').hide();
                });

                /* form submit */
                jQuery('.wt_pdf_request_feature_popup form').on('submit', function (e) { 
                    e.preventDefault();

                    /* Validation */
                    if( "" === jQuery('[name="wt_pdf_request_feature_msg"]').val().trim() )
                    {
                        wf_notify_msg.error( wt_pdf_request_feature_js_params.enter_message, false );
                        jQuery('[name="wt_pdf_request_feature_msg"]').trigger('focus');
                        return false;
                    }

                    if(jQuery('#wt_pdf_request_feature_customer_email_enable').is(':checked') && "" === jQuery('[name="wt_pdf_request_feature_email"]').val().trim())
                    {
                        wf_notify_msg.error(wt_pdf_request_feature_js_params.email_message, false);
                        jQuery('[name="wt_pdf_request_feature_email"]').trigger('focus');
                        return false;
                    }

                     /* Ajax request */
                     var btn_html_bckup = jQuery('[name="wt_pdf_request_feature_sbmt_btn"]').text();
                    jQuery('[name="wt_pdf_request_feature_sbmt_btn"]').prop({ 'disabled': true }).text(wt_pdf_request_feature_js_params.sending);
                    
                    jQuery.ajax({
                        url: wt_pdf_request_feature_js_params.ajax_url,
                        type: 'POST',
                        data: jQuery(this).serialize(),
                        dataType: 'json',
                        success: function(data)
                        {
                            if(data.status)
                            {
                                wf_notify_msg.success(wt_pdf_request_feature_js_params.success_msg, true); 
                                wf_popup.hidePopup();

                            }else{
                                wf_notify_msg.error(data.msg, true);
                            }
                        },
                        error: function()
                        {
                            wf_notify_msg.error(wt_pdf_request_feature_js_params.unable_to_submit, false); 
                        },
                        complete: function()
                        {
                            jQuery('[name="wt_pdf_request_feature_sbmt_btn"]').prop({'disabled': false}).text(btn_html_bckup);
                        }
                    });
                });

                 /* Cancel button */
                 jQuery('[name="wt_pdf_request_feature_cancel_btn"]').on('click', function(){
                    wf_popup.hidePopup();
                });
            }
        };

        wt_pdf_request_feature.init();
    });

})(jQuery);