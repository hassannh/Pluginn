<?php
$arr = array(
	'woocommerce_wf_add_customer_note_in_dispatchlabel' => __("Adds customer note in the dispatch label","print-invoices-packing-slip-labels-for-woocommerce"),
	'woocommerce_wf_packinglist_footer_dl' => __("Adds footer in dispatch label","print-invoices-packing-slip-labels-for-woocommerce")
);
$arr = apply_filters("wt_pklist_alter_tooltip_data_".$this->module_base,$arr,$this->module_base);