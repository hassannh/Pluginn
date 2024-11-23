<?php
if (!defined('ABSPATH')) {
	exit;
}

$tab_items=array(
    "general"=>__("General", 'print-invoices-packing-slip-labels-for-woocommerce')
);
$tab_items = apply_filters('wt_pklist_add_additional_tab_item_into_module',$tab_items,$this->module_base,$this->module_id);

$pro_installed = true;
$pro_invoice_path = 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php';
if(!is_plugin_active($pro_invoice_path)){
$pro_installed = false;
?>
<style type="text/css">
    /* Common prop styles */
    .wf-tab-container{padding: 15px !important;}
    .spinner{<?php echo is_rtl() ? 'float:right;':'float:left;'; ?>margin-top: 25px !important;}
    .wf_settings_form .button{margin: 10px -2px;}
    .wf-tab-content-inner{display: flex;}
    .wf-tab-content-inner .wf_settings_form{ width: 70%;}
    .wf-tab-content-inner .wt_pro_plugin_promotion{ width: 28%;}

    /* Promotion banner style*/
    .wt_pro_addon_tile_doc{width: 100%;position: inherit;}
    .wt_pro_addon_features_list_doc ul li:nth-child(n + 4){display: none;}
    .wt_pro_addon_features_list_doc li{font-style: normal;font-weight: 500;font-size: 13px;line-height: 17px;color: #001A69;list-style: none;position: relative;padding-left: 49px;margin: 0 15px 15px 0;display: flex;align-items: center;}
    .wt_pro_addon_features_list_doc li:before{content: '';position: absolute;height: 15px;width: 15px;background-image: url(<?php echo esc_url($wf_admin_img_path.'/tick.svg'); ?>);background-size: contain;background-repeat: no-repeat;background-position: center;left: 15px;}
    .wt_pro_addon_widget_doc{border:1.3px solid #E8E8E8;margin-top: 1em;border-radius: 0px 7px 0px 0px;}
    .wt_customizer_promotion_tab{ margin-left: .5em; padding: 5px 10px; font-size: 14px; line-height: 1.71428571; font-weight: 600; color: #50575e; text-decoration: none; white-space: nowrap; cursor: pointer; display: inline-block; }
</style>
<?php
}
?>
<div class="wt_wrap">
    <div class="wt_heading_section">
        <h2 class="wp-heading-inline">
        <?php _e('Settings','print-invoices-packing-slip-labels-for-woocommerce');?>: <?php _e('Packing slip','print-invoices-packing-slip-labels-for-woocommerce');?>
        </h2>
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
        ?>
    </div>
    <div class="nav-tab-wrapper wp-clearfix wf-tab-head">
    	<?php Wf_Woocommerce_Packing_List::generate_settings_tabhead($tab_items, 'module'); ?>
        <?php 
            if(!is_plugin_active($pro_invoice_path)){
                echo '<div class="wt_customizer_promotion_popup_btn wt_customizer_promotion_tab">
                <div style="display:flex;">'. __( 'Customize', 'print-invoices-packing-slip-labels-for-woocommerce' ) . '<img src="'. esc_url(WF_PKLIST_PLUGIN_URL.'admin/images/promote_crown.png') .'" style="width: 12px;height: 12px;background: #FFA800;padding: 5px;margin-left: 4px;border-radius: 25px;"></div>
                </div>';
            }
        ?>
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
        <?php 
        // Customizer promotion popup.
        include_once WF_PKLIST_PLUGIN_PATH . 'admin/views/customizer-promotion-popup.php'; 
        ?>
    </div>
</div>