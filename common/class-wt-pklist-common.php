<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.webtoffee.com
 * @since      4.1.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/common
 */

if(!class_exists('Wt_Pklist_Common'))
{
class Wt_Pklist_Common
{
    /**
     * The ID of this plugin.
     *
     * @since    4.1.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    4.1.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    private static $instance = null;
    public static $modules = array(
        'wt-wc-product'
    );
    private static $hpos_enabled = null;
    private static $default_meta_values = array();
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        self::$default_meta_values = array(
            'wf_invoice_number' => 'XXX-1111-YYYY',
        );
    }

    public static function get_instance($plugin_name, $version)
    {
        if(self::$instance==null)
        {
            self::$instance=new Wt_Pklist_Common($plugin_name, $version);
        }
        return self::$instance;
    }

    public function load_common_modules(){
        if(!empty(self::$modules)){
            foreach(self::$modules as $c_module){
                $module_file    = plugin_dir_path( __FILE__ )."modules/".$c_module."/class-".$c_module.".php";
                if(file_exists($module_file)){
                    require_once $module_file;
                }
            }
        }
    }

    /**
     * Is WooCommerce HPOS enabled
     * 
     * @since   4.1.0
     * @static
     * @return  bool True when enabled otherwise false
     */
    public static function is_wc_hpos_enabled()
    {
        if(is_null(self::$hpos_enabled))
        {
            if(class_exists('Automattic\WooCommerce\Utilities\OrderUtil'))
            {
                self::$hpos_enabled = Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
            }else
            {
                self::$hpos_enabled = false;
            }
        }
        return self::$hpos_enabled;
    }

    /**
     * Get WC_Order object from the given value.
     * 
     * @since   4.1.0
     * @static
     * @param   int|WC_order    $order      Order id or order object
     * @return  WC_order        Order object
     */
    public static function get_order($order)
    {
        return (is_int($order) || (is_string($order) && 0 < absint($order)) ? wc_get_order(absint($order)) : $order);
    }

    
    /**
     * Get order id from the given value.
     * 
     * @since   4.1.0
     * @static
     * @param   int|WC_order    $order      Order id or order object
     * @return  int             Order id
     */
    public static function get_order_id($order)
    {
        return (is_int($order) || (is_string($order) && 0 < absint($order))) ? absint($order) : $order->get_id();
    }

    /**
     * To Check if the currenct order is refunded through the status change
     *
     * @param object $order
     * @return boolean
     */
    public static function is_this_refunded( $order ) {
        if(!is_null($order)){
            return "shop_order_refund" === $order->get_type();
        }
		return false;
	}

    /**
     * To get the parent order id of current refunded order
     *
     * @param object $order
     * @return void
     */
    public static function get_refunded_parent_id( $order ) {
        if(!is_null($order)){
            return $order->get_parent_id();
        }
        return 0;
	}

    public static function get_refunded_parent_order( $order ) {
		// only try if this is actually a refund
		if ( ! self::is_this_refunded( $order ) ) {
			return $order;
		}

		$parent_order_id = self::get_refunded_parent_id( $order );
        if(0 > $parent_order_id){
            $order = wc_get_order( $parent_order_id );
        }
		return $order;
	}

    /**
     * Get the order meta
     * @since 4.1.0.1 - [Fix] - Get meta value, if even ACF is active
     * @param object|string|int $order
     * @param string $meta_key
     * @param boolean $single
     * @param string $default
     * @return string|int|float|array
     */
    public static function get_order_meta($order, $meta_key, $single = false, $default = '')
    {    
        $order = self::get_order($order);
        if(is_null($order) && !$default)
        {
            return (isset(self::$default_meta_values[$meta_key]) ? self::$default_meta_values[$meta_key] : '');
        }
        $meta_value = '';
        $actual_meta_key = $meta_key;
        $meta_key = '_' === substr( $meta_key, 0, 1 ) ? ltrim($meta_key, '_') : $meta_key;
        if("order_currency" === $meta_key){
            $meta_key = 'currency';
        }
        

        // To get WC abstract properties
        if(true === self::is_wc_order_prop( $meta_key )){
            $function = 'get_' . $meta_key;
            if ( is_callable( array( $order, $function ) ) ) {
                return $order->{$function}();
            }
        }

        // To get other than WC abstract properties
        if ( empty($meta_value) && !self::is_wc_order_prop( $meta_key ) ) {
			$meta_value = $order->get_meta( $meta_key,$single );
		}

		// if meta_value is still empty then try prefixed with underscore (not when ACF is active!)
		if ( empty( $meta_value ) && '_' !== substr( $meta_key, 0, 1 ) && !self::is_wc_order_prop( "_{$meta_key}" ) && !class_exists('ACF') ) {
			$meta_value = $order->get_meta( "_{$meta_key}",$single );
		}
        
		// WC3.0 fallback to properties
		$property = str_replace('-', '_', sanitize_title( ltrim($meta_key, '_') ) );
		if ( empty( $meta_value ) && is_callable( array( $order, "get_{$property}" ) ) ) {
			$meta_value = $order->{"get_{$property}"}( 'view' );
		}

        // fallback to parent for refunds
		if ( empty( $meta_value ) && self::is_this_refunded( $order ) ) {
			$parent_order = self::get_refunded_parent_order( $order );
			if ( !self::is_wc_order_prop( $meta_key ) ) {
				$meta_value = $parent_order->get_meta( $meta_key,$single );
			}

			// WC3.0 fallback to properties
			if ( empty( $meta_value ) && is_callable( array( $parent_order, "get_{$property}" ) ) ) {
				$meta_value = $parent_order->{"get_{$property}"}( 'view' );
			}
		}

        if ( empty($meta_value) && !self::is_wc_order_prop( $meta_key ) ) {
			$meta_value = $order->get_meta( $actual_meta_key,$single );
		}
        return $meta_value;
    }

    /**
     * Update order meta.
     * HPOS and non-HPOS compatible
     * 
     * @since   4.1.0
     * @static
     * @param   int|WC_order    $order      Order id or order object
     * @param   string          $meta_key   Meta key
     * @param   mixed           $value      Value for meta
     */
    public static function update_order_meta($order, $meta_key, $value)
    {
        if(self::is_wc_hpos_enabled())
        {
            $order = self::get_order($order);
            $order->update_meta_data($meta_key, $value);

            /**
             *  if post and order table are not synchronized,
             *  then delete the meta key and meta value from the post meta table
             */ 
            if("yes" !== get_option( 'woocommerce_custom_orders_table_data_sync_enabled' )){
                $order_id = self::get_order_id($order);
                update_post_meta($order_id, $meta_key, $value);
            }
            $order->save();
        }else
        {
            $order = self::get_order($order);
            $order_id = self::get_order_id($order);
            update_post_meta($order_id, $meta_key, $value);
            $order->save();

            /**
             *  If the post and order table are not synchronized or HPOS is not enabled yet,
             *  then delete the meta key and meta value from the wc_order_meta table
             */
            if("yes" !== get_option( 'woocommerce_custom_orders_table_data_sync_enabled' )){
                self::add_meta_to_wc_order_table($order,$meta_key,$value);
            }
        }
    }

    /**
     * Delete order meta.
     * HPOS and non-HPOS compatible
     * 
     * @since   4.1.0
     * @static
     * @param   int|WC_order    $order      Order id or order object
     * @param   string          $meta_key   Meta key
     */
    public static function delete_order_meta($order, $meta_key)
    {
        if(self::is_wc_hpos_enabled())
        {
            $order = self::get_order($order);
            $order->delete_meta_data($meta_key);
            $order->save();

            delete_post_meta($order->get_id(), $meta_key); //fallback
        }else
        {
            $order_id = self::get_order_id($order);
            delete_post_meta($order_id, $meta_key);

            //fallback
            $order = wc_get_order($order_id);
            $order->delete_meta_data($meta_key);
            $order->save();
        }
    }

    /**
     * To delete all the row of given meta key from the order meta table, if WC HPOS is enabled
     *
     * @param string $meta_key
     * @return void
     */
    public static function delete_order_meta_by_key($meta_key){

        if(!empty($meta_key) && is_string($meta_key)){
            delete_post_meta_by_key( $meta_key );
            $delete_from_order_table = false;
            if(self::is_wc_hpos_enabled())
            {
                // post & order table are not synchronized
                if("yes" !== get_option( 'woocommerce_custom_orders_table_data_sync_enabled' )){
                    $delete_from_order_table = true;
                }
            }else
            {
                // Use post table enabled
                $delete_from_order_table = true;
            }

            if(true === $delete_from_order_table){
                global $wpdb;
                $table_name = $wpdb->prefix.'wc_orders_meta';
                if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name){
                    $del_order_meta_query = "DELETE FROM $table_name WHERE `meta_key` IN('".esc_sql($meta_key)."')";
                    $wpdb->query( $del_order_meta_query );
                }
            }
        }
    }

    /**
     * To Check if the given order is fully refunded through the status change only
     *
     * @param object $order
     * @return boolean
     */
    public static function is_fully_refunded($order){
        if(!is_null($order)){
            $all_refund_orders = $order->get_refunds();
            $number_of_refunds = count($all_refund_orders);
            $order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
            if(1 === $number_of_refunds && "refunded" === $order_status){
                return true;
            }else{
                return false;
            }
        }
        return false;
	}

    /**
     * To check if the meta key is the WC abstract property
     *
     * @param string $key
     * @return boolean
     */
    public static function is_wc_order_prop( $meta_key ) {
		// Taken from WC class
		$order_props = array(
			// Abstract order props
			'parent_id',
			'status',
			'currency',
			'version',
			'prices_include_tax',
			'date_created',
			'date_modified',
			'discount_total',
			'discount_tax',
			'shipping_total',
			'shipping_tax',
			'cart_tax',
			'total',
			'total_tax',
			// Order props
			'customer_id',
			'order_key',
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
			'billing_email',
			'billing_phone',
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'shipping_country',
			'payment_method',
			'payment_method_title',
			'transaction_id',
			'customer_ip_address',
			'customer_user_agent',
			'created_via',
			'customer_note',
			'date_completed',
			'date_paid',
			'cart_hash',
		);

		if ( version_compare( WC()->version, '5.6' ) >= 0 ) {
			$order_props[] = 'shipping_phone';
		}
        
        $order_props = apply_filters('wt_pklist_add_wc_abstract_property',$order_props);
		return in_array($meta_key, $order_props);
	}

    public static function which_table_to_take(){
        if(self::is_wc_hpos_enabled()){
			if("yes" !== get_option( 'woocommerce_custom_orders_table_data_sync_enabled' )){
				$which_table = 'order_table';
			}else{
				$which_table = 'post_table';
			}
		}else{
			$which_table = "post_table";
		}
        return $which_table;
    }

    public static function meta_key_exists_in_wc_order_meta($order_id,$meta_key){
        global $wpdb;
        $table_name = $wpdb->prefix.'wc_orders_meta';
        $search = $wpdb->get_row($wpdb->prepare("SELECT `id` from $table_name WHERE `meta_key` IN (%s) AND `order_id` = %d",array($meta_key,$order_id)));
        if(!$search){
            return false;
        }else{
            return true;
        }
    }

    public static function add_meta_to_wc_order_table($order,$meta_key,$value){
        global $wpdb;
        $table_name = $wpdb->prefix.'wc_orders_meta';
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name){
            $order_id = self::get_order_id($order);
            if(self::meta_key_exists_in_wc_order_meta($order_id,$meta_key)){
                $update_data        = array('meta_value' => $value);
                $update_data_type   = array( '%s' );
                $update_where       = array(
                    'order_id'  => $order_id,
                    'meta_key'  => $meta_key
                );
                $update_where_type  = array('%d','%s');
                $wpdb->update($table_name,$update_data,$update_where,$update_data_type,$update_where_type);
            }else{
                $insert_data = array(
                    'order_id'      =>  $order_id,
                    'meta_key'      =>  $meta_key,
                    'meta_value'    =>  $value
                );
                $insert_data_type = array(
                    '%d','%s','%s'
                );
                $wpdb->insert($table_name,$insert_data,$insert_data_type);
            }
        }
    }

    /**
     * Is current admin page is HPOS enabled orders page
     * 
     * @since   4.1.0.1
     * @static
     * @return  bool    True when current page is HPOS orders page
     */
    public static function is_hpos_orders_page()
    {
        $basename = basename(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH));
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : ''; 
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : ''; 
        return ('admin.php' === $basename && 'wc-orders' === $page && ('' === $action || '-1' === $action));
    }
    /**
     * Is current admin page is HPOS enabled order edit page
     * 
     * @since   4.1.0.1
     * @static
     * @return  bool    True when current page is HPOS order edit page
     */
    public static function is_hpos_order_edit_page()
    {
        $basename = basename(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH));
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : ''; 
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : ''; 
        return ('admin.php' === $basename && 'wc-orders' === $page && 'edit' === $action);
    }

    /**
     * Get the YITH Gift cards coupon amount
     *
     * @since 4.3.0
     * @param object $order
     * @return int|float|string
     */
    public static function get_yith_applied_coupon_total( $order ) {
        if( empty( $order ) ) {
            return 0;
        }
        $total  = 0;
        $order_gift_cards   = $order->get_meta( '_ywgc_applied_gift_cards' );
        if( !empty( $order_gift_cards ) ) {
            $total  = array_sum($order_gift_cards);
        }
        return $total;
    }

    /**
     * Get the YITH Gift cards codes
     *
     * @since 4.3.0
     * @param object $order
     * @return array
     */
    public static function get_yith_applied_coupon_codes ($order) {
        if( empty( $order ) ) {
            return array();
        }
        $codes = array();
        $order_gift_cards   = $order->get_meta( '_ywgc_applied_gift_cards' );
        if( !empty( $order_gift_cards ) ) {
            $codes = array_keys($order_gift_cards);
        }
        return $codes;
    }

    /**
     * To check if the order has local pickup shipping method alone
     * 
     * @since 4.4.1
     * @param object $order
     * @return boolean
     */
    public static function has_order_local_pickup_only($order) {
        $order = self::get_order($order);
        if( !empty( $order ) ) {
            $shipping_methods = $order->get_shipping_methods();
            $shipping_method_id_arr = array();
            if ( !empty($shipping_methods) ) {
                foreach ( $shipping_methods as $shipping_method ) {
                    $shipping_method_id_arr[] = $shipping_method->get_method_id();
                }
            }

            /**
             * Conditions
             * shipping method should not be empty
             * shipping method count is 1 and method id is local pickup
             * or all shipping method are local pickup
             */
            if( !empty( $shipping_method_id_arr ) && ( ( 1 === count( $shipping_method_id_arr ) && "local_pickup" === $shipping_method_id_arr[0] ) || ( 1 < count( $shipping_method_id_arr ) && 1 === count( array_unique( $shipping_method_id_arr ) ) && "local_pickup" === array_unique( $shipping_method_id_arr )[0] ) ) ) {
                return true;
            }
        }
        return false;
    }

    public static function wt_pklist_pdf_add_filters( $filters ) {
		foreach ( $filters as $filter ) {
            if ( is_array( $filter ) && isset( $filter[0] ) && isset( $filter[1] ) ) { // hook name and call back function name should be there.
                $filter = self::wt_pklist_pdf_normalize_filter_args( $filter );
			    add_filter( $filter['hook_name'], $filter['callback'], $filter['priority'], $filter['accepted_args'] );
            }
		}
	}

	public static function wt_pklist_pdf_remove_filters( $filters ) {
		foreach ( $filters as $filter ) {
            if ( is_array( $filter ) && isset( $filter[0] ) && isset( $filter[1] ) ) { // hook name and call back function name should be there.
                $filter = self::wt_pklist_pdf_normalize_filter_args( $filter );
                remove_filter( $filter['hook_name'], $filter['callback'], $filter['priority'] );
            }
		}
	}

	public static function wt_pklist_pdf_normalize_filter_args( $filter ) {
		$filter = array_values( $filter ); 
		$hook_name = $filter[0];
		$callback = $filter[1];
		$priority = isset( $filter[2] ) ? $filter[2] : 10;
		$accepted_args = isset( $filter[3] ) ? $filter[3] : 1;
		return compact( 'hook_name', 'callback', 'priority', 'accepted_args' );
	}

    /**
     * Get all the settings page slugs of pdf invoice plugin and its add-ons
     *
     * @return array
     */
    public static function wt_pdf_get_all_settings_page_slugs() {
        $pdf_settings_pages = array(
            'wf_woocommerce_packing_list',
            'wf_woocommerce_packing_list_invoice',
            'wf_woocommerce_packing_list_packinglist',
            'wf_woocommerce_packing_list_deliverynote',
            'wf_woocommerce_packing_list_shippinglabel',
            'wf_woocommerce_packing_list_dispatchlabel',
            'wf_woocommerce_packing_list_picklist',
            'wf_woocommerce_packing_list_proformainvoice',
            'wf_woocommerce_packing_list_addresslabel',
        );
        return apply_filters( 'wt_pdf_all_settings_page_slugs', $pdf_settings_pages );
    }
    
    /**
	 * To get all the email classes from WooCommerce
     * @since 4.7.0
	 * 
	 * @return array
	 */
	public static function wt_pdf_get_wc_email_classes( $default_wc_emails_only = false ) {
		if ( empty( $_GET['page'] ) || 
            !in_array( $_GET['page'], self::wt_pdf_get_all_settings_page_slugs() ) ) {
			return array();
		}

		// Get all email classes from WooCommerce
		if ( function_exists( 'WC' ) ) {
			$mailer = WC()->mailer();
		} else {
			global $woocommerce;

			if ( empty( $woocommerce ) ) {
				return apply_filters( 'wt_pklist_wc_email_classes', array() );
			}

			$mailer = $woocommerce->mailer();
		}
		$wc_emails = $mailer->get_emails();

		// Emails that are not related to orders
		$non_order_emails = array(
			'customer_reset_password',
			'customer_new_account'
		);

		$emails = array();
		foreach ($wc_emails as $class => $email) {
			if ( !is_object( $email ) ) {
				continue;
			}
			if ( !in_array( $email->id, $non_order_emails ) ) {
				switch ( $email->id ) {
					case 'new_order':
						$emails[$email->id] = sprintf('%s (%s)', $email->title, __( 'Admin email', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
						break;
					case 'customer_invoice':
						$emails[$email->id] = sprintf('%s (%s)', $email->title, __( 'Manual email', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
						break;
					default:
						$emails[$email->id] = $email->title;
						break;
				}
			}
		}

        if ( !empty( $emails ) ) {
            // Renewal order email classes.
            $renewal_order_emails = array(
                'new_renewal_order' => __( 'Admin email (Renewal order)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'customer_renewal_invoice' => __( 'Manual email (Renewal order)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'customer_on_hold_renewal_order' => __( 'Order on-hold (Renewal order)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'customer_processing_renewal_order' => __( 'Order processing (Renewal order)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'customer_completed_renewal_order' => __( 'Order completed (Renewal order)', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            );

            $emails = array_merge( $emails, $renewal_order_emails );
            $emails = array_unique( $emails );

            if ( $default_wc_emails_only ) {
                $default_wc_emails = self::default_wc_email_classes();
                $emails = array_intersect_key( $emails, array_flip( $default_wc_emails ) );
                return $emails;
            }
        }
        
		return apply_filters( 'wt_pklist_wc_email_classes', $emails );
	}

    /**
     *  Get the order statuses and correspoding WC email classes mapping array
     *  @since 4.7.0
     *  @return array
     */
    public static function wc_order_status_email_class_mapping () {
        
        $status_with_classes = array(
            'wc-cancelled' => 'cancelled_order',
            'wc-failed' => 'failed_order',
            'wc-on-hold' => 'customer_on_hold_order',
            'wc-processing' => 'customer_processing_order',
            'wc-completed' => 'customer_completed_order',
            'wc-refunded' => 'customer_refunded_order',
            'customer_note' => 'customer_note',
            'customer_invoice' => 'customer_invoice',
        );
        return apply_filters( 'wt_pdf_wc_order_status_email_class_mapping', $status_with_classes );
    }
    
    public static function default_wc_email_classes () {
        
        return array(
            'cancelled_order',
            'failed_order',
            'customer_on_hold_order',
            'customer_processing_order',
            'customer_completed_order',
            'customer_refunded_order',
        );
    }


    /**
     * To Check if the current date is on or between the start and end date of black friday and cyber monday banner for 2024.
     * @since 4.7.0
     */
    public static function is_bfcm_season() {
        $start_date = new DateTime( '25-NOV-2024, 12:00 AM', new DateTimeZone( 'Asia/Kolkata' ) ); // Start date.
        $current_date = new DateTime( 'now', new DateTimeZone( 'Asia/Kolkata' ) ); // Current date.
        $end_date = new DateTime( '02-DEC-2024, 11:59 PM', new DateTimeZone( 'Asia/Kolkata' ) ); // End date.

        /**
         * check if the date is on or between the start and end date of black friday and cyber monday banner for 2024.
         */
        if ( $current_date < $start_date  || $current_date >= $end_date) {
            return false;
        }
        return true;
    }
}
}