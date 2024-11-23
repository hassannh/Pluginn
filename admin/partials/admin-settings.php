<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin/partials
 */

$wf_admin_view_path=plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME).'admin/views/';
$tab_items=array(
        'documents'=>__('Documents','print-invoices-packing-slip-labels-for-woocommerce'),
        'general'=>__('General','print-invoices-packing-slip-labels-for-woocommerce'),
        'help'=>__('Help Guide','print-invoices-packing-slip-labels-for-woocommerce')
    );
if(!empty(Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons('wt_qr_addon'))){
    $tab_items['freevspro'] = __('Free vs Premium','print-invoices-packing-slip-labels-for-woocommerce');
    $addons_activated_pending = true; 
}else{
    $addons_activated_pending = false;
}
$main_module_base = "";
$main_module_id = "";
$tab_items = apply_filters('wt_pklist_add_additional_tab_item_into_module',$tab_items,$main_module_base,$main_module_id);
if(isset($_GET['debug']))
{
    $tab_items['wf-debug']='Debug';
}
?>
<div class="wt_wrap">
    <div class="wt_heading_section">
        <h2 class="wp-heading-inline">
        <?php _e('Settings','print-invoices-packing-slip-labels-for-woocommerce');?>: 
        <?php _e('WooCommerce PDF Invoices, Packing Slips, Delivery Notes and Shipping Labels', 'print-invoices-packing-slip-labels-for-woocommerce');?>
        </h2>
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
        ?>
    </div>
    <div class="wf_settings_left" style="width:100%;">
        <div class="nav-tab-wrapper wp-clearfix wf-tab-head">
            <?php Wf_Woocommerce_Packing_List::generate_settings_tabhead($tab_items); ?>
        </div>

        <div class="wf-tab-container">
            <?php
                foreach($tab_items as $target_id => $tab_item){
                    $settings_view  = $wf_admin_view_path.$target_id.'.php';
                    if('wf-debug' === $target_id){
                        $settings_view=$wf_admin_view_path.'admin-settings-debug.php';
                    }
                    if(file_exists($settings_view))
                    {
                        include $settings_view;
                    }
                }
            ?>
            <?php do_action('wt_pklist_add_additional_tab_content_into_module',$main_module_id,$main_module_base); ?>
            <?php do_action('wf_pklist_plugin_out_settings_form');?>
        </div>
    </div>
    <div class="wf_settings_right" style="display:none;">
    </div>
</div><?php
/**
 * Fires at the end of the settings page.
 *
 * @since 4.7.0
 */
do_action('wt_pdf_plugin_settings_after_wrap');
?>