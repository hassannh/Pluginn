(function( $ ) {
    $(window).on('hashchange', function (e) {
        var location_hash=window.location.hash;
        if(("" !== location_hash && "#general" === location_hash) || "" === location_hash)
        {
            $('.wf-tab-head').css('width','100%');
            $('.wt_pro_addon_tile_doc').parent().show();
            $('.wt_pro_addon_tile_doc').css('top','5em');
        }else if("" !== location_hash){
            $('.wf-tab-head').css('width','auto');
            $('.wt_pro_addon_tile_doc').parent().hide();
        }
    }).trigger('hashchange');
})( jQuery );