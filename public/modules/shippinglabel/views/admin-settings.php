<?php
if (!defined('ABSPATH')) {
    exit;
}
$tab_items=array(
    "general"=>__("General", 'print-invoices-packing-slip-labels-for-woocommerce'),
);
$tab_items = apply_filters('wt_pklist_add_additional_tab_item_into_module',$tab_items,$this->module_base,$this->module_id);
$pro_installed = true;
$pro_sl_path = 'wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php';
if(!is_plugin_active($pro_sl_path)){
$pro_installed = false;
?>
<style type="text/css">
.spinner{<?php echo is_rtl() ? 'float:right;':'float:left;'; ?>margin-top: 25px !important;}
.wf_settings_form .button{margin: 10px -2px;}
.wf-tab-content{width: 70%;<?php echo is_rtl() ? 'float:right;':''; ?>}
.wt_pro_addon_tile_doc{width: 100%;position: inherit;}
.wt_pro_addon_features_list_doc ul li:nth-child(n + 4){display: none;}
.wt_pro_addon_features_list_doc li{font-style: normal;font-weight: 500;font-size: 13px;line-height: 17px;color: #001A69;list-style: none;position: relative;padding-left: 49px;margin: 0 15px 15px 0;display: flex;align-items: center;}
.wt_pro_addon_features_list_doc li:before{content: '';position: absolute;height: 15px;width: 15px;background-image: url(<?php echo esc_url($wf_admin_img_path.'/tick.svg'); ?>);background-size: contain;background-repeat: no-repeat;background-position: center;left: 15px;}
.wt_pro_addon_widget_doc{border:1.3px solid #E8E8E8;margin-top: 1em;border-radius: 0px 7px 0px 0px;}
</style>
<?php
}
?>
<div class="wt_wrap" style="float: initial;">
    <div class="wt_heading_section">
        <h2 class="wp-heading-inline">
        <?php _e('Settings','print-invoices-packing-slip-labels-for-woocommerce');?>: <?php _e('Shipping label','print-invoices-packing-slip-labels-for-woocommerce');?>
        </h2>
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
        ?>
    </div>
    <div class="nav-tab-wrapper wp-clearfix wf-tab-head">
        <?php Wf_Woocommerce_Packing_List::generate_settings_tabhead($tab_items, 'module'); ?>
    </div>
    <div class="wf-tab-container">
        <?php
            foreach($tab_items as $target_id => $tab_item){
                $settings_view=plugin_dir_path( __FILE__ ).$target_id.'.php';
                if(file_exists($settings_view))
                {
                    include $settings_view;
                }
            }
        ?>
        <!-- add additional tab view pages -->
        <?php do_action('wt_pklist_add_additional_tab_content_into_module',$this->module_base,$this->module_id); ?>
        <?php do_action('wf_pklist_module_out_settings_form',array(
            'module_id'=>$this->module_base
        ));?>
        <?php include_once WF_PKLIST_PLUGIN_PATH . 'admin/views/customizer-promotion-popup.php'; ?>
        <?php
        if(false === $pro_installed){
            $sidebar_pro_link = 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_sidebar&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION;
            $sl_pro_features_list = array(
                __("Multiple templates to personalize the label","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Add a print label button to the order email","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Generate custom-sized shipping labels","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Fit multiple shipping labels on a single page","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Add company logo & return policy to label","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Add notes like fragile, keep dry, etc.","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Add order meta fields to shipping labels","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Include the package number & box names on the label","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Display the total number of items on label","print-invoices-packing-slip-labels-for-woocommerce"),
                __("Generate dispatch labels and delivery notes","print-invoices-packing-slip-labels-for-woocommerce"),
            );
        ?>
        <div style="position:relative;width:30%;float:left;">
            <div class="wt_pro_addon_tile_doc" style="<?php echo is_rtl() ? 'left:0;' : 'right:0;'; ?>">
                <div class="wt_pro_addon_widget_doc">
                    <?php
                        /**
                         * @since 4.7.0 - Add offer for Black Friday Cyber Monday 2024
                         */
                        if( Wt_Pklist_Common::is_bfcm_season() ) {
                        ?>
                    <div class="bfcm_doc_settings">
                        <img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/modules/banner/assets/images/bfcm-doc-settings-coupon.svg'); ?>">
                    </div>
                        <?php
                        }
                    ?>
                    <div class="wt_pro_addon_widget_wrapper_doc">
                        <p><?php _e('Get advanced features for your','print-invoices-packing-slip-labels-for-woocommerce'); ?></p>
                        <div class="wt_pro_addon_widget_wrapper_doc_logo_title">
                            <div class="wt_pro_addon_widget_wrapper_doc_logo_title_col_1">
                                <img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/images/wt_sdd_logo.png'); ?>">
                            </div>
                            <div class="wt_pro_addon_widget_wrapper_doc_logo_title_col_2">
                                <h4><?php echo __("Shipping labels, Dispatch labels and Delivery notes","print-invoices-packing-slip-labels-for-woocommerce"); ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="wt_pro_addon_features_list_doc">
                        <ul>
                            <?php
                                foreach($sl_pro_features_list as $p_feature){
                                    ?>
                                    <li><?php echo esc_html_e($p_feature); ?></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                    <div class="wt_pro_show_more_less_doc">
                        <a class="wt_pro_addon_show_more_doc"><p><? echo __("Show More","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                        <a class="wt_pro_addon_show_less_doc"><p><? echo __("Show Less","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
                    </div>
                    <a class="wt_pro_addon_premium_link_div_doc" href="<?php echo esc_url($sidebar_pro_link); ?>" target="_blank">
                        <?php _e("View add-on","print-invoices-packing-slip-labels-for-woocommerce"); ?> <span class="dashicons dashicons-arrow-right-alt"></span>
                    </a>
                </div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
</div>