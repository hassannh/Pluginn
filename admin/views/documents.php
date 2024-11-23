<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wt_pklist_common_modules=get_option('wt_pklist_common_modules');
if($wt_pklist_common_modules===false || !is_array( $wt_pklist_common_modules ))
{
    $wt_pklist_common_modules=array();
}
$wt_pklist_doc_modules_all = array(
    'col-1_row' => array(
        'wt_ipc_addon'  => array(
            'file_path' => 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php',
            'page_link' => 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_main_menu&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
            'title'     => __("Invoices, Packing Slips and Credit Notes Module","print-invoices-packing-slip-labels-for-woocommerce"),
            'modules'   => array(
                'invoice'       => array(
                    'label' => __("Invoice","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'free',
                ),
                'packinglist'   => array(
                    'label' => __("Packing slip","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'free',
                ),
                'creditnote'    => array(
                    'label' => __("Credit note","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'pro',
                    'page_link' => 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_credit_note&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
                ),
            ),
            'width' => 'wt_doc_col-1',
        ),
        'wt_sdd_addon'  => array(
            'file_path' => 'wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php',
            'page_link' => 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_main_menu&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION,
            'title'     => __("Shipping labels, Dispatch labels and Delivery Notes Module","print-invoices-packing-slip-labels-for-woocommerce"),
            'modules'   => array(
                'shippinglabel' => array(
                    'label' => __("Shipping label","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'free',
                ),
                'dispatchlabel' => array(
                    'label' => __("Dispatch label","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'free',
                ),
                'deliverynote'  => array(
                    'label' => __("Delivery note","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'free',
                ),
            ),
            'width' => 'wt_doc_col-1',
        ),
    ),
    'col-3_row' => array(
        'wt_pl_addon'   => array(
            'file_path' => 'wt-woocommerce-picklist-addon/wt-woocommerce-picklist-addon.php',
            'page_link' => 'https://www.webtoffee.com/product/woocommerce-picklist/?utm_source=free_plugin_main_menu&utm_medium=pdf_basic&utm_campaign=Picklist&utm_content='.WF_PKLIST_VERSION,
            'title'     => __("Picklists Add-on","print-invoices-packing-slip-labels-for-woocommerce"),
            'modules'   => array(
                'picklist' => array(
                    'label' => __("Picklist","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'pro',
                    'bfcm_banner' => true,
                ),
            ),
            'width' => 'wt_doc_col-3',
        ),
        'wt_al_addon'   => array(
            'file_path' => 'wt-woocommerce-addresslabel-addon/wt-woocommerce-addresslabel-addon.php',
            'page_link' => 'https://www.webtoffee.com/product/woocommerce-address-label/?utm_source=free_plugin_main_menu&utm_medium=pdf_basic&utm_campaign=Address_Label&utm_content='.WF_PKLIST_VERSION,
            'title'     => __("Address Label Add-on","print-invoices-packing-slip-labels-for-woocommerce"),
            'modules'   => array(
                'addresslabel' => array(
                    'label' => __("Address labels","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'pro',
                    'bfcm_banner' => true,
                ),
            ),
            'width' => 'wt_doc_col-3',
        ),
        'wt_pi_addon'   => array(
            'file_path' => 'wt-woocommerce-proforma-addon/wt-woocommerce-proforma-addon.php',
            'page_link' => 'https://www.webtoffee.com/product/woocommerce-proforma-invoice/?utm_source=free_plugin_main_menu&utm_medium=pdf_basic&utm_campaign=Proforma_Invoice&utm_content='.WF_PKLIST_VERSION,
            'title'     => __("Proforma Invoices Add-on","print-invoices-packing-slip-labels-for-woocommerce"),
            'modules'   => array(
                'proformainvoice' => array(
                    'label' => __("Proforma invoice","print-invoices-packing-slip-labels-for-woocommerce"),
                    'module_type' => 'pro',
                    'bfcm_banner' => true,
                ),
            ),
            'width' => 'wt_doc_col-3',
        )
    ),
);
$wt_pklist_common_modules_main=array_chunk($wt_pklist_common_modules,3,true);
$wt_pklist_common_modules_main = $wt_pklist_common_modules;
$document_module_labels=Wf_Woocommerce_Packing_List_Public::get_document_module_labels();
?>
<style type="text/css">
    .wfte_doc_outter_div{display: flex;background: #fafafa;align-items: center;padding: 0px 15px;justify-content: space-evenly;min-height: 6em;}
    .wfte_doc_title_image{padding: 10px 0;width: 75%;}
    .wfte_doc_title_image > a{display:flex;align-items: center;text-decoration: none;}
    .wfte_doc_title_image > a > img {width: 15%;}
    .wfte_doc_title_image > a > h3 {margin: 5px 0 10px 10px;color: #4F575F;font-size: 15px;}
    .wfte_doc_setting_toggle{margin: 10px 0;width: 25%;}
    .wf_pklist_dashboard_box_footer_up{align-items: center;display: flex;width: 100%;}
    .doc_module_link:focus{box-shadow: none;}
</style>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
    <form method="post">
        <?php
            // Set nonce:
            if (function_exists('wp_nonce_field'))
            {
                wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
            }
            foreach($wt_pklist_doc_modules_all as $wt_pklist_doc_modules){
        ?>
        <div style="width:100%;float:left;">
        <?php
        foreach($wt_pklist_doc_modules as $doc_mod){
            ?>
            <div class="<?php echo esc_attr($doc_mod['width']); ?> wt_pklist_doc_row_tile">
                <div class="wt_pklist_doc_head" style="float: left;width: 100%;">
                    <p class="wt_pklist_doc_module_title">
                        <?php echo esc_html($doc_mod['title']); ?>
                        <?php if("wt_doc_col-3" === $doc_mod['width']){
                            ?>
                            <img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL); ?>admin/images/promote_crown.png" style="width: 12px;height: 12px;background: #FFA800;padding: 5px;margin-left: 4px;border-radius: 25px;">
                            <?php
                        } ?>
                    </p>
                    <?php
                        if("wt_doc_col-3" !== $doc_mod['width'] && !is_plugin_active($doc_mod['file_path'])){
                            ?>
                            <p class="wt_pklist_doc_module_type wt_pklist_free_addon_badge"><?php _e("Free version","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            <?php
                        }elseif("wt_doc_col-3" !== $doc_mod['width'] && is_plugin_active($doc_mod['file_path'])){
                            ?>
                            <p class="wt_pklist_doc_module_type wt_pklist_pro_addon_badge"><?php _e("Premium version","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            <?php
                        }
                    ?>
                </div>
                <?php
                    if("wt_doc_col-3" !== $doc_mod['width'] && !is_plugin_active($doc_mod['file_path'])){
                        ?>
                        <div style="float:left;width:100%;">
                            <p style="margin:0;line-height: 24px;color: #525252;"><?php echo sprintf(__('You are using free version of %1$s. %2$s Checkout premium here %3$s.','print-invoices-packing-slip-labels-for-woocommerce'),esc_html(strtolower($doc_mod['title'])),'<a href="'.esc_url($doc_mod['page_link']).'" target="_blank">','</a>'); ?></p>
                        </div>
                        <?php
                    }
                ?>
                <div class="wt_pklist_doc_tile_div wt_pklist_doc_tile_<?php echo esc_attr($doc_mod['width']); ?>">
                <?php
                    foreach($doc_mod['modules'] as $this_doc_mod_key => $this_doc_mod){
                        $module_id=Wf_Woocommerce_Packing_List::get_module_id($this_doc_mod_key);
                        $wt_doc_logo_url = WF_PKLIST_PLUGIN_URL.'admin/images/'.$this_doc_mod_key.'.png';
                        $v = isset($wt_pklist_common_modules_main[$this_doc_mod_key]) ? $wt_pklist_common_modules_main[$this_doc_mod_key] : 0;
                        $vl_checked = ((1 === $v || "1" === $v )? 'checked' : '');
                        ?>
                        <div class="wt_pklist_doc_tile">
                            <div class="wfte_doc_outter_div <?php echo ("pro" === $this_doc_mod['module_type'] && !is_plugin_active($doc_mod['file_path'])) ? 'wfte_doc_outter_div_not_active' : '';  ?>">
                                <div class="wfte_doc_title_image">
                                    <?php
                                        if("pro" === $this_doc_mod['module_type'] && !is_plugin_active($doc_mod['file_path'])){
                                            $settings_url = isset($this_doc_mod['page_link']) ? $this_doc_mod['page_link'] :$doc_mod['page_link'];
                                            $target_attr = 'target="_blank"';
                                        }else{
                                            $settings_url = admin_url('admin.php?page='.$module_id);
                                            $target_attr = '';
                                        }
                                    ?>
                                    <a class="doc_module_link" <?php echo $target_attr; ?> href="<?php echo esc_url($settings_url); ?>" data-href="<?php echo esc_url($settings_url); ?>" style="opacity: 1; cursor: pointer;">
                                        <img src="<?php echo esc_url($wt_doc_logo_url); ?>" style="">
                                        <h3><?php echo esc_html($this_doc_mod['label']); ?></h3>
                                    </a>
                                </div>
                                <div class="wfte_doc_setting_toggle">
                                    <div class="wf_pklist_dashboard_box_footer_up">
                                        <?php
                                            if("pro" === $this_doc_mod['module_type'] && !is_plugin_active($doc_mod['file_path'])){
                                                ?>
                                                <a href="<?php echo esc_url($settings_url); ?>" target="_blank"><?php _e("Get Add-on","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                                                <?php
                                            }else{
                                                ?>
                                                <div class="wf_pklist_dashboard_checkbox">
                                                    <input type="checkbox" value="1" name="wt_pklist_common_modules[<?php echo esc_attr($this_doc_mod_key); ?>]" <?php echo esc_html($vl_checked); ?> class="wf_slide_switch wt_document_module_enable" id="wt_pklist_<?php echo esc_attr($this_doc_mod_key); ?>">   
                                                </div>
                                                <?php
                                            }
                                        ?>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
                </div>

                <?php
                /**
                 * @since 4.7.0
                 * Black Friday Cyber Monday banner offer
                 */ 
                if( "pro" === $this_doc_mod['module_type'] && !is_plugin_active( $doc_mod['file_path'] ) && isset( $this_doc_mod['bfcm_banner'] ) && $this_doc_mod['bfcm_banner'] && true === Wt_Pklist_Common::is_bfcm_season() ){
                    echo '<div class="wt-bfcm-banner-2024-offer">'.esc_html__( '30% OFF', 'print-invoices-packing-slip-labels-for-woocommerce' ).'</div>';
                }?>
            </div>
            <?php
        }
        ?>
        </div>
        <?php  } ?>
    </form>
</div>