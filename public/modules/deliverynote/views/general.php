<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
<form method="post" class="wf_settings_form">
    <input type="hidden" value="<?php echo esc_attr($this->module_base); ?>" class="wf_settings_base" />
    <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
    <input type="hidden" value="wt_deliverynote_general" name="wt_tab_name" class="wt_tab_name" />
        <p><?php _e('Configure the general settings required for the delivery note.','print-invoices-packing-slip-labels-for-woocommerce');?></p>
        <?php
            if (function_exists('wp_nonce_field'))
            {
                wp_nonce_field('wf-update-deliverynote-'.WF_PKLIST_POST_TYPE);
            }
        ?>
        <table class="wf-form-table" style="width:100%;">
        <?php
            $settings_arr['deliverynote_general_general'] = array(
                'woocommerce_wf_attach_image_deliverynote' => array(
                    'type' => 'wt_radio',
                    'label' => __("Include product image","print-invoices-packing-slip-labels-for-woocommerce"),
                    'id' => '',
                    'class' => 'woocommerce_wf_attach_image_deliverynote',
                    'name' => 'woocommerce_wf_attach_image_deliverynote',
                    'value' => '',
                    'radio_fields' => array(
                            'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                            'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
                        ),
                    'col' => 3,
                    'tooltip' => true,
                    'alignment' => 'horizontal_with_label',
                ),
                'woocommerce_wf_add_customer_note_in_deliverynote' => array(
                    'type' => 'wt_radio',
                    'label' => __("Add customer note","print-invoices-packing-slip-labels-for-woocommerce"),
                    'id' => '',
                    'class' => 'woocommerce_wf_add_customer_note_in_deliverynote',
                    'name' => 'woocommerce_wf_add_customer_note_in_deliverynote',
                    'value' => '',
                    'radio_fields' => array(
                            'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                            'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
                        ),
                    'col' => 3,
                    'tooltip' => true,
                    'alignment' => 'horizontal_with_label',
                ),
                'woocommerce_wf_packinglist_footer_dn' => array(
                    'type' => 'wt_radio',
                    'label' => __("Add footer","print-invoices-packing-slip-labels-for-woocommerce"),
                    'id' => '',
                    'class' => 'woocommerce_wf_packinglist_footer_dn',
                    'name' => 'woocommerce_wf_packinglist_footer_dn',
                    'value' => '',
                    'radio_fields' => array(
                            'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                            'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
                        ),
                    'col' => 3,
                    'tooltip' => true,
                    'alignment' => 'horizontal_with_label',
                ),
            );
            
            $settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,$target_id,$template_type,$this->module_id);
            if(class_exists('WT_Form_Field_Builder_PRO_Documents_SDD')){
                $Form_builder = new WT_Form_Field_Builder_PRO_Documents_SDD();
            }else{
                $Form_builder = new WT_Form_Field_Builder();
            }
            
            $h_no = 1;
            foreach($settings_arr as $settings){
                foreach($settings as $k => $this_setting){
                    if(isset($this_setting['type']) && "wt_sub_head" === $this_setting['type']){
                        $settings[$k]['heading_number'] = $h_no;
                        $h_no++;
                    }
                }
                $Form_builder->generate_form_fields($settings, $this->module_id);
            }
        ?>        
        </table>
        <?php if(false === $pro_installed){
				?>
				<div style="clear: both;"></div>
				<span id="end_wf_setting_form" class="end_wf_setting_form"></span>
				<input type="submit" name="update_admin_settings_form" value="<?php echo __('Update Settings', 'print-invoices-packing-slip-labels-for-woocommerce'); ?>" class="button button-primary wt_pklist_update_settings_btn"/>
				<span class="spinner" style="margin-top:11px"></span>
				<?php
			}else{
	            include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
			}
        ?>
        <?php 
            //settings form fields for module
            do_action('wf_pklist_document_settings_form');?>
    </form>
</div>
<?php do_action('wf_pklist_document_out_settings_form');?>