<?php
if (!defined('ABSPATH')) {
    exit;
}
function wt_pklist_gen_customize_form_field_sub($arg)
{
	$field_type=isset($arg['type']) ? $arg['type'] : 'text';
	$label=isset($arg['label']) ? $arg['label'] : '';
	
	//event class decides which event to bind (keyup, keypress)
	$event_class=isset($arg['event_class']) ? $arg['event_class'] : 'wf_cst_keyup';
	
	//css property/attribute
	$css_prop=isset($arg['css_prop']) ? ' data-prop="'.$arg['css_prop'].'"' : '';
	
	//target element
	$trgt_elm=isset($arg['trgt_elm']) ? ' data-elm="'.$arg['trgt_elm'].'"' : '';

	//preview element. in case of attribute as target prop
	$preview_elm=isset($arg['preview_elm']) ? ' data-preview_elm="'.$arg['preview_elm'].'"' : '';
	
	//prevent refresh or do refresh the HTML
	$refresh_html=isset($arg['refresh_html']) ? ' data-refresh_html="'.$arg['refresh_html'].'"' : '';

	$unit=isset($arg['unit']) ? ' data-unit="'.$arg['unit'].'"' : ' data-unit=""';
	$default_data=isset($arg['default_data']) ? ' data-default="'.$arg['default_data'].'"' : '';
	if($field_type=='color' && $default_data=='')
	{
		$default_data='data-default="#ffffff"';
	}
	$width=isset($arg['width']) ? 'width:'.$arg['width'].'; ' : '';
	$float=isset($arg['float']) ? 'float:'.$arg['float'].'; ' : '';

	$elm_props=$css_prop.$trgt_elm.$preview_elm.$unit.$default_data.$refresh_html;
	$frmgrp_style_props=$width.$float;
	?>
	<div class="wf_side_panel_frmgrp" style="<?php echo esc_attr($frmgrp_style_props);?>">
		<label><?php echo esc_html($label);?></label>
	<?php
	if("text" === $field_type)
	{
		?>
		<input type="text" name="" class="wf_sidepanel_txt <?php echo esc_attr($event_class);?>" <?php echo $elm_props;?> />
		<?php
	}elseif("select" === $field_type)
	{
		$select_options=isset($arg['select_options']) ? $arg['select_options'] : array();
		$disabled_options = isset($arg['disabled_options']) ? $arg['disabled_options'] : array();
		?>
		<select class="wf_sidepanel_sele <?php echo esc_attr($event_class);?>" <?php echo $elm_props;?> >
			<?php
			foreach($select_options as $select_optionK=>$select_optionV)
			{
				if(in_array($select_optionK,$disabled_options)){
					$disabled_attr = 'disabled'; 
				}else{
					$disabled_attr = ''; 
				}
				?>
				<option value="<?php echo esc_attr($select_optionK);?>" <?php echo $disabled_attr; ?>><?php echo wp_kses_post($select_optionV);?></option>
				<?php
			}
			?>
		</select>
		<?php
	}
	elseif("text_inputgrp" === $field_type)
	{
		$addonblock_vl=isset($arg['addonblock']) ? $arg['addonblock'] : 'px';
		?>
		<div class="wf_inptgrp">
			<input type="text" name="" class="wf_sidepanel_txt <?php echo $event_class;?>" <?php echo $elm_props;?> >
			<div class="addonblock"><input type="text" name="" value="<?php echo $addonblock_vl;?>"></div>
		</div>
		<?php
	}
	elseif("textarea" === $field_type)
	{
		?>
		<textarea class="wf_sidepanel_txtarea <?php echo esc_attr($event_class);?>" <?php echo $elm_props;?> ></textarea>
		<?php
	}
	elseif("color" === $field_type)
	{
		?>
		<input type="text" name="" class="wf-color-field <?php echo esc_attr($event_class);?>" <?php echo $elm_props;?> >
		<?php
	}
	elseif("checkbox" === $field_type)
	{
		?>
		<input type="checkbox" name="" class="wf-checkbox <?php echo esc_attr($event_class);?>" <?php echo $elm_props;?> >
		<?php
	}
	?>
	</div>
	<?php
}
function wt_pklist_gen_customize_form_field($args)
{
	if(isset($args['type'])) //single field
	{
		wt_pklist_gen_customize_form_field_sub($args);
	}else
	{
		foreach($args as $arg)
		{
			wt_pklist_gen_customize_form_field_sub($arg);
		}
	}
}
function wt_pklist_get_customize_panel_html($type,$template_type)
{
	$fields=array();
	if("doc_title" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text',
				'css_prop'=>'html',
				'trgt_elm'=>'doc_title',
			),
			array(
				'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>'doc_title',
				'width'=>'49%',
			),
			array(
				'label'=>__('Text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'doc_title',
				'event_class'=>'wf_cst_change',
				'width'=>'49%',
				'float'=>'right',
			),			
			array(
				'label'=>__('Text Color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>'doc_title',
				'event_class'=>'wf_cst_click',
			)
		);
	}
	elseif("company_logo" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Display','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'event_class'=>'wf_cst_switcher',
				'select_options'=>array(
					'company_logo_img_box'=>__('Company Logo','print-invoices-packing-slip-labels-for-woocommerce'),
					'company_name'=>__('Company Name','print-invoices-packing-slip-labels-for-woocommerce'),
				),
			),
			array(
				'label'=>__('Logo Width','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'width',
				'trgt_elm'=>'company_logo_img',
				'width'=>'49%',
			),
			array(
				'label'=>__('Logo Height','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'height',
				'trgt_elm'=>'company_logo_img',
				'width'=>'49%',
				'float'=>'right',
			),
			array(
				'label'=>__('Company name font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>'company_name',
			),			
			array(
				'label'=>__('Extra Details','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'textarea',
				'css_prop'=>'html',
				'trgt_elm'=>'company_logo_extra_details',
			),
			array(
				'label'=>__('Extra detail font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>'company_logo_extra_details',
			),
			array(
				'label'=>__('Header Color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'background-color',
				'trgt_elm'=>'invoice-header',
				'event_class'=>'wf_cst_click',
			),
		);
	}elseif("invoice_number" === $type || "order_number" === $type || "proforma_invoice_number" === $type || "creditnote_number" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Text','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>$type.'_label',
			),
			array(
				'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>$type,
				'unit'=>'px',
				'width'=>'49%',
			),
			array(
				'label'=>__('Style','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('font-weight'),
				'css_prop'=>'font-weight',
				'trgt_elm'=>$type,
				'width'=>'49%',
				'float'=>'right',
				'event_class'=>'wf_cst_change',
			),
			array(
				'label'=>__('Text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>$type,
				'event_class'=>'wf_cst_click',
			),
		);
	}elseif("invoice_date" === $type || "order_date" === $type || "dispatch_date" === $type || "proforma_invoice_date" === $type || "creditnote_date" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Text','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>$type.'_label',
			),
			array(
				'label'=>__('Format','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'attr-data-'.$type.'-format',
				'event_class'=>'wf_'.$type.'_txt wf_cst_change wf_cst_keyup',
				'trgt_elm'=>$type,
				'unit'=>'',
				'width'=>'49%',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('date_format'),
				'css_prop'=>'attr-data-'.$type.'-format',
				'trgt_elm'=>$type,
				'event_class'=>'wf_'.$type.'_sele wf_cst_change',
				'width'=>'49%',
				'float'=>'right',
			),
			array(
				'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>$type,
				'unit'=>'px',
				'width'=>'49%',
			),
			array(
				'label'=>__('Style','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('font-weight'),
				'css_prop'=>'font-weight',
				'trgt_elm'=>$type,
				'width'=>'49%',
				'float'=>'right',
				'event_class'=>'wf_cst_change',
			),
			array(
				'label'=>__('Text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>$type,
				'event_class'=>'wf_cst_click',
			),
		);
	}elseif("from_address" === $type || "billing_address" === $type || "shipping_address" === $type || "return_address" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>$type.'_label',
			),
			array(
				'label'=>__('Text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>$type,
				'event_class'=>'wf_cst_change',
			),
			array(
				'label'=>__('Text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>$type,
				'event_class'=>'wf_cst_click',
			),
		);
	}elseif("email" === $type || "tel" === $type || "customer_note" === $type || "vat_number" === $type || "ssn_number" === $type || "shipping_method" === $type || "tracking_number" === $type || "total_no_of_items" === $type || "box_name" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>$type.'_label',
			),
			array(
				'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>$type,
				'unit'=>'px',
			),
			array(
				'label'=>__('Text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>$type,
				'event_class'=>'wf_cst_click',
			),
		);
	}elseif("product_table" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Table head background','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'background-color|border-color|border-color',
				'trgt_elm'=>'product_table_head_bg|product_table_head th|product_table_head_bg td',
				'event_class'=>'wf_cst_click',
			),
			array(
				'label'=>__('Table head text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>'table_head_color',
				'event_class'=>'wf_cst_click',
			),
			array(
				'label'=>__('Table body text color','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'color',
				'css_prop'=>'color',
				'trgt_elm'=>'table_body_color',
				'event_class'=>'wf_cst_click',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_serial_no',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Serial No label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_serial_no',
				'width'=>'44%',
			),
			array(
				'label'=>__('Serial no text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_serial_no',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_image',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Image label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_image',
				'width'=>'44%',
			),
			array(
				'label'=>__('Image text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_image',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_sku',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('SKU label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_sku',
				'width'=>'44%',
			),
			array(
				'label'=>__('SKU text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_sku',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_product',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Product label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_product',
				'width'=>'44%',
			),
			array(
				'label'=>__('Product text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_product',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_quantity',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Qty label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_quantity',
				'width'=>'44%',
			),
			array(
				'label'=>__('Qty text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_quantity',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_price',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Price label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_price',
				'width'=>'44%',
			),
			array(
				'label'=>__('Price text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_price',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_tax',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Tax label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_tax',
				'width'=>'44%',
			),
			array(
				'label'=>__('Tax text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_tax',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_total_weight',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Total weight label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_total_weight',
				'width'=>'44%',
			),
			array(
				'label'=>__('Total weight text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_total_weight',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
			array(
				'label'=>'&nbsp;',
				'type'=>'checkbox',
				'trgt_elm'=>'product_table_head_total_price',
				'event_class'=>'wf_cst_toggler',
				'width'=>'10%',
			),
			array(
				'label'=>__('Total label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_head_total_price',
				'width'=>'44%',
			),
			array(
				'label'=>__('Total text align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'product_table_head_total_price',
				'event_class'=>'wf_cst_change',
				'width'=>'44%',
				'float'=>'right',
			),
		);
	}elseif("signature" === $type)
	{
		$fields=array(
			array(
				'label'=>__('Signature type','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'event_class'=>'wf_cst_switcher',
				'select_options'=>array(
					'manual_signature'=>__('Manual Signature','print-invoices-packing-slip-labels-for-woocommerce'),
					'image_signature_box'=>__('Image Signature','print-invoices-packing-slip-labels-for-woocommerce'),
				),
			),
			array(
				'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'signature_label',
			),
			array(
				'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'text_inputgrp',
				'css_prop'=>'font-size',
				'trgt_elm'=>'signature_label',
				'unit'=>'px',
				'width'=>'49%',
			),
			array(
				'label'=>__('Align','print-invoices-packing-slip-labels-for-woocommerce'),
				'type'=>'select',
				'select_options'=>Wf_Woocommerce_Packing_List_Customizer::get_customizer_presets('text-align'),
				'css_prop'=>'text-align',
				'trgt_elm'=>'signature',
				'event_class'=>'wf_cst_change',
				'width'=>'49%',
				'float'=>'right',
			)
		);
	}
	elseif("product_table_subtotal" === $type || "product_table_shipping" === $type || "product_table_cart_discount" === $type
 || "product_table_order_discount" === $type || "product_table_total_tax" === $type || "product_table_fee" === $type
  || "product_table_payment_method" === $type || "product_table_payment_total" === $type || "product_table_coupon" === $type || "payment_link" === $type)
	{
		$fields=array(			
			array(
				'label'=>__('Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>$type.'_label',
			),
		);
	}
	elseif("product_table_payment_summary" === $type)
	{

		$fields=array(			
			array(
				'label'=>__('Subtotal','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_subtotal_label',
				'width'=>'49%',
			),
			array(
				'label'=>__('Shipping','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_shipping_label',
				'width'=>'49%',
				'float'=>'right',
			),
			array(
				'label'=>__('Cart Discount','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_cart_discount_label',
				'width'=>'49%',
			),
			array(
				'label'=>__('Order Discount','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_order_discount_label',
				'width'=>'49%',
				'float'=>'right',
			),
			array(
				'label'=>__('Total Tax','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_total_tax_label',
				'width'=>'49%',
			),
			array(
				'label'=>__('Fee','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_fee_label',
				'width'=>'49%',
				'float'=>'right',
			),
			array(
				'label'=>__('Payment Method','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_payment_method_label',
				'width'=>'49%',
			),
			array(
				'label'=>__('Total','print-invoices-packing-slip-labels-for-woocommerce'),
				'css_prop'=>'html',
				'trgt_elm'=>'product_table_payment_total_label',
				'width'=>'49%',
				'float'=>'right',
			),
		);
	}

	//an informational text
	$info_text			= '';
	$template_addon_key = Wf_Woocommerce_Packing_List_Pro_Addons::wt_get_addon_key_by_template_type($template_type);
	if( !empty( $template_addon_key ) ) {
		if( false === Wf_Woocommerce_Packing_List_Admin::wt_plugin_active($template_addon_key) ){
			
			if( "wt_ipc_addon" === $template_addon_key ){
				$pro_link = "https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips";
			} else {
				$pro_link = "https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes";
			}

			if( "vat_number" === $type ){
				$vat_text	= sprintf('<div><span>%1$s</span>:<br>%2$s <b>%3$s</b>. %4$s <a href="%6$s" target="_blank" style="cursor:pointer;">%5$s</a></div>',
					__("Note","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Displays customer`s VAT number saved in the order details by third-party plugins in this document. Supported meta keys are","print-invoices-packing-slip-labels-for-woocommerce"),
					"vat, vat_number, eu_vat_number",
					__("To add a different meta key","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Upgrade to premium","print-invoices-packing-slip-labels-for-woocommerce"),
					esc_url($pro_link)
				);
				$info_text	= $vat_text;
			}
		}
	}
	$info_text=apply_filters('wf_pklist_alter_customize_info_text',$info_text,$type,$template_type);
	echo '<div class="wf_side_panel_info_text">'.wp_kses_post($info_text).'</div>';
	$fields=apply_filters('wf_pklist_alter_customize_inputs',$fields,$type,$template_type);
	if(count($fields)>0)
	{
		wt_pklist_gen_customize_form_field($fields);
	}
}
foreach($customizable_items as $key=>$label)
{
	$expndble=in_array($key,$non_options_fields) ? false : true;
	$toggle=in_array($key,$non_disable_fields) ? false : true;
	$non_customizable=in_array($key,$non_customizable_items) ? true : false;
	Wf_Woocommerce_Packing_List_Customizer::envelope_customize_hdblock($key,$label,$expndble,$toggle,$non_customizable);
	wt_pklist_get_customize_panel_html($key,$template_type);
	Wf_Woocommerce_Packing_List_Customizer::envelope_customize_ftblock($expndble);
}
?>
<?php
/**
 * Customizer add-on CTA Banner, when pro add-on is active and adc is not active
 * 
 * @since 4.1.1
 */
if(!empty($template_type) && "addresslabel" !== $template_type){
	$template_addon_key = Wf_Woocommerce_Packing_List_Pro_Addons::wt_get_addon_key_by_template_type($template_type);
	if(!empty($template_addon_key)){
		if(true === Wf_Woocommerce_Packing_List_Admin::wt_plugin_active($template_addon_key) && false === Wf_Woocommerce_Packing_List_Admin::wt_plugin_active('wt_adc_addon')){
			$adc_banner_content = Wf_Woocommerce_Packing_List_Pro_Addons::wt_get_addon_cta_banner_content('accounting','wt_adc_addon');
			?>
<div class="wt_pro_addon_tile_pro_ad" style="width:99%;min-height: 316px;margin:0;">
	<div class="wt_pro_addon_widget_pro_ad">
		<div class="wt_pro_addon_widget_wrapper_pro_ad">
			<div class="wt_pro_addon_widget_column_pro_ad_1">
				<img src="<?php echo esc_url($adc_banner_content['logo']); ?>">
			</div>
			<div class="wt_pro_addon_widget_column_pro_ad_2">
				<h4 class="wt_pro_addon_title"><?php echo esc_html($adc_banner_content['title']); ?></h4>
			</div>
		</div>
		<div class="wt_pro_addon_features_list_pro_ad">
			<ul>
			<?php
				foreach($adc_banner_content['features_list'] as $p_feature){
					?>
					<li><?php echo esc_html($p_feature); ?></li>
					<?php
				}
			?>
			</ul>
		</div>
		<div class="wt_pro_show_more_less_pro_ad">
			<a class="wt_pro_addon_show_more_pro_ad"><p><? echo __("Show More","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
			<a class="wt_pro_addon_show_less_pro_ad"><p><? echo __("Show Less","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
		</div>
	</div>
	<a class="wt_pro_addon_premium_link_div_pro_ad" href="https://www.webtoffee.com/product/customizer-for-woocommerce-pdf-invoice/?utm_source=free_plugin_customizer_bottom&utm_medium=pdf_premium&utm_campaign=PDF_Customizer&utm_content=<?php esc_attr_e(WF_PKLIST_VERSION); ?>" target="_blank">
		<?php _e("Checkout Premium","print-invoices-packing-slip-labels-for-woocommerce"); ?>
	</a>
</div>
			<?php
		}
	}
}
?>