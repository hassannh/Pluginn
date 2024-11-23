<?php
$arr = array(
	'woocommerce_wf_attach_image_packinglist' => __("Adds product image to the line items in the product table","print-invoices-packing-slip-labels-for-woocommerce"),
	'woocommerce_wf_add_customer_note_in_packinglist' => __("Adds customer note to the packing slip","print-invoices-packing-slip-labels-for-woocommerce"),
	'woocommerce_wf_packinglist_footer_pk' => __("Adds Footer to the packing slip","print-invoices-packing-slip-labels-for-woocommerce")
);
$arr = apply_filters("wt_pklist_alter_tooltip_data_".$this->module_base,$arr,$this->module_base);