(function ($) {
    //'use strict';
    $(function () {
        $('.wt_pklist_ubl_invoice_tax_class_name').on('click', function () { 
            var target_table_id = $(this).attr('data-target-table-id');
            if ('undefined' !== typeof target_table_id) {
                $('.wt_pklist_ubl_invoice_tax_class_name').removeClass('active');
                $(this).addClass('active');
                
                $('.wt_pklist_ubl_tax_table').removeClass('active');
                $('.wt_pklist_ubl_tax_table[data-table-id="'+target_table_id+'"]').addClass('active');
            }
        });
    });
})( jQuery );