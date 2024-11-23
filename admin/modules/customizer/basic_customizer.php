<?php
/**
 * Template Baisc customizer
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_Basic_Customizer
{
    public function __construct()
    {   
        $this->init();
    }

    public function init(){
        $default_customizer = plugin_dir_path( __FILE__ ).'customizer.php';
        require_once $default_customizer;
    }

}
new Wf_Woocommerce_Packing_List_Basic_Customizer();