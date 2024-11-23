<?php
$template_arr=array(
	array(
		'id'=>'template1',
		'title'=>__('Classic', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'preview_img'=>'template1.png',
	),
);
$template_arr = apply_filters("wt_pklist_add_pro_templates",$template_arr,$this->to_customize);