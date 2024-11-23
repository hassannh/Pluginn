<?php
$template_arr=array(
	array(
		'id'=>'template0',
		'title'=>__('Basic - 0', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'preview_img'=>'template1.png',
	),
);

$template_arr = apply_filters('wt_pklist_alter_basic_template',$template_arr,$this->to_customize);
$template_arr = apply_filters("wt_pklist_add_pro_templates",$template_arr,$this->to_customize);