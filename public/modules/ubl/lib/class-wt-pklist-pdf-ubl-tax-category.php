<?php
namespace Wtpdf\Ubl\Tax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\Ubl\\Tax\\Category' )) {

Class Category {
    public $tax_categories = array();

    public function __construct() {
        $this->tax_categories = $this->get_tax_categories();
    }

    public function get_tax_categories() {
        return array(
            's'  => __( 'Standard rate', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'aa' => __( 'Lower rate', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'z'  => __( 'Zero rated goods', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'a'  => __( 'Mixed tax rate', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ab' => __( 'Exempt for resale', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ac' => __( 'Value Added Tax (VAT) not now due for payment', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'ad' => __( 'Value Added Tax (VAT) due from a previous invoice', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'b'  => __( 'Transferred (VAT)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'c'  => __( 'Duty paid by supplier', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'e'  => __( 'Exempt from tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'g'  => __( 'Free export item, tax not charged', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'h'  => __( 'Higher rate', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            'o'  => __( 'Services outside scope of tax', 'print-invoices-packing-slip-labels-for-woocommerce' ),
        );
    }
}

}

