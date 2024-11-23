<?php
/**
 * Pro addons details
 *  
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
if(!class_exists('Wf_Woocommerce_Packing_List_Pro_Addons')){
class Wf_Woocommerce_Packing_List_Pro_Addons
{
    public static $addon_keys_with_file_path = array();
    public static $template_type_with_addon_key = array();
    public function __construct()
	{
        self::$addon_keys_with_file_path = array(
			'wt_pklist_adc'	=> 'wt-advanced-customizer-addon/wt-advanced-customizer-addon.php',
			'wt_ipc_addon'	=> 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php',
			'wt_sdd_addon'	=> 'wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php',
			'wt_pl_addon'	=> 'wt-woocommerce-picklist-addon/wt-woocommerce-picklist-addon.php',
			'wt_al_addon'	=> 'wt-woocommerce-addresslabel-addon/wt-woocommerce-addresslabel-addon.php',
			'wt_pi_addon'	=> 'wt-woocommerce-proforma-addon/wt-woocommerce-proforma-addon.php',
			'wt_qr_addon'	=> 'qrcode-addon-for-woocommerce-pdf-invoices/qrcode-addon-for-woocommerce-pdf-invoices.php',
			'wt_remote_printnode'	=> 'wt-woocommerce-packing-list-printnode/wt-woocommerce-packing-list-printnode.php',
			'wt_mpdf_addon'	=> 'mpdf-addon-for-pdf-invoices/wt-woocommerce-packing-list-mpdf.php',
			'wt_adc_addon'	=> 'wt-advanced-customizer-addon/wt-advanced-customizer-addon.php',
		);

        self::$template_type_with_addon_key = array(
            'invoice' => 'wt_ipc_addon',
			'packinglist' => 'wt_ipc_addon',
			'creditnote'  => 'wt_ipc_addon',
			'shippinglabel' => 'wt_sdd_addon',
			'dispatchlabel' => 'wt_sdd_addon',
			'deliverynote' => 'wt_sdd_addon',
			'proformainvoice' => 'wt_pi_addon',
			'picklist' => 'wt_pl_addon',
			'addresslabel' => 'wt_al_addon',
        );
        $this->init();
    }

    public function init(){
        self::$addon_keys_with_file_path = apply_filters('wt_pklist_alter_addon_key_with_file_path',self::$addon_keys_with_file_path);
        self::$template_type_with_addon_key = apply_filters('wt_pklist_alter_template_type_with_addon_key',self::$template_type_with_addon_key);
    }

    public static function get_file_path_by_addon_key($addon_key=""){
        if("" === $addon_key){
            return "";
        }
        if(isset(self::$addon_keys_with_file_path[$addon_key])){
            return self::$addon_keys_with_file_path[$addon_key];
        }
        return "";
    }

    public static function wt_get_addon_key_by_template_type($template_type){
		if("" === $template_type){
			return false;
		}
		$pro_ad_arr = self::$template_type_with_addon_key;
		return isset($pro_ad_arr[$template_type]) ? $pro_ad_arr[$template_type] : false;
	}

	public static function wt_get_addon_cta_banner_content($domain_key='',$addon_key=''){
		require WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
		if(!empty($domain_key) && !empty($addon_key) && !empty($premium_ext_lists) && is_array($premium_ext_lists)){
			if(isset($premium_ext_lists[$domain_key]['plugins'][$addon_key])){
				return $premium_ext_lists[$domain_key]['plugins'][$addon_key];
			}
		}
		return array();
	}

	public static function wt_pklist_get_cta_banners($banner_class=''){
		$banner_options = get_option('wt_pklist_dismissible_banners');
		/**
		 * banner array format
		 * array(
		 * 		'banner_class' => array(
		 * 			'class'		=> banner_class,
		 * 			'status' 	=> 0,
		 *.         'last_action' => 1267677434,
		 *			'interval'	=> 15 
		 * 		)
		 * );
		 * 
		 * banner_class: the class name of the banners
		 * status: 0 - do not show, 1 - currently showing, 2 - remaind me later 
		 * last_action: timestamp of last action made
		 * interval: number of days
		 *x
		 */
		if(!empty($banner_options)){
			if(!empty($banner_class) && is_array($banner_options) && isset($banner_options[$banner_class])){
				return array('type' => 'class', 'value' => $banner_options[$banner_class]); 
			}else{
				return array('type' => 'full', 'value' => $banner_options);
			}
		}else{
			return array();
		}
	}
}
new Wf_Woocommerce_Packing_List_Pro_Addons();
}