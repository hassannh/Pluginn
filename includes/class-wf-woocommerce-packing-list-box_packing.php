<?php
// to check whether accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Box_packing_Basic
{
    public $wf_package_type;
    public $template_type;
    public $boxes;
    public $dimension_unit;
    public $weight_unit;
    public function __construct()
    {
        $this->wf_package_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_package_type');
        $this->boxes=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_boxes');
        $this->dimension_unit=get_option('woocommerce_dimension_unit');
        $this->weight_unit = get_option('woocommerce_weight_unit');
    }

    // Function to create packaging list and shipping lables package
    public function wf_pklist_create_order_single_package($order) {

        $order_items = $order->get_items();
        $item_meta = array();
        $packinglist_package = array();
        foreach ($order_items as $id => $item) 
        {     
            $product = $item->get_product();      
            if($product) 
            {
                $extra_meta_details = $this->wf_pklist_get_extra_meta_details($item_meta, $order, $product, $id, $item );
                $sku = $variation_details = '';

                if (WC()->version < '2.7.0') {
                    $product_id = $product->id;
                    $product_variation_data = $product->variation_data;
                    $product_product_type = $product->product_type;
                    $product_variation_id = $product_product_type === 'variation' ? $product->variation_id : '';
                } else {
                    $product_id = $product->get_id();
                    $product_variation_data = $product->is_type('variation') ? wc_get_product_variation_attributes($product->get_id()) : '';
                    $product_product_type = $product->get_type();
                    $product_variation_id = $product->is_type('variation') ? $product->get_id() : '';
                }
                $sku = $product->get_sku();
                $item_meta = (WC()->version < '3.1.0') ? new WC_Order_Item_Meta($item) : new WC_Order_Item_Product($item);
                $variation_details ='';
                if(Wf_Woocommerce_Packing_List_Admin::module_exists('customizer'))
                {
                    $variation_details = Wf_Woocommerce_Packing_List_Customizer::get_order_line_item_variation_data($item, $id, $product, $order, $this->template_type);
                }
                $variation_id = $product_product_type == 'variation' ? $product_variation_id : '';
                $packinglist_package[0][] = array(
                    'sku' => $product->get_sku(),
                    'name' => $product->get_name(),
                    'type' => $product_product_type,
                    'extra_meta_details' => $extra_meta_details,
                    'weight' => $product->get_weight(),
                    'id' => $product_id,
                    'variation_id' => $variation_id,
                    'price' => $product->get_price(),
                    'variation_data' => $variation_details,
                    'quantity' => $item['qty'],
                    'order_item_id' =>$id,
                    'dimension_unit'=>$this->dimension_unit,
                    'weight_unit'=>$this->weight_unit,                   
                );
            }else{
                $packinglist_package[0][] = array(
                    'sku' => '',
                    'name' => $item['name'],
                    'type' => '',
                    'extra_meta_details' => '',
                    'weight' => '',
                    'id' => '',
                    'variation_id' => '',
                    'price' => (float)$item['line_total']/(int)$item['qty'],
                    'variation_data' => '',
                    'quantity' => $item['qty'],
                    'order_item_id' =>$id,
                    'dimension_unit'=>'',
                    'weight_unit'=>'',                   
                );
            }
        }
        return $packinglist_package;
    }

    public function wf_pklist_get_extra_meta_details($item_meta, $order, $product, $id, $item)
    {
        $extra_meta_details='';
        if($product)
        {
            $product_id = (WC()->version < '2.7.0') ? $product->id : $product->get_id();
            $_product = wc_get_product($product_id);                        
            $item_meta = array();
            if($_product) 
            {
                if (((WC()->version < '2.7.0') ? $product->id : $product->get_id()) == ((WC()->version < '2.7.0') ? $_product->id : $_product->get_id())) 
                {
                    $item_meta = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($id, '', false) : $order->get_item_meta($id);
                }
            }
            $extra_meta_details = apply_filters('wf_print_invoice_variation_add', $item_meta);
        }
        return $extra_meta_details;
    }
}