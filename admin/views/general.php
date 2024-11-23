<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wf_admin_img_path                      = WF_PKLIST_PLUGIN_URL . 'admin/images/';
$print_separate_button_for_documents    = apply_filters('wt_pklist_individual_print_button_for_document_types',array());
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
    <form method="post" class="wf_settings_form wf_general_settings_form">
        <input type="hidden" value="main" class="wf_settings_base" />
        <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
        <input type="hidden" value="wt_main_general" name="wt_tab_name" class="wt_tab_name" />
        <p><?php _e("The company name and the address details from this section will be used as the sender address in the invoice and other related documents.","print-invoices-packing-slip-labels-for-woocommerce");?></p>
        <?php
        // Set nonce:
        if (function_exists('wp_nonce_field'))
        {
            wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
        }
        $tooltip_conf=Wf_Woocommerce_Packing_List_Admin::get_tooltip_configs('load_default_address');  
        $load_from_woo = sprintf(
            '<a class="wf_pklist_load_address_from_woo %1$s" %2$s>
            <span class="dashicons dashicons-admin-page"></span>%3$s</a>',
        $tooltip_conf['class'],
        $tooltip_conf['text'],
        __('Load from WooCommerce','print-invoices-packing-slip-labels-for-woocommerce')
        );
        ?>
        <table class="wf-form-table">
            <tbody>
                <?php
                    $settings_arr['general_company_details'] = array(
                        'wt_sub_head_company_details' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Company details",'print-invoices-packing-slip-labels-for-woocommerce'),
                        ),

                        'woocommerce_wf_packinglist_companyname' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Name",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_companyname",
                            'class' => 'woocommerce_wf_packinglist_companyname',
                            'tooltip'=> true,
                            'help_text' => sprintf('%1$s <b>%2$s</b> %3$s <b>%4$s</b>.',
                                            __("To include the keyed in name to the Invoice, ensure to select","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("Company name","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("from","print-invoices-packing-slip-labels-for-woocommerce"),
                                            __("Invoice > Customize > Company Logo / Name","print-invoices-packing-slip-labels-for-woocommerce"))
                        ),

                        'woocommerce_wf_packinglist_logo' => array(
                            'type'  =>  "wt_uploader",
                            'label' =>  __("Logo",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>  "woocommerce_wf_packinglist_logo",
                            'id'    =>  "woocommerce_wf_packinglist_logo",
                            'help_text'=> sprintf('%1$s <b>%2$s</b>. %3$s .',
                                        __("To include the uploaded image as logo to the invoice, ensure to select Company logo from","print-invoices-packing-slip-labels-for-woocommerce"),
                                        __("Invoice > Customize > Company Logo / Name","print-invoices-packing-slip-labels-for-woocommerce"),
                                        __("Recommended size is 150×50px","print-invoices-packing-slip-labels-for-woocommerce")
                                    ),
                             'tooltip'=> true,
                        ),

                        'woocommerce_wf_packinglist_sender_vat' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Company Tax ID",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_vat",
                            'class' => 'woocommerce_wf_packinglist_sender_vat',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_vat',
                            'help_text'=>__("Specify your company tax ID. For e.g., you may enter as VAT: GB123456789, GSTIN:0948745 or ABN:51 824 753 556", 'print-invoices-packing-slip-labels-for-woocommerce'),
                        ),
                        'woocommerce_wf_packinglist_footer' => array(
                            'type'  =>  'wt_textarea',
                            'label' =>  __("Footer",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_footer",
                            'class' => 'woocommerce_wf_packinglist_footer',
                        ),

                        'wt_doc_hr_line_1' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_doc_hr_line_1',
                        )
                    );
                    $settings_arr['general_address_details'] = array(
                        'wt_sub_head_address_details' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Address details",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'ref_id' => 'wt_doc_sub_head_company_address',
                            'col_3' =>  $load_from_woo,
                            'tooltip'=> true,
                        ),

                        'woocommerce_wf_packinglist_sender_name' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Department/Business unit/Sender name",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_name",
                            'class' =>  'woocommerce_wf_packinglist_sender_name',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_name',
                        ),

                        'woocommerce_wf_packinglist_sender_address_line1' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Address line 1",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_address_line1",
                            'class' =>  'woocommerce_wf_packinglist_sender_address_line1',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line1',
                        ),

                        'woocommerce_wf_packinglist_sender_address_line2' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Address line 2",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_address_line2",
                            'class' =>  'woocommerce_wf_packinglist_sender_address_line2',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_address_line2',
                        ),

                        'woocommerce_wf_packinglist_sender_city' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("City",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_city",
                            'class' =>  'woocommerce_wf_packinglist_sender_city',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_city',
                        ),

                        'wf_country' => array(
                            'type'  =>  'wt_wc_country_dropdown',
                            'label' => __('Country/State','print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  => 'wf_country',
                            'placeholder'   => __( 'Choose a country&hellip;','print-invoices-packing-slip-labels-for-woocommerce'),
                            'mandatory' => true,
                        ),

                        'woocommerce_wf_packinglist_sender_postalcode' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Postal code",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_postalcode",
                            'class' =>  'woocommerce_wf_packinglist_sender_postalcode',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_postalcode',
                            'mandatory'=>true,
                        ),

                        'woocommerce_wf_packinglist_sender_contact_number' => array(
                            'type'  =>  'wt_text',
                            'label' =>  __("Contact number",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>    "woocommerce_wf_packinglist_sender_contact_number",
                            'class' =>  'woocommerce_wf_packinglist_sender_contact_number',
                            'ref_id'=>  'woocommerce_wf_packinglist_sender_contact_number',
                        ),

                        'wt_doc_hr_line_1' => array(
                            'type' => 'wt_hr_line',
                            'class' => 'wf_field_hr',
                            'ref_id' => 'wt_doc_hr_line_1',
                        ));
                    $settings_arr['advanced_option'] = array(
                        'wt_doc_sub_head_company_info' => array(
                            'type'  =>  'wt_sub_head',
                            'class' =>  'wt_pklist_field_group_hd_sub',
                            'label' =>  __("Advanced options",'print-invoices-packing-slip-labels-for-woocommerce'),
                            // 'heading_number' => 1,
                            'ref_id' => 'wt_doc_sub_head_company_info'
                        ),

                       'woocommerce_wf_state_code_disable' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Display state name","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_state_code_disable',
                            'name' => 'woocommerce_wf_state_code_disable',
                            'value' => "yes",
                            'checkbox_fields' => array('yes'=> __("Enable to show state name in addresses","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_state_code_disable",
                            'col' => 3,
                            'tooltip' => true,
                        ),

                        'wt_pklist_show_currency_code' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Display currency code","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'wt_pklist_show_currency_code',
                            'name' => 'wt_pklist_show_currency_code',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to show currency code instead of currency symbol","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "wt_pklist_show_currency_code",
                            'col' => 3,
                            'tooltip' => true,
                        ),

                        'woocommerce_wf_packinglist_preview' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Preview before printing","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_packinglist_preview',
                            'name' => 'woocommerce_wf_packinglist_preview',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Preview documents before printing","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_packinglist_preview",
                            'col' => 3,
                            'tooltip' => true
                        ),

                        'woocommerce_wf_add_rtl_support' => array(
                            'type' => 'wt_single_checkbox',
                            'label' => __("Enable RTL support","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => 'woocommerce_wf_add_rtl_support',
                            'name' => 'woocommerce_wf_add_rtl_support',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("RTL support for documents","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class' => "woocommerce_wf_add_rtl_support",
                            'col' => 3,
                            'help_text' => sprintf('%1$s <a href="https://wordpress.org/plugins/mpdf-addon-for-pdf-invoices/">%2$s</a>.',
                                __("For better RTL integration in PDF documents, please use our","print-invoices-packing-slip-labels-for-woocommerce"),
                                __("mPDF add-on","print-invoices-packing-slip-labels-for-woocommerce")),
                        ),
                    );
                    if(is_array($pdf_libs) && count($pdf_libs)>1)
                    {
                        $pdf_libs_form_arr=array();
                        foreach ($pdf_libs as $key => $value)
                        {
                            $pdf_libs_form_arr[$key]=(isset($value['title']) ? $value['title'] : $key);
                        }
                        $settings_arr['advanced_option']['active_pdf_library']=array(
                            'type'  =>  "wt_radio",
                            'label' =>  __("PDF library",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  =>  "active_pdf_library",
                            'radio_fields'  =>  $pdf_libs_form_arr,
                            'tooltip' => true,
                            'form_toggler'=>array(
                                'type'=>'parent',
                                'target'=>'wt_pklist_additional_currency_font_support',
                            ),
                        );

                        $settings_arr['advanced_option']['wt_pklist_additional_currency_font_support'] = array(
                            'type'              => 'wt_single_checkbox',
                            'label'             => sprintf('%1$s',
                                __("Enable additional currency font support","print-invoices-packing-slip-labels-for-woocommerce"),
                            ),
                            'id'                => 'wt_pklist_additional_currency_font_support',
                            'name'              => 'wt_pklist_additional_currency_font_support',
                            'value'             => "Yes",
                            'checkbox_fields'   => array('Yes'=> __("Enable to use additional font library to support the currency symbol in pdf","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class'             => "wt_pklist_additional_currency_font_support",
                            'col'               => 3,
                            'tooltip'           => true,
                            'form_toggler'=>array(
                                'type'  => 'child',
                                'val'   => 'dompdf',
                                'id'    => 'wt_pklist_additional_currency_font_support',
                                'lvl'   => 2,
                            ),
                        );
                    } else {
                        $settings_arr['advanced_option']['wt_pklist_additional_currency_font_support'] = array(
                            'type'              => 'wt_single_checkbox',
                            'label'             => sprintf('%1$s',
                                __("Enable additional currency font support","print-invoices-packing-slip-labels-for-woocommerce"),
                            ),
                            'id'                => 'wt_pklist_additional_currency_font_support',
                            'name'              => 'wt_pklist_additional_currency_font_support',
                            'value'             => "Yes",
                            'checkbox_fields'   => array('Yes'=> __("Enable to use additional font library to support the currency symbol in pdf","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class'             => "wt_pklist_additional_currency_font_support",
                            'col'               => 3,
                            'tooltip'           => true,
                        );
                    }

                    $settings_arr['print_options']  = array(
                        'wt_pklist_common_print_button_enable' => array(
                            'type'              => 'wt_single_checkbox',
                            'label'             => sprintf('%1$s <br><span class="label_sub_text">(%2$s)</span>',
                                __("Enable a common print button for","print-invoices-packing-slip-labels-for-woocommerce"),
                                __("orders listing page","print-invoices-packing-slip-labels-for-woocommerce")
                            ),
                            'id'                => 'wt_pklist_common_print_button_enable',
                            'name'              => 'wt_pklist_common_print_button_enable',
                            'value'             => "Yes",
                            'checkbox_fields'   => array('Yes'=> __("All document types","print-invoices-packing-slip-labels-for-woocommerce")),
                            'class'             => "wt_pklist_common_print_button_enable",
                            'col'               => 3,
                            'tooltip'           => true,
                        ),

                        'wt_pklist_separate_print_button_enable' => array(
                            'type'              => 'wt_multi_checkbox',
                            'label'             => sprintf('%1$s <br><span class="label_sub_text">(%2$s)</span>',
                                __("Enable dedicated print buttons for","print-invoices-packing-slip-labels-for-woocommerce"),
                                __("orders listing page","print-invoices-packing-slip-labels-for-woocommerce")
                                ),
                            'id'                => '',
                            'class'             => 'wt_pklist_separate_print_button_enable',
                            'name'              => 'wt_pklist_separate_print_button_enable',
                            'value'             => '',
                            'checkbox_fields'   => $print_separate_button_for_documents,
                            'col'               => 3,
                            'alignment'         => 'vertical_with_label',
                            'tooltip'           => true
                        ),

                        'wt_pklist_print_button_access_for' => array(
                            'type'          => 'wt_radio',
                            'label'         => __('Print button access for', 'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'          => 'wt_pklist_print_button_access_for',
                            'id'            => 'wt_pklist_print_button_access_for',
                            'class'         => 'wt_pklist_print_button_access_for',
                            'radio_fields'  =>  array(
                                'logged_in' => sprintf(
                                    '%1$s <span style="padding: 2px 6px;border-radius: 3px;background: #DFFCF0;font-weight: 700;color: #216E4E;">%3$s</span><br><span class="label_sub_text">%2$s</span>',
                                    __("Logged in users","print-invoices-packing-slip-labels-for-woocommerce"),
                                    __("Users will be redirected to login page if not logged in","print-invoices-packing-slip-labels-for-woocommerce"),
                                    __("Secure","print-invoices-packing-slip-labels-for-woocommerce")
                                ),
                                'all'       => sprintf(
                                    '%1$s <br><span class="label_sub_text">%2$s</span>',
                                    __("All users","print-invoices-packing-slip-labels-for-woocommerce"),
                                    __("Users who haven`t logged will also be allowed to print","print-invoices-packing-slip-labels-for-woocommerce")
                                ),
                            ),
                            'alignment'     => 'vertical_with_label',
                        ),
                    );

                    $settings_arr['temp_files_cleanup'] = array(
                        'temp_file_path'    => array(
                            'type'  =>  'wt_temp_file_path',
                            'label' => __("File path","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),
                        'temp_file_total'   => array(
                            'type'  => 'wt_temp_file_total',
                            'label' => __("Total files","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),
                        'wt_pklist_auto_temp_clear'  => array(
                            'type'  => 'wt_radio',
                            'label' => __("Automatic cleanup","print-invoices-packing-slip-labels-for-woocommerce"),
                            'name'  =>  'wt_pklist_auto_temp_clear',
                            'id'    =>  'wt_pklist_auto_temp_clear',
                            'class' =>  'wt_pklist_auto_temp_clear',
                            'radio_fields'  => array(
                                'Yes'   => __("Yes","print-invoices-packing-slip-labels-for-woocommerce"),
                                'No'    => __("No","print-invoices-packing-slip-labels-for-woocommerce"),
                            ),
                            'form_toggler'=>array(
                                'type'=>'parent',
                                'target'=>'wt_pklist_auto_temp_clear_div',
                            ),
                        ),
                        'wt_pklist_auto_temp_clear_interval'    => array(
                            'type'  => 'wt_number',
                            'label' => __("Interval",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'name'  => "wt_pklist_auto_temp_clear_interval",
                            'class' => 'wt_pklist_auto_temp_clear_interval',
                            'attr' => 'min="1440"',
                            'form_toggler'=>array(
                                'type'  => 'child',
                                'val'   => 'Yes',
                                'id'    => 'wt_pklist_auto_temp_clear_div',
                                'lvl'   => 2,
                            ),
                            'help_text' => __("In minutes. Eg: 1440 for 1 day. Minimum value is 1440","print-invoices-packing-slip-labels-for-woocommerce"),
                        ),
                    );

                    $settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,'general',"","");
                    if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                        $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
                    }else{
                        $Form_builder = new WT_Form_Field_Builder();
                    }
                    foreach($settings_arr as $settings){
                        $Form_builder->generate_form_fields($settings);
                    }
                ?>
            </tbody>
        </table>
        <?php 
            include "admin-settings-save-button.php";
        ?>
    </form>
</div>