<?php
namespace Wtpdf\Ubl\Tax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\Ubl\\Tax\\Schema' )) {

Class Schema {
    public $tax_schemas = array();

    public function __construct() {
        $this->tax_schemas = $this->get_tax_schemas();
    }

    public function get_tax_schemas() {
        return array(
            'vat' => __( 'Value added tax (VAT)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'gst' => __( 'Goods and services tax (GST)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aaa' => __( 'Petroleum tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aab' => __( 'Provisional countervailing duty cash', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aac' => __( 'Provisional countervailing duty bond', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aad' => __( 'Tobacco tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aae' => __( 'Energy fee', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aaf' => __( 'Coffee tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aag' => __( 'Harmonised sales tax, Canadian', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aah' => __( 'Quebec sales tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aai' => __( 'Canadian provincial sales tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aaj' => __( 'Tax on replacement part', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aak' => __( 'Mineral oil tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aal' => __( 'Special tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'add' => __( 'Anti-dumping duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'bol' => __( 'Stamp duty (Imposta di Bollo)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'cap' => __( 'Agricultural levy', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'car' => __( 'Car tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'coc' => __( 'Paper consortium tax (Italy)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'cst' => __( 'Commodity specific tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'cud' => __( 'Customs duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'cvd' => __( 'Countervailing duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'env' => __( 'Environmental tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'exc' => __( 'Excise duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'exp' => __( 'Agricultural export rebate', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'fet' => __( 'Federal excise tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'fre' => __( 'Free', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'gnc' => __( 'General construction tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ill' => __( 'Illuminants tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'imp' => __( 'Import tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ind' => __( 'Individual tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'lac' => __( 'Business license fee', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'lcn' => __( 'Local construction tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ldp' => __( 'Light dues payable', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'loc' => __( 'Local sales tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'lst' => __( 'Lust tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'mca' => __( 'Monetary compensatory amount', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'mcd' => __( 'Miscellaneous cash deposit', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'oth' => __( 'Other taxes', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'pdb' => __( 'Provisional duty bond', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'pdc' => __( 'Provisional duty cash', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'prf' => __( 'Preference duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'scn' => __( 'Special construction tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'sss' => __( 'Shifted social securities', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'stt' => __( 'State/provincial sales tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'sup' => __( 'Suspended duty', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'sur' => __( 'Surtax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'swt' => __( 'Shifted wage tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'tac' => __( 'Alcohol mark tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'tot' => __( 'Total', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'tox' => __( 'Turnover tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'tta' => __( 'Tonnage taxes', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'vad' => __( 'Valuation deposit', 'print-invoices-packing-slip-labels-for-woocommerce' ),
        );
    }
}

}

