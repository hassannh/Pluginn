<?php
namespace Wtpdf\Ubl\Documents;

use Wf_Woocommerce_Packing_List;
use Wt_Pklist_Common;
use Wtpdf\Ubl\WtPdfUblGenerator;
use Wtpdf\Ubl\Tax\Schema;
use Wtpdf\Ubl\Tax\Category;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\Ubl\\Documents\\Invoice' ) ) {
class Invoice extends WtPdfUblGenerator {
    public $tax_schemas = array();
    public $tax_categories = array();
    public $module_title = '';
    public $module_id = '';
    public $module_base = '';
    public $parent_module_id = '';
    public $parent_module_base = 'invoice';
    public $module_version = WF_PKLIST_VERSION;
    public $settings = array();
    public $ubl_format = null;
    public $attachments = array();
    public $parent_reflection_class = null;

    public static $instance = null;

    public function __construct() {

        $this->module_version = WF_PKLIST_VERSION;
        $this->parent_module_base = 'invoice';
        $this->module_base = 'ublinvoice';
        $this->parent_module_id = \Wf_Woocommerce_Packing_List::get_module_id( $this->parent_module_base );
        $this->module_id = \Wf_Woocommerce_Packing_List::get_module_id( $this->module_base );
        $this->module_title = __( 'UBL Invoice', 'print-invoices-packing-slip-labels-for-woocommerce' );

        $this->tax_schemas = $this->get_tax_schemas();
        $this->tax_categories = $this->get_tax_categories();

        // add the scripts to the admin page.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // add ubl tab and its content in the invoice settings page.
        add_filter( 'wt_pklist_add_additional_tab_item_into_module', array( $this, 'add_additional_tab' ), 10, 3 );
        add_action( 'wt_pklist_add_additional_tab_content_into_module', array($this, 'add_additional_tab_content'), 10, 2 );

        // hooks for ubl invoice settings
        add_filter( 'wf_module_default_settings', array( $this,'default_settings' ),10,2 );
        add_filter( 'wf_module_single_checkbox_fields', array( $this, 'single_checkbox_fields' ), 10, 3 );
        add_filter( 'wf_module_multi_checkbox_fields', array( $this, 'multi_checkbox_fields' ), 10, 3 );
        add_filter( 'wf_module_save_multi_checkbox_fields', array( $this, 'save_multi_checkbox_fields' ), 10, 4 );
        add_filter( 'wt_pklist_intl_alter_validation_rule', array( $this,'alter_validation_rule' ), 10, 2 );

        if ( 'Yes' === \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_ubl_invoice_enable', $this->module_id ) && 'Yes' === \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_enable_invoice', $this->parent_module_id ) ) {

            // Show the print and download buttons for UBL Invoice in admin order details page.
            add_filter( 'wt_print_actions', array( $this, 'add_print_buttons' ), 10, 4 );

            // Append the UBL Invoice as the option for the fields to show the dedicated print button in the admin order listing page.
            add_filter( 'wt_pklist_individual_print_button_for_document_types', array( $this, 'add_individual_print_button_in_admin_order_listing_page' ), 11, 1 );

            // Show the UBL Invoice print button in the admin order listing page action column.
            add_filter( 'woocommerce_admin_order_actions_end', array($this, 'document_print_btn_on_wc_order_listing_action_column' ), 11, 1 );

            // Add the action to print the UBL Invoice.
            add_action( 'wt_print_doc', array( $this, 'print_it' ), 10, 2 );

            // Add the attachment for the UBL Invoice in the order email.
            add_filter( 'wt_email_attachments', array( $this,'add_email_attachments' ), 12, 4 );
        }
    }

    /**
     * Get the single instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the tax schemas.
     *
     * This method retrieves the tax schemas and applies the 'wt_pdf_ubl_tax_schemas' filter to allow customization.
     *
     * @return void
     *
     * @hook wt_pdf_ubl_tax_schemas Allows filtering of the tax schemas.
     * @param array $schema->tax_schemas The array of tax schemas.
     * @param string $this->module_base The base module identifier.
     * @param string $this->parent_module_base The parent module base identifier.
     */
    public function get_tax_schemas(){
        $schema = new \Wtpdf\Ubl\Tax\Schema();
        return apply_filters( 'wt_pdf_ubl_tax_schemas', $schema->tax_schemas, $this->module_base, $this->parent_module_base  );
    }

    /**
     * Get the tax categories.
     *
     * This method retrieves the tax categories and applies the 'wt_pdf_ubl_tax_categories' filter.
     *
     * @return array The filtered tax categories.
     *
     * @hook wt_pdf_ubl_tax_categories
     * @param array $categories->tax_categories The original tax categories.
     * @param string $this->module_base The base module.
     * @param string $this->parent_module_base The parent module base.
     */
    public function get_tax_categories(){
        $categories = new \Wtpdf\Ubl\Tax\Category();
        return apply_filters( 'wt_pdf_ubl_tax_categories', $categories->tax_categories, $this->module_base, $this->parent_module_base  );
    }

    /**
     * Enqueue the scripts for the UBL Invoice.
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->module_id, plugin_dir_url( __FILE__ ) . 'assets/js/settings.js', array( 'jquery' ), $this->module_version, false );
    }

    /**
     * Add additional tab head in the invoice settings page.
     *
     * @param array $tab_items
     * @param string $template_type
     * @param string $base_id
     * @return array
     */
    public function add_additional_tab( $tab_items, $template_type, $base_id ) {
        if( $base_id === $this->parent_module_id ) {
            $new_element = array(
                'ubl' => __('UBL (Beta)','print-invoices-packing-slip-labels-for-woocommerce'),
            );
            $tab_items = \Wf_Woocommerce_Packing_List_Admin::wt_add_array_element_to_position($tab_items,$new_element,'general');
        }
        return $tab_items;
    }

    /**
     * Add additional tab content in the invoice settings page.
     *
     * @param string $template_type
     * @param string $base_id
     * @return void
     */
    public function add_additional_tab_content( $template_type, $base_id ) {
        if ( $base_id === $this->parent_module_id && $template_type === $this->parent_module_base ) {
            $this->settings = get_option( 'wt_pklist_pdf_settings_ubl_taxes', array() );
            include_once( plugin_dir_path( __FILE__ ) . 'views/settings.php' );
        }
    }

    /**
     * Render select option for tax schema
     *
     * @param string $type
     * @param string $id
     * @param string $selected
     * @return string
     */
    public function render_select_option_for_schema( $type, $id, $selected ) {
        $select = '<select name="' . esc_attr( 'wt_pklist_ubl_invoice_taxes[' . $type . '][' . $id . '][scheme]' ) . '"><option value="">' . esc_html__( 'Default', 'print-invoices-packing-slip-labels-for-woocommerce' ) . '</option>';
        foreach ( $this->tax_schemas as $key => $value ) {
            $select .= '<option ' . selected( $key, $selected, false ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /**
     * Render select option for tax category
     *
     * @param string $type
     * @param string $id
     * @param string $selected
     * @return string
     */
    public function render_select_option_for_category( $type, $id, $selected ) {
        $select = '<select name="' . esc_attr( 'wt_pklist_ubl_invoice_taxes[' . $type . '][' . $id . '][category]' ) . '"><option value="">' . esc_html__( 'Default', 'print-invoices-packing-slip-labels-for-woocommerce' ) . '</option>';
        foreach ( $this->tax_categories as $key => $value ) {
            $select .= '<option ' . selected( $key, $selected, false ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
        }
        $select .= '</select>';
        return $select;
    }

    /**
     * Set default settings for ubl invoice
     *
     * @param array $settings
     * @param array $module_id
     * @return array
     */
    public function default_settings( $settings, $module_id ) {
        if ( $module_id === $this->module_id ) {
            $settings = array(
                'wt_pklist_ubl_invoice_enable'  => 'No',
                'wt_pklist_ubl_invoice_format'  => 'ubl_peppol',
                'wt_pklist_ubl_invoice_attach_email_classes' => array(),
                'wt_pklist_ubl_invoice_taxes' => array(
                    'class' => array(),
                    'rate'  => array(),
                ),
            );
            return $settings;
        }
        return $settings;
    }

    /**
     * Set the values for single checkbox fields for ubl invoice, when unchecked.
     *
     * @param array $settings
     * @param string $base_id
     * @param string $tab_name
     * @return array
     */
    public function single_checkbox_fields($settings,$base_id,$tab_name) {
        if( $base_id === $this->module_id ) {	
            // array of fields with their unchecked values.
            $settings['wt_invoice_ubl'] = array(
               'wt_pklist_ubl_invoice_enable'  => 'No',
            );
        }
        return $settings;
    }

    /**
     * Set the values for multi checkbox fields for ubl invoice, when unchecked.
     *
     * @param array $settings
     * @param string $base_id
     * @param string $tab_name
     * @return array
     */
    public function multi_checkbox_fields( $settings, $base_id, $tab_name ) {
        if( $base_id === $this->module_id ) {
            $settings['wt_invoice_ubl'] = array(
                'wt_pklist_ubl_invoice_attach_email_classes' => array(),
                'wt_pklist_ubl_invoice_taxes' => array(
                    'class' => array(),
                    'rate'  => array(),
                ),
            );
        }
        return $settings;
    }

    /**
     * Save the multi checkbox fields for ubl invoice, when unchecked.
     *
     * @param int|string|float|array $result
     * @param int|string $key
     * @param array $fields
     * @param string $base_id
     * @return int|string|float|array
     */
    public function save_multi_checkbox_fields($result,$key,$fields,$base_id){
        if( $base_id === $this->module_id ){
            $result = (isset($fields[$key]) && !isset($_POST[$key])) ? $fields[$key] : $result;
        }
        return $result;
    }

    /**
     * Set the validation rule for the ubl invoice settings.
     *
     * @param array $arr
     * @param string $base_id
     * @return array
     */
    public function alter_validation_rule($arr, $base_id) {
        if( $base_id === $this->module_id ) {
            $arr = array(
                'wt_pklist_ubl_invoice_attach_email_classes' => array( 'type' => 'text_arr' ),
                'wt_pklist_ubl_invoice_taxes' => array( 'type' => 'text_arr' ),
            );
        }
        return $arr;
    }

    /**
     * Add print buttons for UBL Invoice
     *
     * @param array $item_arr
     * @param array|object $order
     * @param int|string $order_id
     * @param string $button_location
     * @return array
     */
    public function add_print_buttons( $item_arr, $order, $order_id, $button_location ) {
        
        if ( !empty( $order ) ) {
            if ('detail_page' === $button_location) {
                
                $is_show_prompt = 1;
                $document_exists = \Wf_Woocommerce_Packing_List_Admin::check_doc_already_created( $order, $order_id, 'ublinvoice' );
                $generate_invoice_for = Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_generate_for_orderstatus', $this->parent_module_id );
                $order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
                $invoice_number = Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_number', true );

                /**
                 * If the order status is in the generate invoice for array or if the invoice number is not empty, then set the is_show_prompt to 0.
                 */
                if ( in_array( 'wc-' . $order_status, $generate_invoice_for ) || !empty( $invoice_number ) ) {
                    $is_show_prompt = 0;
                }

                /**
                 * If the is_show_prompt is 1, then set the document_exists to false.
                 */
                if ( 1 === $is_show_prompt) {
                    $document_exists = false;
                }

                //for print button
                $btn_args = array(
                    'action' => 'print_ublinvoice',
                    'tooltip' => __('Print UBL Invoice', 'print-invoices-packing-slip-labels-for-woocommerce'),
                    'is_show_prompt' => $is_show_prompt,
                    'button_location' => $button_location,
                    'label' => __('Print','print-invoices-packing-slip-labels-for-woocommerce'),
                );
                
                $download_btn_args = array(
                    'action' => 'download_ublinvoice',
                    'tooltip' => __('Download UBL Invoice', 'print-invoices-packing-slip-labels-for-woocommerce'),
                    'is_show_prompt' => $is_show_prompt,
                    'button_location' => $button_location,
                    'label' => __('Download','print-invoices-packing-slip-labels-for-woocommerce'),
                );

                $item_arr['ubl_invoice_details_actions'] = array(
                    'button_type' => 'aggregate',
                    'button_key' => 'invoice_actions', //unique if multiple on same page
                    'button_location' => $button_location,
                    'action' => '',
                    'label' => __('UBL Invoice', 'print-invoices-packing-slip-labels-for-woocommerce'),
                    'is_show_prompt' => 0, //always 0
                    'items' => array(
                        'print_ublinvoice' => $btn_args,
                        'download_ublinvoice' => $download_btn_args,
                    ),
                    'exist' => $document_exists,
                );
            }
        }
        
        return $item_arr;
    }

    /**
     * Add the option as one of the option in the field ( in general settings page ) to show the dedicated print button in the admin order listing page.
     *
     * @param array $documents
     * @return array
     */
    public function add_individual_print_button_in_admin_order_listing_page( $documents ) {
        if( !in_array( $this->module_base, $documents ) ) {
			$documents[$this->module_base] = __("UBL Invoice","print-invoices-packing-slip-labels-for-woocommerce");
		}
        return $documents;
    }

    /**
	 * Add document print button as per the 'wt_pklist_separate_print_button_enable' value
	 *
	 * @since 4.7.0
	 * @param object $order
	 * @return void
	 */
    public function document_print_btn_on_wc_order_listing_action_column( $order ) {
        $show_print_button = apply_filters( 'wt_pklist_show_document_print_button_action_column_free', true, $this->module_base, $order );

        if ( !empty( $order ) && true === $show_print_button ) {
            $order_id = ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id();

            if ( in_array( $this->module_base, \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_separate_print_button_enable' ) ) ) {
                $invoice_number = Wt_Pklist_Common::get_order_meta( $order_id, 'wf_invoice_number', true );
                $btn_action_name = 'wt_pklist_print_document_' . $this->module_base . '_not_yet';
                $img_url = WF_PKLIST_PLUGIN_URL . 'admin/images/' . $this->module_base . '.png';
                $invoice_no_set = true;
                
                if ( empty( $invoice_number ) ) {
                    $invoice_no_set = false;
                }

                if ( true === \Wf_Woocommerce_Packing_List_Admin::check_doc_already_created( $order, $order_id, 'ublinvoice' ) && $invoice_no_set ) {
                    $btn_action_name = 'wt_pklist_print_document_' . $this->module_base;
                    $img_url = WF_PKLIST_PLUGIN_URL . 'admin/images/' . $this->module_base . '_logo.png';
                }

                $action = 'print_' . $this->module_base;
                $action_title = sprintf( '%1$s %2$s',
                    __( "Print", "print-invoices-packing-slip-labels-for-woocommerce" ),
                    $this->module_title
                );
                $print_url = \Wf_Woocommerce_Packing_List_Admin::get_print_url( $order_id, $action );
                if ( $invoice_no_set ) {
                    echo '<a title="' . esc_attr( $action_title ) . '" class="button wc-action-button wc-action-button-' . esc_attr( $btn_action_name ) . ' ' . esc_attr( $btn_action_name ) . ' wt_pklist_action_btn" href="' . esc_url_raw( $print_url ) . '" aria-label="' . esc_attr( $action_title ) . '" target="_blank" style="padding:5px;"><img src="' . esc_url( $img_url ) . '"></a>';
                } else {
                    $is_show_prompt = 1;
                    $onlick = "return wf_Confirm_Notice_for_Manually_Creating_Ubl_Invoicenumbers('".esc_url_raw($print_url)."',$is_show_prompt)";
                    echo '<a title="' . esc_attr( $action_title ) . '" class="button wc-action-button wc-action-button-' . esc_attr( $btn_action_name ) . ' ' . esc_attr( $btn_action_name ) . ' wt_pklist_action_btn" onclick="' . esc_attr( $onlick ) . '" aria-label="' . esc_attr( $action_title ) . '" target="_blank" style="padding:5px;"><img src="' . esc_url( $img_url ) . '"></a>';
                    echo '<a class="wt_pklist_empty_number" data-template-type="' . esc_attr( $this->module_base ) . '" data-id="' . esc_attr( $order_id ) . '" style="display:none;"></a>';
                }
                
            }
        }
    }

    /**
     * To proceed the rendering of the UBL Invoice as per the action
     *
     * @param array $order_ids
     * @param string $action
     * @return void
     */
    public function print_it( $order_ids, $action ) {
        
        if ( 'print_ublinvoice' === $action || 'download_ublinvoice' === $action ) {
            
            foreach ( $order_ids as $order_id ) {
                $order = wc_get_order( $order_id );

                // load the chosen UBL format.
                $this->get_ubl_format( $order );

                // Render the UBL Invoice as per the chosen UBL format.
                $this->generate_ubl_invoice( $order, $action );
            }
            exit;
        }
    }

    /**
     * To get the chosen UBL format
     *
     * @param object $order
     * @return void
     */
    public function get_ubl_format( $order ) {
        $available_formats = array( 
            'ubl_peppol' => array(
                'file_name' => 'class-ubl-peppol.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblPeppol',
            ),
            "ubl_cius_at" => array(
                'file_name' => 'class-ubl-cius-at.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblCiusAt',
            ),
            "ubl_cius_it" => array(
                'file_name' => 'class-ubl-cius-it.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblCiusIt',
            ),
            "ubl_cius_nl" => array(
                'file_name' => 'class-ubl-cius-nl.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblCiusNl',
            ),
            "ubl_cius_es" => array(
                'file_name' => 'class-ubl-cius-es.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblCiusEs',
            ),
            "ubl_cius_ro" => array(
                'file_name' => 'class-ubl-cius-ro.php',
                'class_name' => 'Wtpdf\\Ubl\\Invoice\\Formats\\UblCiusRo',
            ),
        );
        $chosen_format = \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_ubl_invoice_format', $this->module_id );

        if ( !empty( $chosen_format ) && isset( $available_formats[$chosen_format] ) ) {
            $format_file = plugin_dir_path( __FILE__ ) . 'formats/' . $available_formats[$chosen_format]['file_name'];
            if ( file_exists( $format_file ) ) {
                require_once $format_file;
                $class_name = $available_formats[$chosen_format]['class_name'];
                if ( class_exists( $class_name ) ) {
                    $this->ubl_format = $class_name::instance( $order );
                }
            }
        }
    }

    /**
     * Generate UBL Invoice depending on the action
     *
     * @param object $order
     * @param string $action
     * @return void
     */
    public function generate_ubl_invoice( $order, $action ) {
        
        if ( !empty( $order ) ) {
            $order_id = $order->get_id();
            $data = $this->ubl_format->get_formatted_elements();
            $data = $this->sanitize_nested_array_data( $data );
            $document_namespace_array = $this->ubl_format->get_document_namespace();
            $actual_data = array();

            foreach ($data as $key => $item) {
                // Check if 'enabled' is set to 1 and 'value_arr' is not empty
                if (isset($item['enabled']) && true === $item['enabled'] && !empty($item['value_arr'])) {
                    $actual_data[] = $item['value_arr']; // Add 'value' to $data array
                }
            }
            
            $xmlconvert = \UBLXML\Converter::newInstance('Invoice', null, $document_namespace_array );
            $xmlCo = new \Wtpdf\Ubl\Lib\ArrayToXmlConverter();
            $xmlContent = $xmlCo->convertToXml( $actual_data, $xmlconvert );
            
            // Set the file is already created
            $document_created = \Wf_Woocommerce_Packing_List_Admin::created_document_count( $order_id, $this->module_base ); 

             // Define the name of the file to be downloaded (change this as needed)
             $filename = \Wf_Woocommerce_Packing_List_Admin::get_invoice_pdf_name( $this->parent_module_base , array($order), $this->parent_module_id);
             $filename = apply_filters( 'wt_pklist_ubl_invoice_pdf_name', $filename, $order, $this->parent_module_base, $this->parent_module_id );
            
            if ( 'download_ublinvoice' === $action ) {
                $xmlHandler = new \Wtpdf\Ubl\Handler\XmlFileHandler();
                $xmlHandler->generate_xml( $xmlContent, 'invoice', $filename, 'download', $order );
            } else if ( 'attach_ublinvoice' === $action ) {
                $xmlHandler = new \Wtpdf\Ubl\Handler\XmlFileHandler();
                return $xmlHandler->generate_xml( $xmlContent, 'invoice', $filename, 'attach', $order );
            } else {
                header('Content-Type: application/xml');  // Sets the content type as XML
                header('Content-Disposition: inline; filename="' . $filename . '.xml"');  // Sets the file name if saved
                echo $xmlContent;
                exit;
            }
        }
    }

    /**
     * Get the ubl array formatted invoice number
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_invoice_number( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:ID',
            'value' => $this->get_invoice_number( $order ),
        );
    }

    /**
     * Get the invoice number of the order
     *
     * @param object $order
     * @return string|int
     */
    public function get_invoice_number( $order ) {
        return \Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_number', true );
    }

    /**
     * Get the ubl array formatted issued date
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_issue_date( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:IssueDate',
            'value' => $this->get_issue_date( $order ),
        );
    }

    /**
     * Get the issued date of the order
     */
    public function get_issue_date( $order ) {
        $date_format = apply_filters( 'wt_pklist_ubl_invoice_date_format', 'Y-m-d', 'issue_date', $order );
        return !empty( \Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_date', true ) ) ? date( $date_format, \Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_date', true ) ) : '' ;
    }

    /**
     * Get the ubl array formatted due date
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_due_date( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:DueDate',
            'value' => $this->get_due_date( $order ),
        );
    }

    /**
     * Get the due date of the order
     */
    public function get_due_date( $order ) {
        $date_format = apply_filters( 'wt_pklist_ubl_invoice_date_format', 'Y-m-d', 'due_date', $order );
        return !empty( \Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_due_date', true ) ) ? date( $date_format, \Wt_Pklist_Common::get_order_meta( $order, 'wf_invoice_due_date', true ) ) : '' ;
    }

    /**
     * Get the ubl array formatted notes
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_notes( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:Note',
            'value' => $this->get_invoice_notes( $order ),
        );
    }

    /**
     * Get the notes of the order
     */
    public function get_invoice_notes( $order ) {
        return array();
    }

    /**
     * Get the ubl array formatted tax points date
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_tax_points_date( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:TaxPointDate',
            'value' => $this->get_tax_points_date( $order ),
        );
    }

    /**
     * Get the tax points date of the order
     */
    public function get_tax_points_date( $order) {
        return '';
    }

    /**
     * Get the ubl array formatted order currency code
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_currency_code( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:DocumentCurrencyCode',
            'value' => $this->get_currency_code( $order ),
        );
    }

    /**
     * Get the ubl array formatted tax currency code
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_tax_currency_code( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:TaxCurrencyCode',
            'value' => $this->get_currency_code( $order, 'tax' ),
        );
    }

    /**
     * Get the currency code of the order or tax
     */
    public function get_currency_code( $order, $currency_for = '' ) {
        if ( 'tax' === $currency_for ) {
            return $order->get_currency();
        } else {
            return $order->get_currency();
        }
    }

    /**
     * Get the ubl array formatted buyer accounting reference
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_buyer_accounting_reference( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:AccountingCost',
            'value' => $this->get_buyer_accounting_reference( $order ),
        );
    }

    /**
     * Get the buyer accounting reference of the order
     */
    public function get_buyer_accounting_reference( $order ) {
        return '';
    }

    /**
     * Get the ubl array formatted buyer reference
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_buyer_reference( $order, $ubl_format ): array {
        return array(
            'name' => 'cbc:BuyerReference',
            'value' => $this->get_buyer_reference( $order ),
        );
    }
    
    /**
     * Get the buyer reference of the order
     */
    public function get_buyer_reference( $order ) {
        return '';
    }

    /**
     * Get the ubl array formatted invoice period
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_invoice_period( $order, $ubl_format ): array {
        return array(
            'name' => 'cac:InvoicePeriod',
            'value' => $this->get_invoice_period( $order ),
        );
    }

    /**
     * Get the invoice period of the order
     */
    public function get_invoice_period( $order ) {
        return '';
    }

    /**
     * Get the ubl array formatted order reference
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_order_reference( $order, $ubl_format ): array {
        return array(
            'name' => 'cac:OrderReference',
            'value' => $this->get_formatted_order_reference_values( $order, $ubl_format ),
        );
    }

    /**
     * Get the formatted order reference values of the order
     * 
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_order_reference_values( $order, $ubl_format ): array {
        return array(
            array(
                'name' => 'cbc:ID',
                'value' => $this->get_purchase_order_reference_id( $order ),
            ),
            array(
                'name' => 'cbc:SalesOrderID',
                'value' => $this->get_sales_order_id( $order ),
            ),
        );
    }

    /**
     * Get the purchase order reference id of the order
     */
    public function get_purchase_order_reference_id( $order ) {
        return $order->get_id();
    }

    /**
     * Get the sales order id of the order
     */
    public function get_sales_order_id( $order ) {
        return $order->get_id();
    }

    /**
     * Get the ubl array formatted delivery
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_originator_document_reference( $order, $ubl_format ): array {
        return array(
            'name' => 'cac:OriginatorDocumentReference',
            'value' => $this->get_originator_document_reference( $order ),
        );
    }

    /**
     * Get the originator document reference of the order
     */
    public function get_originator_document_reference( $order ) {
        return '';
    }

    /**
     * Get the ubl array formatted contract document reference
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_contract_document_reference( $order, $ubl_format ): array {
        return array(
            'name' => 'cac:ContractDocumentReference',
            'value' => $this->get_formatted_contract_document_reference_id( $order, $ubl_format ),
        );
    }

    /**
     * Get the formatted contract document reference id of the order
     * 
     * @param object $order
     * @param string $ubl_format
     */
    public function get_formatted_contract_document_reference_id( $order, $ubl_format ) {
        return array(
            'name' => 'cbc:ID',
            'value' => $this->get_contract_document_reference_id( $order ),
        );
    }

    /**
     * Get the contract document reference id of the order
     */
    public function get_contract_document_reference_id( $order ) {
        return $order->get_id();
    }

    /**
     * Get the ubl array formatted additional document reference
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_accounting_supplier_party( $order, $ubl_format ): array {
        return array(
            'name' => 'cac:AccountingSupplierParty',
            'value' => $this->get_formatted_seller_party_details( $order, $ubl_format ),
        );
    }

    /**
     * Get the formatted seller party details of the order
     * 
     * @param object $order
     * @param string $ubl_format
     */
    public function get_formatted_seller_party_details( $order, $ubl_format ) {
        return array(
                array(
                    'name' => 'cac:party',
                    'value' => $this->get_formatted_seller_address( $order, $ubl_format ),
                )
            );
    }

    /**
     * Get the formatted seller address of the order
     * 
     * @param object $order
     * @param string $ubl_format
     */
    public function get_formatted_seller_address( $order, $ubl_format ) {
        return array(
            array(
                'name' => 'cac:PostalAddress',
                'value' => array(
                    array(
                        'name' => 'cbc:StreetName',
                        'value' => $this->get_address_line( $order ),
                    ),
                    array(
                        'name' => 'cbc:CityName',
                        'value' => $this->get_shop_city( $order ),
                    ),
                    array(
                        'name' => 'cbc:PostalZone',
                        'value' => $this->get_shop_postalcode( $order ),
                    ),
                    array(
                        'name' => 'cac:Country',
                        'value' => array(
                            array(
                                'name' => 'cbc:IdentificationCode',
                                'value' => $this->get_shop_country( $order ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'cac:PartyLegalEntity',
                'value' => array(
                    array(
                        'name' => 'cbc:RegistrationName',
                        'value' => $this->get_shop_name( $order ),
                    ),
                    array(
                        'name' => 'cbc:CompanyID',
                        'value' => null,
                    ),
                ),
            ),
            array(
                'name' => 'cac:Contact',
                'value' => array(
                    array(
                        'name' => 'cbc:ElectronicMail',
                        'value' => get_option( 'woocommerce_email_from_address' ),
                    ),
                ),
            ),
        );
    }
    
    /**
     * Get the shop name of the order
     */
    public function get_shop_name () {
        $company_name = \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_companyname' );
        if ( !empty( $company_name ) ) {
            return $company_name;   
        } else if ( !empty( get_option( 'woocommerce_store_name' ) ) ) {
            return get_option( 'woocommerce_store_name' );
        } else{
            return get_bloginfo( 'name' );
        }
    }

    /**
     * Get the address line of the order
     */
    public function get_address_line () {
        $address_line_1 = \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_sender_address_line1' );
        if ( !empty( $address_line_1 ) ) {
            return $address_line_1;   
        } else if ( !empty( get_option( 'woocommerce_store_address' ) ) ) {
            return get_option( 'woocommerce_store_address' );
        } else{
            return '';
        }
    }

    /**
     * Get the shop city of the order
     */
    public function get_shop_city () {
        $city = \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_sender_city' );
        if ( !empty( $city ) ) {
            return $city;   
        } else if ( !empty( get_option( 'woocommerce_store_city' ) ) ) {
            return get_option( 'woocommerce_store_city' );
        } else{
            return '';
        }
    }

    /**
     * Get the shop postal code of the order
     */
    public function get_shop_postalcode () {
        $city = \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_sender_postalcode' );
        if ( !empty( $city ) ) {
            return $city;   
        } else if ( !empty( get_option( 'woocommerce_store_postcode' ) ) ) {
            return get_option( 'woocommerce_store_postcode' );
        } else{
            return '';
        }
    }

    /**
     * Get the shop country of the order
     */
    public function get_shop_country () {
        $country = \Wf_Woocommerce_Packing_List::get_option( 'wf_country' );
        if ( !empty( $country ) ) {
            return $country;   
        } else if ( !empty( get_option( 'woocommerce_default_country' ) ) ) {
            return get_option( 'woocommerce_default_country' );
        } else{
            return '';
        }
    }

    /**
     * Get the ubl array formatted accounting customer party
     *
     * @param object $order
     * @param string $ubl_format
     * @return array
     */
    public function get_formatted_accounting_customer_party( $order, $ubl_format ) {
        return array(
            'name' => 'cac:AccountingCustomerParty',
            'value' => $this->get_formatted_buyer_party_details( $order, $ubl_format),
        );
    }

    /**
     * Get the formatted buyer party details of the order
     * 
     * @param object $order
     * @param string $ubl_format
     */
    public function get_formatted_buyer_party_details( $order, $ubl_format ) {
        return array(
            array(
                'name' => 'cac:Party',
                'value' => $this->get_formatted_buyer_address( $order, $ubl_format ),
            )
        );
    }

    /**
     * Get the formatted buyer address of the order
     * 
     * @param object $order
     * @param string $ubl_format
     */
    public function get_formatted_buyer_address( $order, $ubl_format ) {
        return array(
            array(
                'name' => 'cac:PostalAddress',
                'value' => array(
                    array(
                        'name' => 'cbc:StreetName',
                        'value' => $order->get_billing_address_1(),
                    ),
                    array(
                        'name' => 'cbc:CityName',
                        'value' => $order->get_billing_city(),
                    ),
                    array(
                        'name' => 'cbc:PostalZone',
                        'value' => $order->get_billing_postcode(),
                    ),
                    array(
                        'name' => 'cac:Country',
                        'value' => array(
                            array(
                                'name' => 'cbc:IdentificationCode',
                                'value' => $order->get_billing_country(),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'cac:PartyLegalEntity',
                'value' => array(
                    array(
                        'name' => 'cbc:RegistrationName',
                        'value' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    ),
                    array(
                        'name' => 'cbc:CompanyID',
                        'value' => null,
                    ),
                ),
            ),
            array(
                'name' => 'cac:Contact',
                'value' => array(
                    array(
                        'name' => 'cbc:ElectronicMail',
                        'value' => $order->get_billing_email(),
                    ),
                ),
            ),
        );
    }

    /**
     * Get the current order tax rates and its details.
     *
     * @param object $order
     * @return array
     */
    public function get_current_wc_order_tax_rates( $order ) {
        $order_tax_data = array();
        $items          = $order->get_items( array( 'fee', 'line_item', 'shipping' ) );

        foreach ( $items as $item_id => $item ) {
            $taxDataContainer = ( 'line_item' === $item['type'] ) ? 'line_tax_data' : 'taxes';
            $taxDataKey       = ( 'line_item' === $item['type'] ) ? 'subtotal'      : 'total';
            $lineTotalKey     = ( 'line_item' === $item['type'] ) ? 'line_total'    : 'total';

            $line_tax_data = $item[ $taxDataContainer ];
            foreach ( $line_tax_data[ $taxDataKey ] as $tax_id => $tax ) {
                if ( is_numeric( $tax ) ) {
                    if ( empty( $order_tax_data[ $tax_id ] ) ) {
                        $order_tax_data[ $tax_id ] = array(
                            'total_ex'  => $item[ $lineTotalKey ],
                            'total_tax' => $tax,
                            'items'     => array( $item_id ),
                        );
                    } else {
                        $order_tax_data[ $tax_id ]['total_ex']  += $item[ $lineTotalKey ];
                        $order_tax_data[ $tax_id ]['total_tax'] += $tax;
                        $order_tax_data[ $tax_id ]['items'][]    = $item_id;
                    }
                }
            }
        }

        $tax_items = $order->get_items( array( 'tax' ) );

        if ( empty( $tax_items ) ) {
            return $order_tax_data;
        }

        foreach ( $order_tax_data as $tax_data_key => $tax_data ) {
            foreach ( $tax_items as $tax_item_key => $tax_item ) {
                if ( $tax_item['rate_id'] !== $tax_data_key ) {
                    continue;
                }

                $order_tax_data[ $tax_data_key ]['total_tax'] = wc_round_tax_total( $tax_item['tax_amount'] ) + wc_round_tax_total( $tax_item['shipping_tax_amount'] );

                if ( is_callable( array( $tax_item, 'get_rate_percent' ) ) && version_compare( '3.7.0', $order->get_version(), '>=' ) ) {
                    $percentage = $tax_item->get_rate_percent();
                } else {
                    $percentage = $this->get_percentage_from_fallback( $tax_data, $tax_item['rate_id'] );
                }

                if ( ! is_numeric( $percentage ) ) {
                    $percentage = $this->get_percentage_from_fallback( $tax_data, $tax_item['rate_id'] );
                }

                $category = wc_get_order_item_meta( $tax_item_key, '_wtpdf_ubl_tax_category', true );

                if ( empty( $category ) ) {
                    $category = $this->get_category_from_fallback( $tax_data, $tax_item['rate_id'] );
                    wc_update_order_item_meta( $tax_item_key, '_wtpdf_ubl_tax_category', $category );
                }

                $scheme = wc_get_order_item_meta( $tax_item_key, '_wtpdf_ubl_tax_scheme', true );

                if ( empty( $scheme ) ) {
                    $scheme = $this->get_scheme_from_fallback( $tax_data, $tax_item['rate_id'] );
                    wc_update_order_item_meta( $tax_item_key, '_wtpdf_ubl_tax_scheme', $scheme );
                }
            }

            $order_tax_data[ $tax_data_key ]['percentage'] = $percentage;
            $order_tax_data[ $tax_data_key ]['category']   = $category;
            $order_tax_data[ $tax_data_key ]['scheme']     = $scheme;
            $order_tax_data[ $tax_data_key ]['name']       = ! empty( $tax_item['label'] ) ? $tax_item['label'] : $tax_item['name'];
        }

        return $order_tax_data;
    }

    /**
     * Get the percentatge for tax.
     *
     * @param array $tax_data
     * @param int|string $rate_id
     * @return float|int|string
     */
    public function get_percentage_from_fallback( $tax_data, $rate_id ) {
        $percentage = ( 0 !== $tax_data['total_ex'] ) ? ( $tax_data['total_tax'] / $tax_data['total_ex'] ) * 100 : 0;

        if ( class_exists( '\WC_TAX' ) && is_callable( array( '\WC_TAX', '_get_tax_rate' ) ) ) {
            $tax_rate = \WC_Tax::_get_tax_rate( $rate_id, OBJECT );

            if ( ! empty( $tax_rate ) && is_numeric( $tax_rate->tax_rate ) ) {
                $difference = $percentage - $tax_rate->tax_rate;

                // Turn negative into positive for easier comparison below
                if ( $difference < 0 ) {
                    $difference = -$difference;
                }

                // Use stored tax rate if difference is smaller than 0.3
                if ( $difference < 0.3 ) {
                    $percentage = $tax_rate->tax_rate;
                }
            }
        }

        return $percentage;
    }

    /**
     * Get the category for tax.
     *
     * @param array $tax_data
     * @param int|string $rate_id
     * @return string
     */
    public function get_category_from_fallback( $tax_data, $rate_id ) {
        $category = '';

        if ( class_exists( '\WC_TAX' ) && is_callable( array( '\WC_TAX', '_get_tax_rate' ) ) ) {
            $tax_rate = \WC_Tax::_get_tax_rate( $rate_id, OBJECT );
            
            if ( ! empty( $tax_rate ) && is_numeric( $tax_rate->tax_rate ) ) {
               
                $ubl_tax_settings = \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_ubl_invoice_taxes', $this->module_id );
                $category = isset( $ubl_tax_settings['rate'][ $tax_rate->tax_rate_id ]['category'] ) ? $ubl_tax_settings['rate'][ $tax_rate->tax_rate_id ]['category'] : '';
                $tax_rate_class = empty($tax_rate->tax_rate_class) ? 'standard' : $tax_rate->tax_rate_class;

                if ( empty( $category ) ) {
                    $category = isset( $ubl_tax_settings['class'][ $tax_rate_class ]['category'] ) ? $ubl_tax_settings['class'][ $tax_rate_class ]['category'] : '';
                }
            }
        }

        return $category;
    }

    /**
     * Get the scheme for tax.
     *
     * @param array $tax_data
     * @param int|string $rate_id
     * @return string
     */
    public function get_scheme_from_fallback( $tax_data, $rate_id ) {
        $scheme = '';
        
        if ( class_exists( '\WC_TAX' ) && is_callable( array( '\WC_TAX', '_get_tax_rate' ) ) ) {
            $tax_rate = \WC_Tax::_get_tax_rate( $rate_id, OBJECT );

            if ( ! empty( $tax_rate ) && is_numeric( $tax_rate->tax_rate ) ) {
                $ubl_tax_settings = \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_ubl_invoice_taxes', $this->module_id );
                $scheme           = isset( $ubl_tax_settings['rate'][ $tax_rate->tax_rate_id ]['scheme'] ) ? $ubl_tax_settings['rate'][ $tax_rate->tax_rate_id ]['scheme'] : '';
                $tax_rate_class   = $tax_rate->tax_rate_class;
                
                if ( empty( $tax_rate_class ) ) {
                    $tax_rate_class = 'standard';
                }

                if ( empty( $scheme ) ) {
                    $scheme = isset( $ubl_tax_settings['class'][ $tax_rate_class ]['scheme'] ) ? $ubl_tax_settings['class'][ $tax_rate_class ]['scheme'] : '';
                }
            }
        }

        return $scheme;
    }

    /**
     * To get the base64 encoded pdf attachment for UBL Invoice
     *
     * @param int|string $order
     * @param string $ubl_format
     * @return string|bool|void
     */
    public function convert_pdf_attachment_to_base64( $order_id, $ubl_format ) {
        $order_arr[] = $order_id;
        $attachments = array();
        
        /**
         * Creating reflection class for the invoice to avoid the constructor call, 
         * since the constructor has some unnessary filters which are not required for this method.
         */

        if( empty( $this->parent_reflection_class ) ) {
            $this->parent_reflection_class = self::initialize_reflection_class();
        }

        if ( !empty( $this->parent_reflection_class ) && method_exists( $this->parent_reflection_class, 'newInstanceWithoutConstructor' ) ) {
            $instance = $this->parent_reflection_class->newInstanceWithoutConstructor();
            $attachments = $instance->get_attachment_for_ubl_invoice( $order_arr );
        }

        if ( !empty( $attachments ) ) {
            $file_path = $attachments[0];
            $fp = @fopen($file_path, "r");
            
            if ( $fp ) {
				$pdf_data = @fread($fp, filesize($file_path));
				@fclose($fp);
				return base64_encode($pdf_data);
			}
        }
        return false;
    }

    /**
     * Initialize the reflection class for the invoice
     *
     * @return object
     */
    public function initialize_reflection_class() {
        return new \ReflectionClass('\Wf_Woocommerce_Packing_List_Invoice');
    }

    /**
     * To get the attachment name for UBL Invoice
     *
     * @param int|string $order
     * @return string
     */
    public function get_attachment_name_for_ubl_invoice( $order_id ) {
        
        /**
         * Creating reflection class for the invoice to avoid the constructor call, 
         * since the constructor has some unnessary filters which are not required for this method.
         */
        if (empty($this->parent_reflection_class)) {
            $this->parent_reflection_class = self::initialize_reflection_class();
        }
        
        if ( !empty( $this->parent_reflection_class ) && method_exists( $this->parent_reflection_class, 'newInstanceWithoutConstructor' ) ) {
            $instance = $this->parent_reflection_class->newInstanceWithoutConstructor();
            $order_arr[] = $order_id;
            return $instance->get_attachment_name_for_ubl_invoice($order_arr);
        }
        return '';
    }

    /**
     * Adds email attachments for the UBL invoice based on the order status and email class.
     *
     * This method checks if the current email class and order status are selected for attaching the invoice document.
     * If they are, it generates the UBL invoice and attaches it to the email.
     *
     * @param array $attachments The current list of email attachments.
     * @param object $order The WooCommerce order object.
     * @param int $order_id The ID of the WooCommerce order.
     * @param string $email_class_id The ID of the email class.
     * @return array The updated list of email attachments.
     *
     * Hooks:
     * - `wf_pklist_alter_{module_base}_attachment_mail_type`: Filters the chosen email classes for attaching the invoice.
     * - `wf_pklist_alter_{parent_module_base}_attachment_order_status`: Filters the order statuses for attaching the invoice.
     */
    public function add_email_attachments( $attachments, $order, $order_id, $email_class_id ) {

        if ( empty( $order ) ) {
            return $attachments;
        }

        $chosen_wc_email_classes = \Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_ubl_invoice_attach_email_classes', $this->module_id );
		$chosen_wc_email_classes = apply_filters( 'wf_pklist_alter_'.$this->module_base.'_attachment_mail_type', $chosen_wc_email_classes, $order_id, $email_class_id, $order );
		$chosen_wc_email_classes = array_unique( $chosen_wc_email_classes );
		$generate_invoice_for = \Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_generate_for_orderstatus', $this->parent_module_id );
		$generate_invoice_for = apply_filters( 'wf_pklist_alter_'.$this->parent_module_base.'_attachment_order_status', $generate_invoice_for, $order_id, $email_class_id, $order );

		/**
		 * Check if the current email class and current order status are selected for attaching the invoice document.
		 */
		if( !empty( $chosen_wc_email_classes ) && in_array( $email_class_id, $chosen_wc_email_classes ) && !empty( $generate_invoice_for ) && in_array( 'wc-'.$order->get_status(), $generate_invoice_for ) ) {
            // load the chosen UBL format.
            $this->get_ubl_format( $order );

            // Render the UBL Invoice as per the chosen UBL format.
            $attach_file_path = $this->generate_ubl_invoice( $order, 'attach_ublinvoice' );
            if ( !empty( $attach_file_path ) ) {
                $attachments[] = $attach_file_path;
            }
        }

        return $attachments;
    }
}
}