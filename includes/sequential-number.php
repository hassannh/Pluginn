<?php
/**
 * Template sequential number generator and processor
 *
 * @link       
 * @since 4.0.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_Sequential_Number
{
	public $module_base='sequential-number';
	public static $module_id_static='';
	private $to_module='';
	private $to_module_id='';
	private static $to_module_title='';
	public static $return_dummy_invoice_number=false;  //it will return dummy invoice number if force generate is on
	public function __construct()
	{

	}

	/**
	* Get order date timestamp
	* @since 4.0.0
	* @return integer
	*/
    protected static function get_orderdate_timestamp($order_id)
    {
    	$order_date=get_the_date('Y-m-d h:i:s A',$order_id);
		return strtotime($order_date);
    }

    /**
	* Function to generate sequential number
	* @since 4.0.0
	* @return mixed
	*/
    public static function generate_sequential_number($order, $module_id, $keys= array('number'=>'wf_invoice_number', 'date'=>'wf_invoice_date','enable'=>''), $force_generate=true) 
    {
	    //if module (Eg: invoice) is disabled then force generate is always false, otherwise the value of argument
	    if("" !== $keys['enable']) //if module has such an option (Invoice module have that option)
	    {
	    	$force_generate= ("No" === Wf_Woocommerce_Packing_List::get_option($keys['enable'], $module_id)) ? false : $force_generate;
	    }
	    $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    $wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, $keys['number'], true);
		if(false === $force_generate){
			return $wf_invoice_id;
		}
	    if(!empty($wf_invoice_id))
	    {
	    	/* order date as invoice date, adding compatibility with old orders  */
	    	$invoice_date=Wt_Pklist_Common::get_order_meta($order_id, $keys['date'], true);
	    	$invoice_date_hid=Wt_Pklist_Common::get_order_meta($order_id, '_'.$keys['date'], true);
	    	if(empty($invoice_date) && empty($invoice_date_hid))
	    	{
	    		/* set order date as invoice date */
	    		$order_date=self::get_orderdate_timestamp($order_id);
				Wt_Pklist_Common::update_order_meta($order_id, '_'.$keys['date'], $order_date);
	    	}else
	    	{
	    		if(!empty($invoice_date))
	    		{
					Wt_Pklist_Common::delete_order_meta($order_id, $keys['date']);
	    			Wt_Pklist_Common::update_order_meta($order_id, '_'.$keys['date'], $invoice_date);
	    		}
	    	}
	        return $wf_invoice_id;
	    }else
	    {
	    	if(false === $force_generate)
	    	{
	    		if(self::$return_dummy_invoice_number)
	    		{
	    			return 123456;
	    		}else
	    		{
	    			return '';
	    		}
	    	}
	    }
	    if(self::$return_dummy_invoice_number)
	    {
	    	return 123456;
	    }
	    //$all_invoice_numbers =self::wf_get_all_invoice_numbers();
	    $wf_invoice_as_ordernumber =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber', $module_id);
	    $generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $module_id);

	   	if(isset($_GET['type'])){
    		if("preview_invoice" === $_GET['type'] || "preview_dispatchlabel" === $_GET['type']){
	   			if((is_array($generate_invoice_for)) && (!in_array('wc-'.$order->get_status(), $generate_invoice_for))){
	    			return "";
	    		}else{
	    			$old_order_invoice = self::not_to_generate_invoice_number_for_old_orders($order_id,$module_id);
	    			if($old_order_invoice){
	    				return "";
	    			}
	    		}
	    	}
	   	}else{
	   		$old_order_invoice = self::not_to_generate_invoice_number_for_old_orders($order_id,$module_id);
			if($old_order_invoice){
				return "";
			}
	   	}

	    if("Yes" === $wf_invoice_as_ordernumber)
	    {
	    	if(is_a($order, 'WC_Order') || is_a($order,'WC_Subscriptions'))
	    	{
	    		$order_num=	$order->get_order_number();
	    	}else
	    	{
	    		$parent_id= $order->get_parent_id();
	    		$parent_order=( WC()->version < '2.7.0' ) ? new WC_Order($parent_id) : new wf_order($parent_id);
	    		$order_num=	$parent_order->get_order_number();
	    	}
	    	$inv_num= $order_num;	
	    }else
	    {
	    	$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number', $module_id); 
	    	$inv_num=++$current_invoice_number;
	    	$padded_next_invoice_number=self::add_sequential_padding($inv_num, $module_id);
	        $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number, $module_id, $order);
	        while(self::wf_is_sequential_number_exists($postfix_prefix_padded_next_invoice_number, $keys['number']))
            { 
                 $inv_num++;
                 $padded_next_invoice_number=self::add_sequential_padding($inv_num, $module_id);
                 $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number, $module_id, $order);               
            }
            Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_Current_Invoice_number', $inv_num, $module_id);
	    }
	    $padded_invoice_number=self::add_sequential_padding($inv_num, $module_id);
        $invoice_number=self::add_postfix_prefix($padded_invoice_number, $module_id, $order);
        Wt_Pklist_Common::update_order_meta($order_id, $keys['number'], $invoice_number);

        $orderdate_as_invoicedate=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_orderdate_as_invoicedate', $module_id);
        $invoicedate=time();
        if("Yes" === $orderdate_as_invoicedate)
        {
        	$invoicedate=self::get_orderdate_timestamp($order_id);
        }
        Wt_Pklist_Common::update_order_meta($order_id, '_'.$keys['date'], $invoicedate);
		if(isset($_GET['wt_dont_show_again']) && (1 === $_GET['wt_dont_show_again'] || "1" === $_GET['wt_dont_show_again'])){
			self::dont_show_again_doc_create_popup();  
		}
        return $invoice_number;
	}

	public static function dont_show_again_doc_create_popup(){
		$user_id = get_current_user_id();
		if(0 !== $user_id){
			update_user_meta($user_id,'wt_pklist_doc_create_dont_show_popup',1);
		}
	}

	public static function not_to_generate_invoice_number_for_old_orders($order_id,$module_id){
		$invoice_for_prev_install_order = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_prev_install_orders',$module_id);
	   	if("No" === $invoice_for_prev_install_order){
	   		$order_date_format='Y-m-d h:i:s';
	   		$order_date=(get_the_date($order_date_format,$order_id));
			if(false === get_option('wt_pklist_installation_date')){
				if(get_option('wt_pklist_start_date')){
					$install_date = get_option('wt_pklist_start_date',time());
				}else{
					$install_date = time();
				}
				update_option('wt_pklist_installation_date',$install_date);
			}
	        $utc_timestamp = get_option('wt_pklist_installation_date');
			$utc_timestamp_converted = date( 'Y-m-d h:i:s', $utc_timestamp );
			$local_timestamp = get_date_from_gmt( $utc_timestamp_converted, 'Y-m-d h:i:s' );
	   		if($order_date < $local_timestamp){
	   			return true;
	   		}
	   	}

	   	return false;
	}
	/**
	* Get sequential number date (Eg: Invoice date)
	* @since 4.0.0
	* @return mixed
	*/
    public static function get_sequential_date($order_id, $key, $date_format, $order)
    {
    	$invoice_date=Wt_Pklist_Common::get_order_meta($order_id, '_'.$key, true);
    	if($invoice_date)
    	{
    		return (empty($invoice_date) ? '' : date_i18n($date_format, $invoice_date));
    	}else
    	{
    		if(self::$return_dummy_invoice_number)
	    	{
	    		return date_i18n($date_format);
	    	}else
	    	{
	    		return '';
	    	}
    	}
    }

    /** 
    *	@since 4.0.0
	* 	Get all sequential numbers
	* 	@return int
	*/
	public static function wf_get_all_sequential_numbers($key='wf_invoice_number') 
	{
        global $wpdb;
        $post_type = 'shop_order';

        $r = $wpdb->get_col($wpdb->prepare("
	    SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	    LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	    WHERE pm.meta_key = '%s' 
	    AND p.post_type = '%s'", $key, $post_type));
        return $r;
    }


    /** 
    *	@since 4.0.0
	* 	Check sequential number already exists
	* 	@return boolean
	*/
	public static function wf_is_sequential_number_exists($invoice_number, $key='wf_invoice_number') 
	{
		global $wpdb;
		if("order_table" === Wt_Pklist_Common::which_table_to_take()){
			$table_name = $wpdb->prefix.'wc_orders_meta';
			$r = $wpdb->get_col($wpdb->prepare("
			SELECT COUNT(om.meta_value) AS inv_exists FROM {$table_name} om
			WHERE om.meta_key = '%s' AND om.meta_value = '%s'", $key,$invoice_number));
			return $r[0]>0 ? true : false;
		}else{
			$post_type = 'shop_order';
			$r = $wpdb->get_col($wpdb->prepare("
			SELECT COUNT(pm.meta_value) AS inv_exists FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = '%s' 
			AND p.post_type = '%s' AND pm.meta_value = '%s'", $key, $post_type,$invoice_number));
			return $r[0]>0 ? true : false;
		}
	}

	/**
	*	@since 4.0.0
	* 	This function sets the autoincrement value while admin edits sequential number settings
	*/
	public function set_current_sequential_autoinc_number($module_id)
	{ 
		$wf_invoice_as_ordernumber =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber', $module_id);
	    $generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $module_id);
	    if("Yes" === $wf_invoice_as_ordernumber)
	    {
	    	return true; //no need to set a starting number	
	    }else
	    {
	    	$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number', $module_id); 
	    	$inv_num=++$current_invoice_number;
	    	$padded_next_invoice_number=self::add_sequential_padding($inv_num,$module_id);
	        $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,$module_id);
	        while(self::wf_is_sequential_number_exists($postfix_prefix_padded_next_invoice_number))
            { 
                 $inv_num++;
                 $padded_next_invoice_number=self::add_sequential_padding($inv_num,$module_id);
                 $postfix_prefix_padded_next_invoice_number=self::add_postfix_prefix($padded_next_invoice_number,$module_id);               
            }
            //$inv_num is the next invoice number so next starting number will be one lesser than the $inv_num
            $inv_num=$inv_num-1;
            Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_Current_Invoice_number',$inv_num,$module_id);
            return true;
	    }
	    return false;
	}

	/**
	*	@since 4.0.4
	* 	Adding padding number to sequential number
	*/
	public static function add_sequential_padding($wf_invoice_number,$module_id) 
	{
        $padded_invoice_number = '';
        $padding_count =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_padding_number',$module_id)- strlen($wf_invoice_number);
        if ($padding_count > 0) {
            for ($i = 0; $i < $padding_count; $i++)
            {
                $padded_invoice_number .= '0';
            }
        }
        return $padded_invoice_number.$wf_invoice_number;
    }

    
    /**
    *   @since 4.0.0
	* 	Replace date shortcode from sequential number prefix/postfix data
	*
	* 	@return string
	*/
    public static function get_shortcode_replaced_date($shortcode_text, $order=null) 
	{	
	    preg_match_all("/\[([^\]]*)\]/", $shortcode_text, $matches);
	    if(!empty($matches[1]))
	    { 
	        foreach($matches[1] as $date_shortcode) 
	        { 
	        	$match=array();
	        	$date_val=time();
	        	$date_shortcode_format=$date_shortcode;
	            if(preg_match('/data-val=\'(.*?)\'/s', $date_shortcode, $match))
	            { 
	            	if("order_date" === trim($match[1]))
	            	{
	            		$date_shortcode_format=trim(str_replace($match[0], '', $date_shortcode));           		
	            		if(!is_null($order))
	            		{ 
	            			$wc_version=WC()->version;
							$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
							$date_val=strtotime(get_the_date('Y-m-d H:i:s', $order_id));
	            		}
	            	}
	            }
	            $date=date($date_shortcode_format, $date_val);
	            $shortcode_text=str_replace("[$date_shortcode]", $date, $shortcode_text); 
	        }
	    }
	    return $shortcode_text;
	}

    /** 
	* 	@since 4.0.0
	*	Add Prefix/Postfix to sequential number
	* 	@return string
	*/
	public static function add_postfix_prefix($padded_invoice_number,$module_id, $order=null) 
	{          
        $invoice_format =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_format',$module_id);
        $prefix_data =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_prefix',$module_id);
        $postfix_data =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_postfix',$module_id);
        if("" === $invoice_format)
        {
            if("" !== $prefix_data && "" !== $postfix_data)
            {
            	$invoice_format='[prefix][number][suffix]';
            }
            elseif("" !== $prefix_data)
            {
            	$invoice_format = '[prefix][number]'; 
            }
            elseif("" !== $postfix_data)
            {
                $invoice_format = '[number][suffix]'; 
            }
        }
        if("" !== $prefix_data)
        {
            $prefix_data=self::get_shortcode_replaced_date($prefix_data, $order);
        }
        if("" !== $postfix_data)
        {
            $postfix_data=self::get_shortcode_replaced_date($postfix_data, $order);
        }
        return str_replace(array('[prefix]','[number]','[suffix]'),array($prefix_data,$padded_invoice_number,$postfix_data),$invoice_format); 
    }
}