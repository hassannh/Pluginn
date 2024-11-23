<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
// step 1 field values
$sample_logo    = WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
$wc_email_classes = Wt_Pklist_Common::wt_pdf_get_wc_email_classes( true ); // Get default wc email classes.
$invoice_module_id = Wf_Woocommerce_Packing_List::get_module_id('invoice');
$company_name = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_companyname');
$street         = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_address_line1');
$street_line_2  = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_address_line2');
$city           = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_city');
$country_arr    = Wf_Woocommerce_Packing_List::get_option('wf_country');
$postal_code    = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_postalcode');
$phone_no       = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_contact_number');
$company_tax_id = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_sender_vat');
$company_logo   = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_logo');
$logo_url       = !empty($company_logo) ? $company_logo : $sample_logo;
if( strstr( $country_arr, ':' ))
{
    $country_arr= explode( ':', $country_arr );
    $country    = current( $country_arr );
    $state      = end( $country_arr );                                            
}else 
{
    $country    = $country_arr;
    $state      = '*';
}

// step 2 field values
$attach_invoice = Wf_Woocommerce_Packing_List::get_option('wt_pdf_invoice_attachment_wc_email_classes',$invoice_module_id);
$invoice_no_type = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$invoice_module_id);
$invoice_no_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_format',$invoice_module_id);
$prefix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_prefix',$invoice_module_id);
$suffix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_postfix',$invoice_module_id);
$invoice_start_number = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_start_number',$invoice_module_id);
$invoice_no_length = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_padding_number',$invoice_module_id);
$date_frmt_tooltip=__('Click to append with existing data','print-invoices-packing-slip-labels-for-woocommerce');
$template_type = "invoice";
$query = new WC_Order_Query( array(
    'limit' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'parent'=>0,
) );

$orders = $query->get_orders();
$order_number = "123";
if(count($orders)>0)
{
    $order=$orders[0];
    $order_number=$order->get_order_number();
}

$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$invoice_module_id);
$current_invoice_number_in_db = $current_invoice_number=($current_invoice_number<0 ? 0 : $current_invoice_number);
$inv_num=++$current_invoice_number;
$use_wc_order_number = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$invoice_module_id);
?>
<style>
.wt_wrap{background-color: #F1F8FE;}
.wt_wrap_wizard_container_inner_empty_col{float: left;width: 10%;height: 1px;}
.wt_wrap_wizard_form{width: 85%;float: left;}
.wt_wrap_wizard_container{width: 90%;float: left;padding: 40px 15px 100px 15px;}
.wt_wrap_wizard_form_outter{float: left; width: 100%;background-color: #fff;}
.wt_wrap_wizard_form_steps{float: left; width: 100%;}
.wt_wrap_wizard_form_steps_progress{float: left;width: 10%;padding: 2em;}
.wt_wrap_wizard_form_steps_fields{float: left;width: 75%;padding: 2em;}
ul.progress-bar { height: 150px; list-style: none; margin: 0; padding: 0; position: relative; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;}
ul.progress-bar::after { content: ""; position: absolute; top: 0; left: 5px; background: transparent; width: 5px; height: 100vh; }
ul.progress-bar li { background: #f1f8fe; border-radius: 100px; width: 15px; height: 15px; z-index: 1; border: 1px solid #F1F8FE; position: relative;}
ul.progress-bar li.step_active{background: #056BE7;}
ul.progress-bar li span{ position: absolute; left: 20px; width: 5em;}
ul.progress-bar li::after { content: ""; position: absolute; bottom: 0; top: 15px; left: 6px; background: #F1F8FE; width: 3px; height: 50px; }
ul.progress-bar li:last-child::after {display: none;}
ul.progress-bar li.step_active::after{ background: #056be7; }
ul.progress-bar li.stop_active::after{background: #F1F8FE;}
.wt_form_wizard_field_col,.wt_form_wizard_field_row{width: 100%;float: left;}
.wt_form_wizard_field_col{margin: 0 1.5em 0 0;}
.wt_form_wizard_field_col label{width: 100%;float: left;padding: 5px 0;}
.wt_form_wizard_field_col input[type="text"]{width: 100%;float: left;padding: 5px;border-radius: 3px;border: 1.5px solid #BDC1C6;background-color: #FFF;}
.wt_form_wizard_field_col input[type="number"]{width: 85px;float: left;padding: 5px;border-radius: 3px;border: 1.5px solid #BDC1C6;background-color: #FFF;}
.wt_form_wizard_field_col select{width: 100%;float: left;padding: 5px;border-radius: 3px;border: 1.5px solid #BDC1C6;}
.wt_form_wizard_field_col_2{width: 40%;}
.wt_form_wizard_field_col_1{width: 100%;}
.wt_form_wizard_field_col_3{width: 30%;}
.wt_form_wizard_field_col_4{width: 22%;}
.wt_form_wizard_field_col_5{width: 15%;}
.wt_form_wizard_field_col_3_4{width: 70%;}
.wt_pdf_invoice_attachment_wc_email_classes_label{float: initial !important; cursor: pointer;}
.wt_wrap_wizard_form_steps h3{margin: 0 0 1em 0;}
.wt_form_wizard_help_text{font-style: italic;color: #6E7681;}
.wt_form_wizard_footer{float: left;width: 100%;}
.wt_form_wizard_prev,.wt_form_wizard_next,.wt_form_wizard_invoice_setup_skip,.wt_form_wizard_submit{float: right;}
.wt_pklist_btn_secondary{margin-right: 15px;}
.wt_pklist_checkbox_div{margin-bottom: 10px;}
</style>
<div class="wt_wrap">
    <div class="wt_heading_section">
        <?php
            //webtoffee branding
            include WF_PKLIST_PLUGIN_PATH.'/admin/views/admin-settings-branding.php';
        ?>
    </div>
    <div class="wt_wrap_wizard_container">
        <div class="wt_wrap_wizard_container_inner_empty_col"></div>
        <form class="wt_wrap_wizard_form" method="post">
            <?php 
                if (function_exists('wp_nonce_field'))
                {
                    wp_nonce_field('wt-pklist-form-wizard-'.WF_PKLIST_POST_TYPE);
                }
            ?>
            <h2><?php _e("Hello there! Let’s begin with the basics","print-invoices-packing-slip-labels-for-woocommerce").'...'; ?></h2>
            <div class="wt_wrap_wizard_form_outter">
                <div class="wt_wrap_wizard_form_steps">
                    <div class="wt_wrap_wizard_form_steps_progress">
                        <ul class="wt_form_wizard_progress_bar progress-bar">
                            <li class="wt_form_wizard_progress_step_1 step_active stop_active"><span><strong><?php _e("Step 1","print-invoices-packing-slip-labels-for-woocommerce"); ?></strong></span></li>
                            <li class="wt_form_wizard_progress_step_2"><span><strong><?php _e("Step 2","print-invoices-packing-slip-labels-for-woocommerce"); ?></strong></span></li>
                            <li class="wt_form_wizard_progress_step_3"><span><?php _e("Step 3","print-invoices-packing-slip-labels-for-woocommerce"); ?></span></li>
                        </ul>
                    </div>
                    <div class="wt_wrap_wizard_form_steps_fields" data-wizard-step="1">
                        <h3><?php _e("Add shop details","print-invoices-packing-slip-labels-for-woocommerce"); ?></h3>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Shop name","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_companyname" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($company_name); ?>">
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Address line 1","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_address_line1" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($street); ?>">
                            </div>
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Address line 2","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_address_line2" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($street_line_2); ?>">
                            </div>
                            
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("City","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_city" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($city); ?>">
                            </div>
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Country/State","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <select name="wf_country" class="wt_pklist_form_wizard_field">
                                    <option value=""><?php esc_attr_e("Select country","print-invoices-packing-slip-labels-for-woocommerce");?></option>
                                    <?php
                                        ob_start();
                                        WC()->countries->country_dropdown_options($country,$state);
                                        $html=ob_get_clean();
                                        echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_4 wt_form_wizard_field_col">
                                <label><?php _e("Postal code","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_postalcode" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($postal_code); ?>">
                            </div>
                            <div class="wt_form_wizard_field_col_3 wt_form_wizard_field_col">
                                <label><?php _e("Phone number","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_contact_number" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($phone_no); ?>">
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Tax ID","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input type="text" name="woocommerce_wf_packinglist_sender_vat" class="wt_pklist_form_wizard_field" value="<?php esc_attr_e($company_tax_id); ?>">
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <label><?php _e("Upload logo","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
                                <input id="woocommerce_wf_packinglist_logo" type="hidden" name="woocommerce_wf_packinglist_logo" value="<?php echo esc_url($company_logo); ?>">
                                <div class="wf_file_attacher_dv">
                                    <div class="wf_file_attacher_inner_dv">
                                        <span class="dashicons dashicons-dismiss wt_logo_dismiss"></span>
                                        <img class="wf_image_preview_small" src="<?php echo esc_url($logo_url); ?>">
                                    </div>
                                    <span class="size_rec"><?php _e("Recommended size is 150x50px.","print-invoices-packing-slip-labels-for-woocommerce"); ?></span>
                                    <input type="button" name="upload_image" class="wf_button button button-primary wf_file_attacher" wf_file_attacher_target="#woocommerce_wf_packinglist_logo" value="Upload">
                                </div>
                            </div>
                        </div>

                        <div class="wt_form_wizard_footer">
                            <a class="wt_form_wizard_next wt_pklist_btn wt_pklist_btn_primary" data-target-class="wt_wrap_wizard_form_steps_fields" data-wizard-step="1" data-wizard-next-step="2"><?php _e("Next","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                            <a class="wt_form_wizard_invoice_setup_skip wt_pklist_btn wt_pklist_btn_empty" href="<?php echo admin_url('admin.php?page=wf_woocommerce_packing_list&skip_wizard=1'); ?>"><?php _e("Skip invoice setup","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                        </div>
                    </div>
                    <div class="wt_wrap_wizard_form_steps_fields" data-wizard-step="2" style="display:none">
                        <h3><?php _e("Choose emails for invoice attachment","print-invoices-packing-slip-labels-for-woocommerce"); ?></h3>
                        <p><?php _e("Choose the order emails to which you'd like to attach invoices for your customers","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_2 wt_form_wizard_field_col">
                                <?php 
                                    foreach($wc_email_classes as $or_st => $or_st_label){ 
                                        $checked = in_array($or_st, $attach_invoice) ? 'checked' : '';
                                ?>
                                    <div class="wt_pklist_checkbox_div">
                                        <input type="checkbox" name="wt_pdf_invoice_attachment_wc_email_classes[]" value="<?php esc_attr_e($or_st); ?>" id="<?php echo esc_attr('wt_pdf_invoice_attachment_wc_email_classes_label_'.$or_st); ?>" <?php echo esc_attr($checked); ?>>
                                        <label class="wt_pdf_invoice_attachment_wc_email_classes_label" for="<?php echo esc_attr('wt_pdf_invoice_attachment_wc_email_classes_label_'.$or_st); ?>"> <?php esc_html_e($or_st_label); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="wt_form_wizard_footer">
                            <a class="wt_form_wizard_next wt_pklist_btn wt_pklist_btn_primary" data-target-class="wt_wrap_wizard_form_steps_fields" data-wizard-step="2" data-wizard-next-step="3"><?php _e("Next","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                            <a class="wt_form_wizard_prev wt_pklist_btn wt_pklist_btn_secondary" data-target-class="wt_wrap_wizard_form_steps_fields" data-wizard-step="2" data-wizard-prev-step="1"><?php _e("Back","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                        </div>
                    </div>
                    <div class="wt_wrap_wizard_form_steps_fields" data-wizard-step="3" style="display:none">
                        <h3><?php _e("Create your unique invoice numbering system","print-invoices-packing-slip-labels-for-woocommerce"); ?></h3>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_1 wt_form_wizard_field_col">
                                <p><?php _e("Complete the invoice number format to suit your requirements","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            </div>
                            <div class="wt_form_wizard_field_col_4 wt_form_wizard_field_col">
                                <input type="hidden" name="woocommerce_wf_invoice_number_format_pdf_fw" value="<?php echo esc_attr($invoice_no_format); ?>">
                                <div class="choose_date_div">
                                    <input type="text" name="woocommerce_wf_invoice_number_prefix_pdf_fw" placeholder="<?php _e("Prefix","print-invoices-packing-slip-labels-for-woocommerce"); ?>" value="<?php echo esc_attr($prefix); ?>">
                                    <img class="choose_date_img" data-target-id="woocommerce_wf_invoice_number_prefix_pdf_fw" src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/images/choose_date.png'); ?>">
                                    <a class="choose_date_drop_down" data-target-id="woocommerce_wf_invoice_number_prefix_pdf_fw"><?php _e("Choose date","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                                </div>
                            </div>
                            <div class="wt_form_wizard_field_col_4 wt_form_wizard_field_col">
                                <select name="woocommerce_wf_invoice_as_ordernumber_pdf_fw">
                                    <option value="Yes" <?php echo "Yes" === $invoice_no_type ? 'selected' : ''; ?>><?php _e("Order number","print-invoices-packing-slip-labels-for-woocommerce"); ?></option>
                                    <option value="No" <?php echo "No" === $invoice_no_type ? 'selected' : ''; ?>><?php _e("Custom number","print-invoices-packing-slip-labels-for-woocommerce"); ?></option>
                                </select>
                            </div>
                            <div class="wt_form_wizard_field_col_4 wt_form_wizard_field_col">
                                <div class="choose_date_div">
                                    <input type="text" class="wt_pklist_inv_no_suffix" name="woocommerce_wf_invoice_number_postfix_pdf_fw" placeholder="<?php _e("Suffix","print-invoices-packing-slip-labels-for-woocommerce"); ?>" value="<?php echo esc_attr($suffix); ?>">
                                    <img class="choose_date_img" data-target-id="woocommerce_wf_invoice_number_postfix_pdf_fw" src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/images/choose_date.png'); ?>">
                                    <a class="choose_date_drop_down" data-target-id="woocommerce_wf_invoice_number_postfix_pdf_fw"><?php _e("Choose date","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row wc_custom_no_div">
                            <div class="wt_form_wizard_field_col_1 wt_form_wizard_field_col">
                                <p><?php _e("What should be the starting number for your invoices?","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            </div>
                            <div class="wt_form_wizard_field_col_5 wt_form_wizard_field_col">
                                <input type="number" name="woocommerce_wf_invoice_start_number_preview_pdf_fw" value="<?php echo esc_attr($invoice_start_number); ?>" min="0">
                                <input type="hidden" name="woocommerce_wf_invoice_start_number_pdf_fw" value="<?php echo esc_attr($invoice_start_number); ?>" min="0">
                                <input type="hidden" class="wf_current_invoice_number_pdf_fw" value="<?php echo esc_attr($current_invoice_number_in_db); ?>" name="woocommerce_wf_Current_Invoice_number_pdf_fw" class="">
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_1 wt_form_wizard_field_col">
                                <p><?php _e("What length would you prefer for your invoice number","print-invoices-packing-slip-labels-for-woocommerce"); ?></p>
                            </div>
                            <div class="wt_form_wizard_field_col_5 wt_form_wizard_field_col">
                                <input type="number" name="woocommerce_wf_invoice_padding_number_pdf_fw" value="<?php echo esc_attr($invoice_no_length); ?>" min="0">
                            </div>
                        </div>
                        <div class="wt_form_wizard_field_row">
                            <div class="wt_form_wizard_field_col_1 wt_form_wizard_field_col">
                                <input type="hidden" value="<?php echo esc_attr($order_number); ?>" id="sample_invoice_number_pdf_fw">
                                <input type="hidden" id="sample_current_invoice_number_pdf_fw" value="<?php echo esc_attr($current_invoice_number); ?>">
                                <div id="invoice_number_prev_div" style="width: auto;border: 1px solid #dadadc;padding: 5px 12px;border-radius: 5px;display: inline-block;background: #f0f0f1;margin-top:25px;">
                                    <p style="font-weight: bold;line-height: 0;">
                                        <?php echo __('PREVIEW','print-invoices-packing-slip-labels-for-woocommerce'); ?>
                                    </p>
                                    <p style="margin: 1em  0 0.5em 0; <?php if("No" === $use_wc_order_number){ echo "display: none;"; }?>" id="preview_invoice_number_text">
                                        <?php echo __('If the order number is','print-invoices-packing-slip-labels-for-woocommerce'); ?> <?php echo $order_number; ?>, 
                                        <br> 
                                        <?php echo sprintf(__("the %s number would be",'print-invoices-packing-slip-labels-for-woocommerce'),$template_type); ?> 
                                    </p>
                                    <p style="margin: 1em  0 0.5em 0; <?php if("Yes" === $use_wc_order_number){ echo "display: none;"; }?>" id="preview_invoice_number_text_custom">
                                        <?php echo sprintf(__('Your next %s number would be','print-invoices-packing-slip-labels-for-woocommerce'),$template_type); ?>
                                    </p>
                                    <span id="preview_invoice_number_pdf_fw" style="background: #ffffff;padding: 5px;color: #3c434a;border-radius: 3px;float: left;font-weight: bold;margin-bottom: 0.5em;"></span>    
                                </div>
                            </div>
                        </div>
                        <div class="wt_form_wizard_footer">
                            <a class="wt_form_wizard_submit wt_pklist_btn wt_pklist_btn_primary"><?php _e("Finish setup","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                            <a class="wt_form_wizard_prev wt_pklist_btn wt_pklist_btn_secondary" data-wizard-step="3" data-target-class="wt_wrap_wizard_form_steps_fields" data-wizard-prev-step="2"><?php _e("Back","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="wt_wrap_wizard_container_inner_empty_col"></div>
    </div>
</div>
<div class="wf_inv_num_frmt_hlp_fw wf_pklist_popup" style="width: 365px;">
    <div class="wf_pklist_popup_hd">
        <span class="popup_title"><?php _e('Add date to invoice number','print-invoices-packing-slip-labels-for-woocommerce');?></span>
        <div class="wf_pklist_popup_close"><span class="dashicons dashicons-dismiss"></span></div>
    </div>
    <div class="wf_pklist_popup_body">
        <table class="wp-list-table widefat choose_date_table">
            <thead>
                <tr>
                    <th><?php _e('Date format strings','print-invoices-packing-slip-labels-for-woocommerce');?></th><th><?php _e('Output','print-invoices-packing-slip-labels-for-woocommerce');?></th><th></th>
                </tr>
            </thead>
            <tbody>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[F]</a></td>
                    <td class="date_format_text"><?php echo date('F'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[dS]</a></td>
                    <td class="date_format_text"><?php echo date('dS'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[M]</a></td>
                    <td class="date_format_text"><?php echo date('M'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[m]</a></td>
                    <td class="date_format_text"><?php echo date('m'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d]</a></td>
                    <td class="date_format_text"><?php echo date('d'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[D]</a></td>
                    <td class="date_format_text"><?php echo date('D'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[y]</a></td>
                    <td class="date_format_text"><?php echo date('y'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[Y]</a></td>
                    <td class="date_format_text"><?php echo date('Y'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d/m/y]</a></td>
                    <td class="date_format_text"><?php echo date('d/m/y'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
                <tr class="wf_inv_num_frmt_fw_append_btn_tr">
                    <td><a class="wf_inv_num_frmt_fw_append_btn" title="<?php echo $date_frmt_tooltip; ?>">[d-m-Y]</a></td>
                    <td class="date_format_text"><?php echo date('d-m-Y'); ?></td>
                    <td class="date_format_add"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="wt_pklist_form_wizard_success wf_pklist_popup" style="border-radius:8px;">
    <div class="wf_pklist_popup_body">    
        <div style="background-color: #89c98b;border-radius:50%;margin: 2em 8em 0em;">
            <img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/images/fm_wz_success.png'); ?>">
        </div> 
        <div>
            <p style="font-size: 16px;font-style: normal;font-weight: 600;line-height: 28px;"><?php _e("Invoice setup successfully","print-invoices-packing-slip-labels-for-woocommerce"); ?>
        </div>
        <div style="margin-bottom: 2em;">
            <a href="<?php echo admin_url('admin.php?page=wf_woocommerce_packing_list&complete_wizard=1'); ?>" class="wt_pklist_btn wt_pklist_btn_primary"><?php _e("Close","print-invoices-packing-slip-labels-for-woocommerce"); ?></a>
        </div>
    </div>         
</div>