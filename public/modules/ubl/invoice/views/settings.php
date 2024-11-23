<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rates                       = \WC_Tax::get_tax_rate_classes();
$formatted_rates             = array();
$formatted_rates['standard'] = esc_html__( 'Standard', 'print-invoices-packing-slip-labels-for-woocommerce' );

foreach ( $rates as $rate ) {
    if ( empty( $rate->slug ) ) {
        continue;
    }
    $formatted_rates[ $rate->slug ] = ! empty( $rate->name ) ? esc_attr( $rate->name ) : esc_attr( $rate->slug );
}
?>
<style>
    .wt_pklist_ubl_tax_table th { font-style: normal; font-weight: 700; font-size: 14px; line-height: 17px; color: #6E7681; padding: 15px 10px; text-align: start; }
    .wt_pklist_ubl_tax_table tr th:first-child{ width:auto;  position: relative;font-weight: bold; }
    .wt_pklist_ubl_tax_table tr th{text-align:start; vertical-align: top !important;}
    .wt_pklist_ubl_tax_table tr td:nth-child(2){ width:auto; padding: 15px 10px; vertical-align:middle; }
    .wt_pklist_ubl_tax_table tr td:nth-child(3){ width:auto; }
    .wt_pklist_ubl_invoice_tax_class_name_list{ display: flex; flex-direction: row; align-items: center; }
    .wt_pklist_ubl_invoice_tax_class_name{ box-sizing: border-box; padding: 11px 18px; background: #FFFFFF; border: 1px solid #EAEBED; box-shadow: 0px 4px 12px rgba(85, 101, 125, 0.08); border-radius: 19px; margin-right: 7px; }
    .wt_pklist_ubl_tax_table{ width: 100%; border-collapse: collapse; margin-top: 20px; }
    .wt_pklist_ubl_tax_table{display: none;}
    .wt_pklist_ubl_invoice_tax_class_name.active{ background: #F1F8FE; border: 1px solid #CCE3FF; box-shadow: 0px 4px 12px rgba(85, 101, 125, 0.08); font-style: normal; font-weight: 500; font-size: 14px; line-height: 17px; color: #056BE7; cursor: pointer; }
    .wt_pklist_ubl_tax_table.active{ display: table; }
    table.wt_pklist_ubl_tax_table td{ text-align: start; padding: 15px 10px; font-size: 14px; vertical-align: middle; font-weight: normal; position: relative; }
    table.wt_pklist_ubl_tax_table tbody tr:nth-child(odd) { background-color: #f2f2f2; }
    table.wt_pklist_ubl_tax_table tbody tr:nth-child(even) { background-color: #ffffff; }
    table.wt_pklist_ubl_tax_table_empty tr{ background-color: #ffffff !important; }
</style>
<div class="wf-tab-content" data-id="ubl">
    <form method="post" class="wf_settings_form">
        <input type="hidden" value="ublinvoice" class="wf_settings_base" />
        <input type="hidden" value="wf_save_settings" class="wf_settings_action" />
        <input type="hidden" value="wt_invoice_ubl" name="wt_tab_name" class="wt_tab_name" />
        <table class="wf-form-table">
            <tbody>
                <?php
                    $settings_arr['invoice_ubl'] = array(
                        'wt_sub_head_inv_ubl' => array(
                            'type' => 'wt_sub_head',
                            'class' => 'wt_pklist_field_group_hd_sub',
                            'label' => __("General",'print-invoices-packing-slip-labels-for-woocommerce'),
                            'heading_number' => 1,
                            'ref_id' => 'wt_sub_head_1'
                        ),

                        'wt_pklist_ubl_invoice_enable' => array(
                            'type' => 'wt_single_checkbox',
                            'id' => 'wt_pklist_ubl_invoice_enable',
                            'class' => 'wt_pklist_ubl_invoice_enable',
                            'name' => 'wt_pklist_ubl_invoice_enable',
                            'value' => "Yes",
                            'checkbox_fields' => array('Yes'=> __("Enable to print, download and attach UBL (XML) invoices","print-invoices-packing-slip-labels-for-woocommerce")),
                            'label' => array(
                                'text' => __('Enable UBL Invoice',"print-invoices-packing-slip-labels-for-woocommerce"),
                            ),
                            'tooltip' => false,
                            'col' => 3,
                        ),

                        'wt_pklist_ubl_invoice_format' => array(
                            'type' => 'wt_select_dropdown',
                            'label' => __("Select UBL format","print-invoices-packing-slip-labels-for-woocommerce"),
                            'id' => "",
                            'name' => "wt_pklist_ubl_invoice_format",
                            'value' => "",
                            'select_dropdown_fields' => array(
                                    'ubl_peppol' => __("PEPPOL","print-invoices-packing-slip-labels-for-woocommerce"),
                                    "ubl_cius_at" => __("Austrian","print-invoices-packing-slip-labels-for-woocommerce"),
                                    "ubl_cius_it" => __("Italia","print-invoices-packing-slip-labels-for-woocommerce"),
                                    "ubl_cius_nl" => __("Netherlands","print-invoices-packing-slip-labels-for-woocommerce"),
                                    "ubl_cius_es" => __("Spanish","print-invoices-packing-slip-labels-for-woocommerce"),
                                    "ubl_cius_ro" => __("Romania","print-invoices-packing-slip-labels-for-woocommerce"),
                                ),
                            'class' => "",
                            'col' => 3,
                            'ref_id' => 'wt_pklist_ubl_invoice_format',
                        ),

                        'wt_pklist_ubl_invoice_attach_email_classes' => array(
                            'type' => 'wt_select2_checkbox',
                            'label' => __("Select email(s) to attach UBL invoice","print-invoices-packing-slip-labels-for-woocommerce"),
                            'name' => 'wt_pklist_ubl_invoice_attach_email_classes',
                            'id' => 'wt_pklist_ubl_invoice_attach_email_classes_st',
                            'value' => Wt_Pklist_Common::wt_pdf_get_wc_email_classes(),
                            'checkbox_fields' => Wt_Pklist_Common::wt_pdf_get_wc_email_classes(),
                            'class' => 'wt_pklist_ubl_invoice_attach_email_classes',
                            'col' => 3,
                            'placeholder' => __("Choose order status","print-invoices-packing-slip-labels-for-woocommerce"),
                            'help_text' => __("Select email classes to send UBL invoice as attachment. This option depends on the order statuses selected under the `Automate invoice creation` (Invoice/Packing > Invoice >General) setting. Ensure that the selected email classes match the order statuses for the invoice to be sent correctly.","print-invoices-packing-slip-labels-for-woocommerce"),
                            'alignment' => 'vertical_with_label',
                            'ref_id' => 'wt_pklist_ubl_invoice_attach_email_classes',
                        ),
                    );

                    $settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,'ubl',$this->parent_module_base,$this->parent_module_id);

                    if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
                        $Form_builder = new WT_Form_Field_Builder_PRO_Documents();
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
                <tr>
                    <td colspan="3">
                        <div class="wt_pklist_field_group_hd_sub">
                            <span style="background: #3157A6;color: #fff;border-radius: 25px;padding: 4px 9px;margin-right: 5px;">2</span> <?php echo esc_html__( 'Tax classification', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?>
                        </div>
                        <p class="wt_pklist_field_group_hd_sub_desc"><?php echo esc_html__( 'Map your WooCommerce tax classes to the appropriate UBL tax categories and schemes', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="wt_pklist_ubl_invoice_tax_class_details" style="margin-bottom: 15px;">
                            <div class="wt_pklist_ubl_invoice_tax_class_name_list">
                                <?php
                                    $tax_slug_first = true; // Initialize a flag for the first iteration
                                    foreach ( $formatted_rates as $slug => $name ) {
                                        // Check if this is the first iteration
                                        $slug_class = $tax_slug_first ? 'active' : ''; // Add 'active' class for the first item
                                        $tax_slug_first = false; // Set the flag to false after the first iteration
                                        echo '<div class="wt_pklist_ubl_invoice_tax_class_name ' . esc_attr( $slug_class ) . '" data-target-table-id="'.esc_attr( $slug ).'">'.esc_html($name).'</div>';
                                    }
                                ?>
                            </div>
                            <div class="wt_pklist_ubl_invoice_tax_class_name_details">
                                <?php
                                    $tax_settings = \Wf_Woocommerce_Packing_List::get_option('wt_pklist_ubl_invoice_taxes', $this->module_id);
                                    global $wpdb;
                                    $tax_table_first = true; // Initialize a flag for the first iteration
                                    foreach ( $formatted_rates as $slug => $name ) {
                                        // Check if this is the first iteration
                                        $table_class = $tax_table_first ? 'active' : ''; // Add 'active' class for the first item
                                        $tax_table_first = false; // Set the flag to false after the first iteration
                                        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_class = %s;", ( $slug == 'standard' ) ? '' : $slug ) );
                                        if ( ! empty( $results ) ) {
                                        ?>
                                    <table class="wt_pklist_ubl_tax_table <?php echo esc_attr( $table_class ); ?>" data-table-id="<?php echo esc_attr( $slug )?>">
                                        <thead>
                                            <tr>
                                                <th ><?php echo esc_html__( 'Country&nbsp;code', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></th>
                                                <th ><?php echo esc_html__( 'State code', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></th>
                                                <th ><?php echo esc_html__( 'Postcode / ZIP', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></th>
                                                <th ><?php echo esc_html__( 'City', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></th>
                                                <th ><?php echo esc_html__( 'Rate&nbsp;%', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></th>
                                                <th  style="width:20%; padding:15px;">
                                                    <div style="display: flex;justify-content: space-between;">
                                                        <p style="margin: 0;"><?php _e( 'Tax Scheme', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></p>
                                                        <a href="https://service.unece.org/trade/untdid/d00a/tred/tred5153.htm" target="_blank" style="text-decoration: none;">
                                                            <?php echo esc_html__( 'Learn more', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?> <span class="dashicons dashicons-external" style="font-size:16px;"></span>
                                                        </a>
                                                    </div>
                                                </th>
                                                <th style="width:20%; padding:15px;">
                                                    <div style="display: flex;justify-content: space-between;">
                                                        <p style="margin: 0;"><?php _e( 'Tax Category', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></p>
                                                        <a href="https://service.unece.org/trade/untdid/d97a/uncl/uncl5305.htm" target="_blank" style="text-decoration: none;">
                                                            <?php echo esc_html__( 'Learn more', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?> <span class="dashicons dashicons-external" style="font-size:16px;"></span>
                                                        </a>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="rates">
                                        <?php
                                            
                                                foreach ( $results as $result ) {
                                                    $locationResults = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rate_locations WHERE tax_rate_id = %d;", $result->tax_rate_id ) );
                                                    $postcode = $city = '';
                        
                                                    foreach ( $locationResults as $locationResult ) {
                                                        if ( 'postcode' === $locationResult->location_type ) {
                                                            $postcode = $locationResult->location_code;
                                                            continue;
                                                        }
                        
                                                        if ( 'city' === $locationResult->location_type ) {
                                                            $city = $locationResult->location_code;
                                                            continue;
                                                        }
                                                    }
                        
                                                    $scheme   = isset( $tax_settings['rate'][ $result->tax_rate_id ]['scheme'] )   ? $tax_settings['rate'][ $result->tax_rate_id ]['scheme']   : '';
                                                    $category = isset( $tax_settings['rate'][ $result->tax_rate_id ]['category'] ) ? $tax_settings['rate'][ $result->tax_rate_id ]['category'] : '';
                        
                                                    echo '<tr>';
                                                    echo '<td>'.$result->tax_rate_country.'</td>';
                                                    echo '<td>'.$result->tax_rate_state.'</td>';
                                                    echo '<td>'.$postcode.'</td>';
                                                    echo '<td>'.$city.'</td>';
                                                    echo '<td>'.$result->tax_rate.'</td>';
                                                    echo '<td>'.$this->render_select_option_for_schema( 'rate', $result->tax_rate_id, $scheme ).'</td>';
                                                    echo '<td>'.$this->render_select_option_for_category( 'rate', $result->tax_rate_id, $category ).'</td>';
                                                    echo '</tr>';
                                                }
                                        ?>
                                        </tbody>
                                    </table>
                                        <?php
                                        } else {
                                            ?>
                                            <table class="wt_pklist_ubl_tax_table wt_pklist_ubl_tax_table_empty <?php echo esc_attr( $table_class ); ?>" data-table-id="<?php echo esc_attr( $slug )?>">
                                                <tr>
                                                    <td colspan="7"><?php echo esc_html__( 'No taxes found for this class.', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></td>
                                                </tr>
                                            </table>
                                            <?php
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        
                    </td>
                </tr>
            </tbody>
        </table>
        <?php 
            include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
        ?>
    </form>
</div>
