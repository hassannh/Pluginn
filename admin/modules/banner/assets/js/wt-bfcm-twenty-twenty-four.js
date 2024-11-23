(function ($) {
    'use strict';

    $(function () {
        var wt_bfcm_twenty_twenty_four_banner = {
            init: function () { 
                var data_obj = {
                    _wpnonce: wt_bfcm_twenty_twenty_four_banner_js_params.nonce,
                    action: wt_bfcm_twenty_twenty_four_banner_js_params.action,
                    wt_bfcm_twenty_twenty_four_banner_action_type: '',
                };

                $(document).on('click', 'wt-bfcm-banner-2024 .bfcm_cta_button', function (e) { 
                    e.preventDefault(); 
                    var elm = $(this);
                    window.open(wt_bfcm_twenty_twenty_four_banner_js_params.cta_link, '_blank'); 
                    elm.parents('.wt-bfcm-banner-2024').hide();
                    data_obj['wt_bfcm_twenty_twenty_four_banner_action_type'] = 3; // Clicked the button.
                    
                    $.ajax({
                        url: wt_bfcm_twenty_twenty_four_banner_js_params.ajax_url,
                        data: data_obj,
                        type: 'POST'
                    });
                }).on('click', '.wt-bfcm-banner-2024 .notice-dismiss', function(e) {
                    e.preventDefault();
                    data_obj['wt_bfcm_twenty_twenty_four_banner_action_type'] = 2; // Closed by user
                    
                    $.ajax({
                        url: wt_bfcm_twenty_twenty_four_banner_js_params.ajax_url,
                        data: data_obj,
                        type: 'POST',
                    });
                });
            }
        };
        wt_bfcm_twenty_twenty_four_banner.init();
    });

})(jQuery);