<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('Wf_Woocommerce_Packing_List_Order_Func'))
{
class Wf_Woocommerce_Packing_List_Order_Func{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public static function wt_get_discount_amount($discount_type = 'total', $incl_tax=false, $order = null, $template_type=""){
        if(is_null($order) || empty($order)){
            return 0;
        }
        $wc_version     = WC()->version;
        $order_id       = $wc_version<'2.7.0' ? $order->id : $order->get_id();
        $user_currency  = Wt_Pklist_Common::get_order_meta($order_id, 'currency', true);
        $incl_tax_text  = '';
        if($incl_tax)
        {
            $incl_tax_text  = Wf_Woocommerce_Packing_List_CustomizerLib::get_tax_incl_text($template_type, $order, 'product_price');
            $incl_tax_text  = ("" !== $incl_tax_text ? ' ('.$incl_tax_text.')' : $incl_tax_text);
            
        }   
        if(true === $incl_tax){
            if ( version_compare( $wc_version, '2.3' ) >= 0 ) {
                $discount_value = $order->get_total_discount( false ); // $ex_tax = false
            } else {
                // WC2.2 and older: recalculate to include the taxes
                $discount_value = 0;
                $items = $order->get_items();;
                if( sizeof( $items ) > 0 ) {
                    foreach( $items as $item ) {
                        $discount_value += ($item['line_subtotal'] + $item['line_subtotal_tax']) - ($item['line_total'] + $item['line_tax']);
                    }
                }
            }
            if(abs($discount_value) > 0){
                return '-'.Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$discount_value).$incl_tax_text;
            }else{
                return 0;
            }
        }else{
            $discount_value = 0;
            if ( version_compare( $wc_version, '2.3' ) >= 0 ) {
                $discount_value_on_price = $order->get_total_discount( true ); // $ex_tax = true
                $discount_value_on_tax   = $order->get_total_discount( false ) - $discount_value_on_price;
                if(abs($discount_value_on_price) <= 0){
                    return 0;
                }
                $discount_value = '-'.Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$discount_value_on_price);
                $discount_value = apply_filters('wt_pklist_alter_discount_amount_formated',$discount_value,$discount_value_on_price,$discount_value_on_tax,$incl_tax,$order,$template_type);
                return $discount_value;
            } else {
                // WC2.2 and older: recalculate to exclude tax
                $discount_value = 0;
                $items = $order->get_items();;
                if( sizeof( $items ) > 0 ) {
                    $discount_value_on_price = 0;
                    $discount_value_on_tax = 0;
                    $discount_value_incl_tax = 0;
                    foreach( $items as $item ) {
                        $discount_value_on_price += $item['line_subtotal'] - $item['line_total'];
                        $discount_value_on_tax += $item['line_subtotal_tax'] - $item['line_tax'];
                        $discount_value_incl_tax += ($item['line_subtotal'] + $item['line_subtotal_tax']) - ($item['line_total'] + $item['line_tax']);
                    }
                    if($discount_value_on_price <= 0){
                        return 0;
                    }
                    $discount_value = '-'.Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$discount_value_on_price);
                    $discount_value = apply_filters('wt_pklist_alter_discount_amount_formated',$discount_value,$discount_value_on_price,$discount_value_on_tax,$incl_tax,$order,$template_type);
                }
                return $discount_value;
            }
        }
    }
}
// end of class
}