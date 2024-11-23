<?php
$arr=array(
	"woocommerce_wf_enable_invoice" => __("Admins and customers both can leverage this feature wherever applicable. Disabling will remove all the provisions. Yet, the invoices are customizable.","print-invoices-packing-slip-labels-for-woocommerce"),
	"woocommerce_wf_orderdate_as_invoicedate" => __("The date is displayed on the invoice. It can be either the order creation date or the invoiced date.","print-invoices-packing-slip-labels-for-woocommerce"),
	"wf_woocommerce_invoice_show_print_button" => __("Allows customers to print the invoice from the specified pages.","print-invoices-packing-slip-labels-for-woocommerce"),	
);
$arr = apply_filters("wt_pklist_alter_tooltip_data_".$this->module_base,$arr,$this->module_base);