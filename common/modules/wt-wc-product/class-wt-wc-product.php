<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('WT_WC_Product')){
class WT_WC_Product
{
    private static $instance = null;
    public $qty;
    public $weight;
    public $price;
    public $variation_data;
    public $variation_id;
    public $item_id;
    public $name;
    public $sku;
    public $order_item_id;
    public $item;
    public $obj;
    public function __construct($item_id = null)
    {
        if(!is_null($item_id)){
            $this->obj = wc_get_product($item_id);
        }
    } 
    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new WT_WC_Product(null);
        }
        return self::$instance;
    }
}
}