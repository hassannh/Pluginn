<?php
/**
 * Update the settings
 *  
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
if(!class_exists('Wf_Woocommerce_Packing_List_Update_Install')){
class Wf_Woocommerce_Packing_List_Update_Install
{

    private static $instance = null;
    public function __construct()
    {
        add_action('admin_init',array($this,'do_update_or_install'));
        add_action('wt_pklist_save_default_templates',array($this,'wt_pklist_save_default_templates_func'));
    }

    public static function instance()
    {
		if ( is_null( self::$instance ) )
        {
			self::$instance = new self();
		}
		return self::$instance;
	}

    public function do_update_or_install() {

        $wt_pklist_save_default_templates = get_option('wt_pklist_save_default_templates');
        $new_install = get_option( 'wt_pklist_new_install' );
        $new_install = ( 1 === absint( $new_install )) ? 1 : 0;
        
        if( false === $wt_pklist_save_default_templates || empty( $wt_pklist_save_default_templates ) ) {

            $group = "wt_pklist_save_default_templates_group";
            if(false === as_next_scheduled_action( 'wt_pklist_save_default_templates' ) ){
                as_schedule_single_action( time(), 'wt_pklist_save_default_templates', array($new_install), $group );
            }
            
        }
    }

    public function wt_pklist_save_default_templates_func($new_install){
        $new_install = is_array($new_install) ? $new_install[0] : $new_install;
        $template_path = plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME).'public/modules/';
        $wt_pklist_common_modules   = get_option('wt_pklist_common_modules');

        if(!empty($wt_pklist_common_modules)){
            $customizer_obj     = Wf_Woocommerce_Packing_List::load_modules('customizer'); 
            foreach($wt_pklist_common_modules as $base => $base_val){
                if(1 === absint($base_val)){
                    $path = '';
                    if('invoice' === $base){
                        if(isset($new_install) && 1 === absint($new_install)){
                            $path = $template_path.$base.'/data/data.templates.php';
                        }else{
                            $path = $template_path.$base.'/data/data.templates-prev-version.php';
                        }
                    }
                    $customizer_obj->save_default_template($base,$path);
                }
            }
            update_option('wt_pklist_save_default_templates',1);
        }
	}

    
    public  function do_update_things() {
        $wt_pklist_ver = get_option('wfpklist_basic_version');

        // new install
        if ( false === $wt_pklist_ver || empty($wt_pklist_ver) ) {
            self::install_tables();
            update_option('wfpklist_basic_version',WF_PKLIST_VERSION);
            update_option( 'wt_pklist_new_install' , 1);
        } else {
            // update
            self::install_tables();
            self::use_migrate_values();
            do_action('wt_pklist_update_settings_module_wise_on_update');
            update_option( 'wt_pklist_new_install' , 0);
            update_option('wfpklist_basic_version_prev',$wt_pklist_ver);
            update_option('wfpklist_basic_version',WF_PKLIST_VERSION);
        }
    }

    public static function install_tables()
	{
		global $wpdb;
		//install necessary tables
		//creating table for saving template data================
        $search_query = "SHOW TABLES LIKE %s";
        $charset_collate = $wpdb->get_charset_collate();
        //$tb=Wf_Woocommerce_Packing_List::$template_data_tb;
        $tb='wfpklist_template_data';
        $like = '%' . $wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql_settings = "CREATE TABLE IF NOT EXISTS `$table_name` (
			  `id_wfpklist_template_data` int(11) NOT NULL AUTO_INCREMENT,
			  `template_name` varchar(200) NOT NULL,
			  `template_html` text NOT NULL,
			  `template_from` varchar(200) NOT NULL,
              `is_dc_compatible` int(11) NOT NULL DEFAULT '0',
			  `is_active` int(11) NOT NULL DEFAULT '0',
			  `template_type` varchar(200) NOT NULL,
			  `created_at` int(11) NOT NULL DEFAULT '0',
			  `updated_at` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY(`id_wfpklist_template_data`)
			) DEFAULT CHARSET=utf8;";
            dbDelta($sql_settings);
        }else
        {
	        $search_query = "SHOW COLUMNS FROM `$table_name` LIKE 'is_dc_compatible'";
	        if(!$wpdb->get_results($search_query,ARRAY_N)) 
	        {
	        	$wpdb->query("ALTER TABLE `$table_name` ADD `is_dc_compatible` int(11) NOT NULL DEFAULT '0' AFTER `template_from`");
	        }
        }
        //creating table for saving template data================
	}

    public function use_migrate_values() {
        
        $show_preview = Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_preview' );
        if ( !empty( $show_preview )
            && ( 'enabled' === $show_preview || 'disabled' === $show_preview )
        ) {
            if ( 'enabled' === $show_preview ) {
                Wf_Woocommerce_Packing_List::update_option( 'woocommerce_wf_packinglist_preview', 'No' );
            } else if ( 'disabled' === $show_preview ) {
                Wf_Woocommerce_Packing_List::update_option( 'woocommerce_wf_packinglist_preview', 'Yes' );
            }
        }

        
        // invoice attachment for email classes
        $invoice_module_id = Wf_Woocommerce_Packing_List::get_module_id( 'invoice' );
        $invoice_options = get_option( $invoice_module_id );
        if ( !empty( $invoice_options ) && is_array( $invoice_options ) && isset( $invoice_options['woocommerce_wf_generate_for_orderstatus'] ) && !empty( $invoice_options['woocommerce_wf_generate_for_orderstatus'] ) ) {    
            
            if ( !isset( $invoice_options['wt_pdf_invoice_attachment_wc_email_classes'] ) && ( 
                    ( isset( $invoice_options['woocommerce_wf_add_invoice_in_customer_mail'] ) && 
                    !empty( $invoice_options['woocommerce_wf_add_invoice_in_customer_mail'] ) 
                    ) ||
                    ( isset( $invoice_options['woocommerce_wf_add_invoice_in_admin_mail'] ) && 
                    !empty( $invoice_options['woocommerce_wf_add_invoice_in_admin_mail '] ) 
                    ) 
            )) {
                
                $invoice_attachment_wc_email_classes = array();
                if ( 'Yes' === $invoice_options['woocommerce_wf_add_invoice_in_admin_mail '] ) {
                    $invoice_attachment_wc_email_classes[] = 'new_order';
                    $invoice_attachment_wc_email_classes[] = 'new_renewal_order';
                }

                $order_status_wc_email_class_map_arr = Wt_Pklist_Common::wc_order_status_email_class_mapping();
                $choosen_order_status = isset( $invoice_options['woocommerce_wf_add_invoice_in_customer_mail'] ) ? $invoice_options['woocommerce_wf_add_invoice_in_customer_mail'] : array();
                if ( !empty( $choosen_order_status ) && is_array( $choosen_order_status ) && !empty( $order_status_wc_email_class_map_arr ) && is_array( $order_status_wc_email_class_map_arr ) ) {
                    foreach ( $choosen_order_status as $order_status ) {
                        if ( isset( $order_status_wc_email_class_map_arr[ $order_status ] ) ) {
                            $invoice_attachment_wc_email_classes[] = $order_status_wc_email_class_map_arr[ $order_status ];
                        }
                    }
                }
                
                if ( !empty( $invoice_attachment_wc_email_classes ) ) {
                    $invoice_options['wt_pdf_invoice_attachment_wc_email_classes'] = $invoice_attachment_wc_email_classes;  
                    update_option( $invoice_module_id, $invoice_options );
                }   
            }
        }
    }
}
}