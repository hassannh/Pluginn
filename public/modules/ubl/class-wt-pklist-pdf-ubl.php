<?php
namespace Wtpdf\Ubl;

use Wf_Woocommerce_Packing_List;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\UBL\\WtPdfUblGenerator' ) ) {

class WtPdfUblGenerator {
    CONST WT_UBL_INVOICE_NAMESPACE = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
    CONST WT_UBL_CREDITNOTE_NAMESPACE = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2';
    CONST WT_UBL_CAC_NAMESPACE = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
    CONST WT_UBL_CBC_NAMESPACE = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';

    public $module_id = 'ubl';
    public $module_name = 'ubl';
    public static $instance = null;
    public $documents = array(
        'invoice'
    );
    public $tax_schemas = array();
    public $tax_category = array();
    public $document = null;

    public function __construct() {
        $this->load_dependency_files();
        $this->load_documents();
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load documents
     *
     * @return void
     */
    public function load_documents() {
        foreach ( $this->documents as $document ) {
            $document_class = 'Wtpdf\\Ubl\\Documents\\' . ucfirst( $document );
            $document_file = plugin_dir_path(__FILE__) . "{$document}/class-wt-pklist-pdf-ubl-{$document}.php";

            // load document file
            if ( file_exists( $document_file ) ) {
                require_once $document_file;
                if ( class_exists( $document_class ) ) {
                    $this->document = $document_class::instance();
                }
            }
        }
    }

    /**
     * Load dependency files from library
     *
     * @return void
     */
    public function load_dependency_files() {
        $files_array = array(
            'tax_schema' => 'class-wt-pklist-pdf-ubl-tax-schema.php',
            'tax_category' => 'class-wt-pklist-pdf-ubl-tax-category.php',
            'ubl_xml_converter' => 'class-ubl-xml-converter.php',
            'array_to_xml' => 'class-array-to-xml.php',
            'xml_file_handler' => 'class-ubl-xml-file-handler.php',
        );

        foreach ( $files_array as $file ) {
            $file_path = plugin_dir_path(__FILE__) . "lib/{$file}";
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            }
        }
    }

    public function sanitize_nested_array_data( $data ) {
        // Check if it's an array
        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                // Recursively sanitize if value is an array
                if ( is_array( $value ) ) {
                    $data[ $key ] = $this->sanitize_nested_array_data( $value );
                } else {
                    // Sanitize based on type of value (text, email, url, etc.)
                    if ( is_string( $value ) || is_numeric( $value ) ) {
                        $data[ $key ] = sanitize_text_field( $value ); // You can use other sanitizers depending on the type
                    } elseif ( is_email( $value ) ) {
                        $data[ $key ] = sanitize_email( $value );
                    }
                    // Add more sanitization cases if needed
                }
            }
        }

        return $data;
    }
}
new \Wtpdf\Ubl\WtPdfUblGenerator();

}