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
$order_number = "123";
if(count($orders)>0)
{
	$order=$orders[0];
	$order_number=$order->get_order_number();
}

$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$base_id);
$current_invoice_number=($current_invoice_number<0 ? 0 : $current_invoice_number);
$inv_num=++$current_invoice_number;
$date_frmt_tooltip=__('Click to append with existing data','print-invoices-packing-slip-labels-for-woocommerce');
$use_wc_order_number = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$base_id);
?>
<input type="hidden" value="<?php echo esc_attr($order_number); ?>" id="sample_invoice_number">
<input type="hidden" id="sample_current_invoice_number" value="<?php echo esc_attr($current_invoice_number); ?>">
<div id="invoice_number_prev_div" style="width: auto;border: 1px solid #dadadc;padding: 5px 12px;border-radius: 5px;display: inline-block;background: #f0f0f1;position: absolute;<?php echo is_rtl() ? "left: 1%;" : "right: 1%;"?>top: 0;">
    <p style="font-weight: bold;line-height: 0;">
        <?php echo __('PREVIEW','print-invoices-packing-slip-labels-for-woocommerce'); ?>
    </p>
    <p style="margin: 1em  0 0.5em 0; <?php if("No" === $use_wc_order_number){ echo "display: none;"; }?>" id="preview_invoice_number_text">
        <?php echo __('If the order number is','print-invoices-packing-slip-labels-for-woocommerce'); ?> <?php echo $order_number; ?>, 
        <br> 
        <?php echo sprintf(__("the %s number would be",'print-invoices-packing-slip-labels-for-woocommerce'),$template_type); ?> 
    </p>
    <p style="margin: 1em  0 0.5em 0; <?php if("Yes" === $use_wc_order_number){ echo "display: none;"; }?>" id="preview_invoice_number_text_custom">
        <?php echo sprintf(__('Your next %s number would be','print-invoices-packing-slip-labels-for-woocommerce'),$template_type); ?>
    </p>
    <span id="preview_invoice_number" style="background: #ffffff;padding: 5px;color: #3c434a;border-radius: 3px;float: left;font-weight: bold;margin-bottom: 0.5em;"></span>    
</div>