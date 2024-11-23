<?php
if (!defined('ABSPATH')) {
    exit;
}
$query = new WC_Order_Query( array(
    'limit' => 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'parent'=>0,
) );

$orders = $query->get_orders();
if(count($orders)>0)
{
	$order=$orders[0];
	$order_number=$order->get_order_number();
	$tooltip_conf=Wf_Woocommerce_Packing_List_Admin::get_tooltip_configs('preview_option', $module_id);
?>	
	<div style="float:left;width: 100%;padding: 0px 10px;">
		<a href="https://www.webtoffee.com/woocommerce-pdf-invoices-packing-slips-delivery-notes-shipping-labels-userguide-free-version/#customize-demo" target="_blank" style="text-decoration: none;">
			<span class="dashicons dashicons-external"></span>
			<?php _e('View sample customization demo','print-invoices-packing-slip-labels-for-woocommerce');?>
		</a>
	</div>
	<div style="float:left; width:100%; margin-bottom:10px; padding:10px; padding-top:0px; box-sizing:border-box;height:103px !important; overflow:hidden; border:solid 1px #fff;" class="wf_sample_pdf_options wf_sample_pdf_options_div">
		<a class="wf_download_sample_pdf wf_codeview_link_btn <?php echo esc_attr($tooltip_conf['class']);?>" style="margin-top:9px;" <?php echo $tooltip_conf['text'];?>>
			<span class="dashicons dashicons-external"></span>
			<?php _e('Preview sample PDF','print-invoices-packing-slip-labels-for-woocommerce');?>
			(<?php _e('Order','print-invoices-packing-slip-labels-for-woocommerce');?>: <span class="wf_sample_pdf_order_no_preview"><?php echo wp_kses_post($order_number);?></span>)
		</a>		
		<a class="wf_codeview_link_btn" style="float:right; margin-right:-5px; font-weight:900; font-size:22px; line-height:15px; color:#333; display: none;">...</a>
		<span class="spinner" style="margin-top:11px; display:none;"></span>
		<div class="show_ord_preview_div">
			<label style="font-weight:bold; margin-top:9px; margin-bottom:5px; float:left; width:100%;"><?php _e('Order number', 'print-invoices-packing-slip-labels-for-woocommerce');?></label>
			<input type="text" style="width:99%;" name="wf_sample_pdf_order_no" value="<?php echo wp_kses_post($order_number);?>" class="wf_pklist_text_field" placeholder="<?php _e('Order number', 'print-invoices-packing-slip-labels-for-woocommerce');?>">
		</div>
	</div>
<?php
}
?>