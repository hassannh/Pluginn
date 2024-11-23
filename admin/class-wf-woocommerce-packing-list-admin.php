<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 * @author     WebToffee <info@webtoffee.com>
 */
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

class Wf_Woocommerce_Packing_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules=array(
		'customizer',
		'uninstall-feedback',
		//'freevspro',
	);

	public static $existing_modules=array();

	public $bulk_actions=array();

	public static $tooltip_arr=array();

	/**
	*	To store the RTL needed or not status
	*	@since 2.6.6
	*/
	public static $is_enable_rtl=null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.5.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-banners', plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin-banners.css', array(), $this->version, 'all' );
		if(!empty(self::not_activated_pro_addons())){
			wp_enqueue_style( $this->plugin_name.'-addons-page', plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin-addons-page.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_scripts() 
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin.js', array( 'jquery','jquery-ui-autocomplete','wp-color-picker','jquery-tiptip'), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-form-wizard', plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin-form-wizard.js', array( 'jquery','jquery-ui-autocomplete','wp-color-picker','jquery-tiptip'), $this->version, false );
		//order list page bulk action filter
		$this->bulk_actions=apply_filters('wt_print_bulk_actions',$this->bulk_actions);

		$order_meta_autocomplete = self::order_meta_dropdown_list();
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
		$is_rtl = is_rtl() ? 'rtl' : 'ltr';
		$user_id = get_current_user_id();
		$dont_show_again = false;
		if(0 !== $user_id){
			if(1 == get_user_meta($user_id, 'wt_pklist_doc_create_dont_show_popup',true) || "1" === get_user_meta($user_id, 'wt_pklist_doc_create_dont_show_popup',true)){
				$dont_show_again = true;
			}
		}
		$wt_pklist_plugin_data = $this->get_wt_pklist_plugin_data();
		$params=array(
			'nonces' => array(
		            'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
		     ),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'no_image'=>$wf_admin_img_path,
			'bulk_actions'=>array_keys($this->bulk_actions),
			'print_action_url'=>admin_url('?print_packinglist=true'),
			'order_meta_autocomplete' => json_encode($order_meta_autocomplete),
			'is_rtl' => $is_rtl,
			'wt_plugin_data' => $wt_pklist_plugin_data,
			'show_document_preview' => Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_preview' ),
			'document_access_type'	=> Wf_Woocommerce_Packing_List::get_option('wt_pklist_print_button_access_for'),
			'is_user_logged_in'	=> is_user_logged_in(),
			'msgs'=>array(
				'settings_success'=>__('Settings updated.','print-invoices-packing-slip-labels-for-woocommerce'),
				'all_fields_mandatory'=>__('All fields are mandatory','print-invoices-packing-slip-labels-for-woocommerce'),
				'settings_error'=>sprintf(__('Unable to update settings due to an internal error. %s To troubleshoot please click %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<br />', '<a href="https://www.webtoffee.com/how-to-fix-the-unable-to-save-settings-issue/" target="_blank">', '</a>'),
				'select_orders_first'=>__('You have to select order(s) first!','print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_not_gen_bulk'=>__('One or more order do not have invoice generated. Generate manually?','print-invoices-packing-slip-labels-for-woocommerce'),
				'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
				'please_wait'=>__('Please wait','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_required'=>__("is required",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_title_prompt' => __("Invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt' => __("number has not been generated yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_free_order' => __("‘Generate invoice for free orders’ is disabled in Invoice settings > Advanced. You are attempting to generate invoice for this free order. Proceed?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'creditnote_number_prompt' => __("Refund in this order seems not having credit number yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_no_from_addr' => __("Please fill the `from address` in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'),
				'fitler_code_copied' => __("Code Copied","print-invoices-packing-slip-labels-for-woocommerce"),
				'close'=>__("Close",'print-invoices-packing-slip-labels-for-woocommerce'),
				'save'=>__("Save",'print-invoices-packing-slip-labels-for-woocommerce'),
				'enter_mandatory_fields'=>__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_order_meta' => __('You can add more than 1 order meta in','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_edit_order_meta' => __('Edit','print-invoices-packing-slip-labels-for-woocommerce'),
				'buy_pro_prompt_edit_order_meta_desc' => __('You can edit an existing item by using its key.','print-invoices-packing-slip-labels-for-woocommerce'),
				'pop_dont_show_again' => $dont_show_again,
				'add_date_string_text' => __("Add","print-invoices-packing-slip-labels-for-woocommerce"),
				'request_error' => __('Request error.','print-invoices-packing-slip-labels-for-woocommerce'),
				'error_loading_data' => __('Error loading data.','print-invoices-packing-slip-labels-for-woocommerce'),
				'min_value_error' => __( 'minimum value should be', 'print-invoices-packing-slip-labels-for-woocommerce'),
				'generating_document_text' => __( 'Generating document...', 'print-invoices-packing-slip-labels-for-woocommerce' ),
				'new_tab_open_error' => __( 'Failed to open new tab. Please check your browser settings.', 'print-invoices-packing-slip-labels-for-woocommerce' ),
			)
		);
		wp_localize_script($this->plugin_name, 'wf_pklist_params', $params);

	}


	/**
    * 	@since 2.5.8
    * 	Set tooltip for form fields 
    */
    public static function set_tooltip($key,$base_id="",$custom_css="")
    {
    	$tooltip_text=self::get_tooltips($key,$base_id);
    	if("" !== $tooltip_text)
    	{
    		$rtl_css = is_rtl() ? 'left:0;' : 'right:0;';
    		$tooltip_text='<span style="color:#4d535a; '.($custom_css!="" ? $custom_css : 'top:15px; margin-left:2px; position:absolute;').$rtl_css.'" class="dashicons dashicons-editor-help wt-tips" data-wt-tip="'.wp_kses_post($tooltip_text).'"></span>';
    	}
    	return $tooltip_text;
    }

    /**
    * 	@since 2.5.8
    * 	Get tooltip config data for non form field items
    * 	@return array 'class': class name to enable tooltip, 'text': tooltip text including data attribute if not empty
    */
    public static function get_tooltip_configs($key,$base_id="")
    {
    	$out=array('class'=>'','text'=>'');
    	$text=self::get_tooltips($key,$base_id);
    	if("" !== $text)
    	{
    		$out['text']=' data-wt-tip="'.wp_kses_post($text).'"';
    		$out['class']=' wt-tips';
    	}  	
    	return $out;
    }

    /**
    *	@since 2.5.8
	* 	This function will take tooltip data from modules and store ot 
	*
	*/
	public function register_tooltips()
	{
		include(plugin_dir_path( __FILE__ ).'data/data.tooltip.php');
		self::$tooltip_arr=array(
			'main'=>$arr
		);
		/* hook for modules to register tooltip */
		self::$tooltip_arr=apply_filters('wt_pklist_alter_tooltip_data',self::$tooltip_arr);
	}

	/**
	* 	Get tooltips
	*	@since 2.5.8
	*	@param string $key array key for tooltip item
	*	@param string $base module base id
	* 	@return tooltip content, empty string if not found
	*/
	public static function get_tooltips($key,$base_id='')
	{
		$arr = ("" !== $base_id && isset(self::$tooltip_arr[$base_id]) ? self::$tooltip_arr[$base_id] : self::$tooltip_arr['main']);
		return (isset($arr[$key]) ? $arr[$key] : '');
	}

	/**
	 * Function to add Items to Orders Bulk action dropdown
	 *
	 * @since    2.5.0
	 */
	public function alter_bulk_action($actions)
	{
        return array_merge($actions,$this->bulk_actions);
	}
	

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_checkout_fields($fields) 
	{
		$checkout_fields_from_pro = apply_filters('wt_pklist_switch_pro_for_checkout_fields',false);
		if(!$checkout_fields_from_pro){
			$additional_options=Wf_Woocommerce_Packing_List::get_option('wf_invoice_additional_checkout_data_fields');
			$basic_checkout_fields = Wf_Woocommerce_Packing_List::$default_additional_checkout_data_fields;
	        if(is_array($additional_options) && count(array_filter($additional_options))>0 && is_array($basic_checkout_fields))
	        {
	            foreach ($additional_options as $value)
	            {
	            	if(in_array($value,$basic_checkout_fields)){
	            		$fields['billing']['billing_' . $value] = array(
		                    'text' => 'text',
		                    'label' => __(str_replace('_', ' ', $value), 'woocommerce'),
		                    'placeholder' => _x('Enter ' . str_replace('_', ' ', $value), 'placeholder', 'woocommerce'),
		                    'required' => false,
		                    'class' => array('form-row-wide', 'align-left'),
		                    'clear' => true
		                );
	            	}
	            }
	        }
		}
		return $fields;
	}

    /**
	 * Function to add email attachments to order email
	 *
	 * @since    2.5.0
	 */
	public function add_email_attachments($attachments, $status=null, $order=null)
	{
		if(is_object($order) && is_a($order,'WC_Order') && isset($status))
		{
            $order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$attachments=apply_filters('wt_email_attachments',$attachments,$order,$order_id,$status);
        }
		return $attachments;	
	}
   
    /**
	 * Function to add action buttons in order email
	 *
	 * 	@since    2.5.0
	 *	@since 	  2.6.5 	[Bug fix] Print button missing in email 
	 */
	public function add_email_print_actions($order)
	{
		if(is_object($order) && is_a($order,'WC_Order'))
		{
			$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			$html=apply_filters('wt_email_print_actions',$html,$order,$order_id);	
		}
	}

    /**
	 * Function to add action buttons in user dashboard order list page
	 *
	 * @since    2.5.0
	 */
	public function add_fontend_print_actions($order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		$html='';
		$html=apply_filters('wt_frontend_print_actions',$html,$order,$order_id);	
	}

	public function add_order_list_page_print_actions($actions, $order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();

		$wt_actions=array();
		$wt_actions=apply_filters('wt_pklist_intl_frontend_order_list_page_print_actions', $wt_actions, $order, $order_id);
		if(is_array($wt_actions) && count($wt_actions)>0)
		{
			foreach($wt_actions as $template_type => $action_arr)
			{
				if(is_array($action_arr))
				{
					foreach ($action_arr as $action => $title)
					{
						$show_button=true;
						$show_button=apply_filters('wt_pklist_is_frontend_order_list_page_print_action', $show_button, $template_type, $action);
						if($show_button)
						{
							/** button info to WC hook */
							$action_data=array(
								'url'  => Wf_Woocommerce_Packing_List::generate_print_url_for_user($order, $order_id, $template_type, $action),
								'name' => $title,
							);
							$actions['wt_pklist_'.$template_type.'_'.$action]=apply_filters('wt_pklist_frontend_order_list_page_print_action', $action_data, $template_type, $action, $order, $order_id);
						}
					}
				}
			}
		}

		return $actions;
	}

	public static function get_print_url($order_id, $action)
	{
		$url=wp_nonce_url(admin_url('?print_packinglist=true&post='.($order_id).'&type='.$action), WF_PKLIST_PLUGIN_NAME);
		$url=(isset($_GET['debug']) ? $url.'&debug' : $url);
		return $url;
	}

	public static function generate_print_button_data($order,$order_id,$action,$label,$icon_url,$is_show_prompt,$button_location="detail_page")
	{
		$url=self::get_print_url($order_id, $action);
		
		$href_attr='';
		$onclick='';
		$confirmation_clss='';
		if(false === Wf_Woocommerce_Packing_List::is_from_address_available()) 
    	{
    		$is_show_prompt = 3;
    	}
		if((1 === $is_show_prompt || "1" === $is_show_prompt) || (2 === $is_show_prompt || "2" === $is_show_prompt) || (3 === $is_show_prompt || "3" === $is_show_prompt))
		{
			$confirmation_clss='wf_pklist_confirm_'.$action;
			$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\','.$is_show_prompt.');"';
		}else
		{
			$href_attr=' href="'.esc_url($url).'"';
		}
		if("detail_page" === $button_location)
        {
        ?>
		<tr>
			<td>
				<a class="button tips wf-packing-list-link" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo strip_tags($label);?>" >
				<?php
				if("" !== $icon_url)
				{
				?>
					<img src="<?php echo esc_url($icon_url);?>" alt="<?php echo esc_attr($label);?>" width="14"> 
				<?php
				}
				?>
				<?php echo wp_kses_post($label);?>
				</a>
			</td>
		</tr>
		<?php
        }elseif("list_page" === $button_location)
        {
        ?>
			<li>
				<a class="<?php echo esc_attr($confirmation_clss);?>" data-id="<?php echo esc_attr($order_id);?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank"><?php echo wp_kses_post($label);?></a>
			</li>
		<?php
        }
	}
	
	/**
	 * Registers meta box and printing options
	 *
	 * @since 2.5.0
	 * @since 4.1.3 - Add - Debug meta box in order edit page - admin dashboard
	 */
	public function add_meta_boxes()
	{
		$order_details_screen = Wt_Pklist_Common::is_wc_hpos_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box('woocommerce-packinglist-box', __('Invoice/Packing','print-invoices-packing-slip-labels-for-woocommerce'), array($this,'create_metabox_content'),$order_details_screen, 'side', 'default');
		if(isset($_GET['wt-pklist-debug'])){
			add_meta_box('woocommerce-packinglist-box-debug', __('WT PDF Invoice Debug','print-invoices-packing-slip-labels-for-woocommerce'), array($this,'wt_pklist_debug_metabox_content'),$order_details_screen, 'normal', 'default');
		}
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links links array
	 */
	public function plugin_action_links($links) 
	{
	   	$links[] = '<a href="'.admin_url('admin.php?page='.WF_PKLIST_POST_TYPE).'">'.__('Settings', 'print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   	$links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce" target="_blank">'.__('Support','print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   	$links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/?rate=5#new-post" target="_blank">' . __('Review','print-invoices-packing-slip-labels-for-woocommerce') . '</a>';
	   	$links[] = '<a href="https://www.webtoffee.com/woocommerce-pdf-invoices-packing-slips-delivery-notes-shipping-labels-userguide-free-version/" target="_blank">' . __('Documentation','print-invoices-packing-slip-labels-for-woocommerce') . '</a>';
	   	$not_activated_pro_addons = Wf_Woocommerce_Packing_List_Admin::not_activated_pro_addons('wt_qr_addon');
	   	if(!empty($not_activated_pro_addons)){
	   		$pro_addon_arr = array(
		   		'wt_ipc_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('PDF Invoices','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_sdd_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Shipping labels','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_pl_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-picklist/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Picklist&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Pick lists','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_pi_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-proforma-invoice/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Proforma_Invoice&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Proforma invoices','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   		'wt_al_addon' => array(
		   				'utm_link' => 'https://www.webtoffee.com/product/woocommerce-address-label/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=Address_Label&utm_content='.WF_PKLIST_VERSION,
		   				'link_label' => __('Address labels','print-invoices-packing-slip-labels-for-woocommerce'),
		   			),
		   	);
	   		$addon_link = '<br><span style="color:#3db634;">'.__("Premium Extensions","print-invoices-packing-slip-labels-for-woocommerce").': </span>';
		   	for($i = 0; $i < count($not_activated_pro_addons); $i++){
		   		if(isset($pro_addon_arr[$not_activated_pro_addons[$i]])){
		   			$pro_add = $pro_addon_arr[$not_activated_pro_addons[$i]];
		   			$addon_link .= '<a href="'.esc_url($pro_add['utm_link']).'" target="_blank">'.esc_html($pro_add['link_label']).'</a>';
		   			if($i < count($not_activated_pro_addons)-1){
		   				$addon_link .=' | ';
		   			}
		   		}
		   	}
	   		$links[] = $addon_link;
	   	}
	   	return $links;
	}

	/**
	 *	@since  4.0.0  
	 * 	- create content for metabox
	 *	- added separate section for document details and print actions
	 * 
	 */
	public function create_metabox_content($post_or_order_object)
	{
		$order = ( $post_or_order_object instanceof \WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		if ( ! is_object( $order ) && is_numeric( $order ) ) {
			$order = wc_get_order( absint( $order ) );
		}
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		?>
		<table class="wf_invoice_metabox" style="width:100%;">			
			<?php
			$data_arr=array();
			$data_arr=apply_filters('wt_print_docdata_metabox',$data_arr, $order, $order_id);
			if(count($data_arr)>0)
			{
			?>
			<tr>
				<td style="font-weight:bold;">
					<h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Document details','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:10px;">
					<?php
					
					foreach($data_arr as $datav)
					{
						echo '<span style="font-weight:500;">';
						echo ("" !== $datav['label'] ? $datav['label'].': ' : '');
						echo '</span>';
						echo $datav['value'].'<br />';
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td>
					<h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Print/Download','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
				</td>
			</tr>
			<tr>
				<td style="height:3px; font-size:0px; line-height:0px;"></td>
			</tr>
			<?php
			$btn_arr=array();
			$btn_arr=apply_filters('wt_print_actions', $btn_arr, $order, $order_id, 'detail_page');
			self::generate_print_button_html($btn_arr, $order, $order_id, 'detail_page'); //generate buttons
			?>
		</table>
		<?php
	}

	public static function generate_print_button_html($btn_arr, $order, $order_id, $button_location)
	{
		/* filter for customers to alter buttons */
		$btn_arr=apply_filters('wt_pklist_alter_print_actions',$btn_arr, $order, $order_id, $button_location);
		foreach($btn_arr as $btn_key=>$args)
		{
			$action=$args['action'];
			if("print_invoice" === $action || 'print_packinglist' === $action){
				continue;
			}
			$css_class=(isset($args['css_class']) && is_string($args['css_class']) ? $args['css_class'] : ''); /* button custom css */
			$custom_attr=(isset($args['custom_attr']) && is_string($args['custom_attr']) ? $args['custom_attr'] : ''); /* button custom attribute */

			$label=$args['label'];
			$is_show_prompt=$args['is_show_prompt'];
			$tooltip=(isset($args['tooltip']) ? $args['tooltip'] : $label);
			$button_location=(isset($args['button_location']) ? $args['button_location'] : 'detail_page');

			$url=self::get_print_url($order_id, $action);

			$href_attr='';
			$onclick='';
			$confirmation_clss='';
			if(0 !== $is_show_prompt && "0" !== $is_show_prompt) //$is_show_prompt variable is a string then it will set as warning msg title
			{
				$confirmation_clss='wf_pklist_confirm_'.$action;
				$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\',\''.$is_show_prompt.'\');"';
			}else
			{
				$href_attr=' href="'.$url.'"';
			}
			if("detail_page" === $button_location)
	        {
	        	$button_type=(isset($args['button_type']) ? $args['button_type'] : 'normal');
	        	$button_key=(isset($args['button_key']) ? $args['button_key'] : 'button_key_'.$btn_key);
				$doc_exist = (isset($args['exist']) ? $args['exist'] : false);
				$icon_class = false === $doc_exist ? 'wt_doc_not_exist' : '';
	        ?>
				<tr>
					<td class="wt_pklist_dash_btn_row">
						<?php
						if("aggregate" === $button_type || "dropdown" === $button_type)
						{
							if("aggregate" === $button_type) /* reverse the order of buttons */
							{
								$args['items']=array_reverse($args['items']);
							}
							?>
							<div class="wt_pklist_<?php echo $button_type;?> <?php echo $css_class;?>" <?php echo $custom_attr;?> >
								<div class="wt_pklist_btn_text"><?php echo wp_kses_post($label);?></div>
								<div class="wt_pklist_<?php echo $button_type;?>_content">
									<?php
									foreach($args['items'] as $btnkk => $btnvv)
									{
										$action=$btnvv['action'];
										$label=$btnvv['label'];
										
										$icon=(isset($btnvv['icon']) && "" !== $btnvv['icon'] ? $btnvv['icon'] : ''); //dashicon
										$icon_url=(isset($btnvv['icon_url']) && "" !== $btnvv['icon_url'] ? $btnvv['icon_url'] : ''); //image icon

										if("aggregate" === $button_type) /* only icon, No label */
										{
											if("" === $icon && "" === $icon_url)
											{											
												global $wp_version;
												if(version_compare($wp_version, '5.5.3')>=0)
												{
													$fallback_icon='tag';
													if(false !== strpos($action, 'download_'))
													{
														$fallback_icon='download';

													}elseif(false !== strpos($action, 'print_'))
													{
														$fallback_icon='printer';
													}
													$btn_label='<span class="dashicons dashicons-'.$fallback_icon.' '.esc_attr($icon_class).'"></span>';

												}else
												{
													$fallback_icon_url='tag-icon.png';
													if(false !== strpos($action, 'download_'))
													{
														$fallback_icon_url='download-icon.png';

													}elseif(false !== strpos($action, 'print_'))
													{
														$fallback_icon_url='print-icon.png';
													}
													$btn_label='<span class="dashicons" style="line-height:17px;"><img src="'.WF_PKLIST_PLUGIN_URL.'admin/images/'.$fallback_icon_url.'" style="width:16px; height:16px; display:inline;"></span>';
												}
											}else
											{
												if("" !== $icon)
												{
													$btn_label='<span class="dashicons dashicons-'.$icon.' '.esc_attr($icon_class).'"></span>';
												}else
												{
													$btn_label='<span class="dashicons" style="line-height:17px;"><img src="'.esc_url($icon_url).'" style="width:16px; height:16px; display:inline;"></span>';
												}
											}
											if(true === $doc_exist){
												$btn_label .= '<span class="dashicons dashicons-saved wt_pklist_doc_exist"></span>';
											}
										}else
										{
											$btn_label=$label;
										}

										$tooltip=(isset($btnvv['tooltip']) ? $btnvv['tooltip'] : $label);
										$is_show_prompt=$btnvv['is_show_prompt'];
										$item_css_class=(isset($btnvv['css_class']) && is_string($btnvv['css_class']) ? $btnvv['css_class'] : ''); /* dropdown item custom css */
										$item_custom_attr=(isset($btnvv['custom_attr']) && is_string($btnvv['custom_attr']) ? $btnvv['custom_attr'] : ''); /* dropdown item custom attribute */
										
										$url=self::get_print_url($order_id, $action);
										
										$href_attr='';
										$onclick='';
										$confirmation_clss='';
										$print_node_attr = '';
										if(0 !== $is_show_prompt) //$is_show_prompt variable is a string then it will set as warning msg title
										{
											if(strpos($item_css_class, 'wt_pklist_printnode_manual_print') === false){
												$confirmation_clss='wf_pklist_confirm_'.$action;
												$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\',\''.$is_show_prompt.'\');"';
											}
											$print_node_attr = $is_show_prompt;
										}else
										{
											if(false !== strpos($action, 'download_'))
											{
												$item_css_class .=' wt_pklist_admin_download_document_btn';

											}elseif( false !== strpos($action, 'print_') && false === strpos( $action, 'print_ubl' ) ) // UBL document doesnot need this class, as it is handled for print popup screen.
											{
												$item_css_class .=' wt_pklist_admin_print_document_btn';
											}
											$href_attr=' href="'.esc_url($url).'"';
											$print_node_attr = 0;
										}
										?>
										<a <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-id="<?php echo esc_attr($order_id);?>" class="<?php echo esc_attr($item_css_class);?>" <?php echo $item_custom_attr;?> title="<?php echo esc_attr($tooltip);?>" data-prompt="<?php echo esc_attr($print_node_attr); ?>"> <?php echo wp_kses_post($btn_label);?></a>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}else
						{
						?>
							<a class="button tips wf-packing-list-link <?php echo $css_class;?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo esc_attr($tooltip);?>" data-id="<?php echo $order_id;?>" <?php echo $custom_attr;?> >
								<?php echo $label;?>
							</a>
						<?php
						}
						?>
					</td>
				</tr>
			<?php
	        }elseif("list_page" === $button_location)
	        {
				if(false !== strpos($action, 'download_'))
				{
					$css_class .=' wt_pklist_admin_download_document_btn';

				}elseif(false !== strpos($action, 'print_'))
				{
					$css_class .=' wt_pklist_admin_print_document_btn';
				}
	        ?>
				<li>
					<a class="<?php echo esc_attr($confirmation_clss);?> <?php echo esc_attr($css_class);?>" data-id="<?php echo esc_attr($order_id);?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" title="<?php echo esc_attr($tooltip);?>" <?php echo $custom_attr;?> ><?php echo wp_kses_post($label);?></a>
				</li>
			<?php
	        }
	    }
	}

	/**
	 * @since 4.0.0 Removed other solution page, instead created seperate menu for all documents
	 * 
	 */
	public function admin_menu()
	{
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images';
		$menus=array(
			array(
				'menu',
				__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Invoice/Packing','print-invoices-packing-slip-labels-for-woocommerce'),
				'manage_woocommerce',
				WF_PKLIST_POST_TYPE,
				array($this,'admin_settings_page'),
				'dashicons-media-text',
				56,
				'id' => 'main_menu',
			)
		);

		if(0 === absint(get_option('wt_pklist_new_install'))){
			$menus=apply_filters('wt_admin_menu',$menus);
			$menus[]=array(
				'submenu',
				WF_PKLIST_POST_TYPE,
				__('Extensions','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Extensions','print-invoices-packing-slip-labels-for-woocommerce'),
				'manage_woocommerce',
				WF_PKLIST_POST_TYPE.'_premium_extension',
				array($this,'admin_premium_extension_page'),
				'id' => 'premium_extension',
			);
			$menus = apply_filters('wt_pklist_add_menu',$menus);
		}
		
		if(count($menus)>0)
		{
			add_submenu_page(WF_PKLIST_POST_TYPE,__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'), "manage_woocommerce",WF_PKLIST_POST_TYPE,array($this,'admin_settings_page'));
			foreach($menus as $menu)
			{
				if("submenu" === $menu[0])
				{
					add_submenu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6]);
				}else
				{
					add_menu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6],$menu[7]);	
				}
			}
		}

		if(function_exists('remove_submenu_page')){
			//remove_submenu_page(WF_PKLIST_POST_TYPE,WF_PKLIST_POST_TYPE);
		}
	}

	public static function add_menu_after_id($menus,$current_menu,$after_id){
		$pos = 1;
		foreach($menus as $key => $menu){
			if ( isset( $menu['id'] ) && $menu['id'] == $after_id ) {
				break;
			}else{
				$pos++;
			}
		}
		$menus = array_merge( array_slice( $menus, 0, $pos, true ), $current_menu, array_slice( $menus, $pos, NULL, true ) );
		return $menus;
	}

	public static function add_tab_after_id($tab_items,$new_tab_item,$after_id,$template_type,$module_id){
		$pos = 1;
		foreach($tab_items as $key => $tab_item){
			if ( $key == $after_id ) {
				break;
			}else{
				$pos++;
			}
		}
		$tab_items = array_merge( array_slice( $tab_items, 0, $pos, true ), $new_tab_item, array_slice( $tab_items, $pos, NULL, true ) );
		return $tab_items;
	}

	/**
	* @since 2.5.9
	* Is allowed to print
	*/
	public static function check_role_access()
	{
		$admin_print_role_access=array('manage_options', 'manage_woocommerce');
    	$admin_print_role_access=apply_filters('wf_pklist_alter_admin_print_role_access', $admin_print_role_access);  
    	$admin_print_role_access=(!is_array($admin_print_role_access) ? array() : $admin_print_role_access);
    	$is_allowed=false;
    	foreach($admin_print_role_access as $role) //checking access
    	{
    		if(current_user_can($role)) //any of the role is okay then allow to print
    		{
    			$is_allowed=true;
    			break;
    		}
    	}
    	return $is_allowed;
	}

	/**
	 * function to render printing window
	 *
	 * @since 4.2.0 - print the document as per the input from the plugin settings page
	 */
    public function print_window() {
		if ( isset( $_REQUEST['print_packinglist']) ) {
			$document_access_type	= Wf_Woocommerce_Packing_List::get_option('wt_pklist_print_button_access_for');
			$wc_my_account_page 	= get_permalink( get_option('woocommerce_myaccount_page_id') );

			if ( "logged_in" === $document_access_type ) {
				if ( is_user_logged_in() && !isset( $_REQUEST['attaching_pdf'] ) ) {
					// admin link
					$this->print_document_from_the_admin_link();
				} else if ( is_user_logged_in() && isset( $_REQUEST['attaching_pdf'] ) ) {
					// guest link
					$this->print_document_from_the_mail_link();
				} else {
					
					// not allowed for the guest user
					$redirect_url = self::get_page_url_for_denied_document_access();
					self::wt_pklist_safe_redirect_or_die( $redirect_url, __("You are not allowed to access this page","print-invoices-packing-slip-labels-for-woocommerce") );
				}
			} else {
				if ( !isset( $_REQUEST['attaching_pdf'] ) ) {
					if ( is_user_logged_in() ) {
						// admin link
						$this->print_document_from_the_admin_link();
					} else {
						// not allowed guest user for the admin link
						$redirect_url = self::get_page_url_for_denied_document_access();
						self::wt_pklist_safe_redirect_or_die( $redirect_url, __("You are not allowed to access this page","print-invoices-packing-slip-labels-for-woocommerce") );
					}
				} else {
					// allow both user to access the document if it is the link from mail
					$this->print_document_from_the_mail_link();
				}
			}
		}
    }

	/**
	 * Handles the admin side document link
	 * @since 4.2.0
	 * @return void
	 */
	public function print_document_from_the_admin_link() { 
		$nonce	= isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : ''; 
		$orders	= array();
		$not_allowed_msg=__('You are not allowed to view this page.','print-invoices-packing-slip-labels-for-woocommerce');
		$not_allowed_title=__('Access denied !!!.','print-invoices-packing-slip-labels-for-woocommerce');

		if( !( wp_verify_nonce( $nonce, WF_PKLIST_PLUGIN_NAME ) ) )
		{
			self::wt_pklist_safe_redirect_or_die( null, $not_allowed_msg );	
		} else
		{
			$user_access	= apply_filters('wt_pklist_document_access_for_user_role',true);
			if ( !$user_access ) {
				self::wt_pklist_safe_redirect_or_die( null, $not_allowed_title);	
			}
			$orders = explode(',', sanitize_text_field($_REQUEST['post']));
		}
		
		// when doing the bulk print without permission
		if( count( $orders ) > 1 ) {
			if( !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) //Check access
			{
				self::wt_pklist_safe_redirect_or_die( '', __("You are not allowed to do this action","print-invoices-packing-slip-labels-for-woocommerce") );
			}
		}
		
		if (1 === count( $orders ) ) {
			$order = ( WC()->version < '2.7.0' ? new WC_Order( $orders[0] ) : new wf_order( $orders[0] ) );
			if ( empty( $order ) ) {
				// order is empty.
				self::wt_pklist_safe_redirect_or_die( null, __( 'There is no order with this id', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
			} else {
				if ( !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) {
					// order is not empty then check the user of the order.
					$order_user_id	= $order->get_user_id();
					$user_id 		= get_current_user_id();
					if( $user_id !== $order_user_id ) {
						// check if the order is belonging to this user by checking the product author.
						$i = 0;
						$order_items = $order->get_items();
						if( !empty( $order_items ) ) {
							foreach ($order_items as $item_id => $item) {
								if( method_exists( $item, 'get_product')  ) {
									$product = $item->get_product();
									if ( $product ) {
										// check if the product is belonging to the current user
										$product_id	= $product->get_id();
										$vendor_id	= (int) get_post_field('post_author', $product_id);
										if($user_id !== $vendor_id){
											// the product is not belonging to this user.
											self::wt_pklist_safe_redirect_or_die( null, __( 'It seems this order is not yours', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
											break;
										}
									} else {
										// increase the deleted product count.
										$i++;
									}
								} else {
									// increase the deleted product count.
									$i++;
								}
							}

							if( $i === count( $order_items ) ) {
								// all the items in the order were deleted.
								self::wt_pklist_safe_redirect_or_die( null, __( 'It seems the products in this order were removed, we cannot proceed this actoin now.', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
							}
						} else {
							// Order does not have the items.
							self::wt_pklist_safe_redirect_or_die( null, __( 'There is no items in this order.', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
						}
					} else if ( 0 === $user_id) { // Guest user is not allowed to print/download through the admin link
						self::wt_pklist_safe_redirect_or_die( null, __( 'You are not allowed to do this action', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
					}
				}
			}

			if( isset( $order ) && !empty( $order ) ) {
				// clear the variable to save the memory
				unset($order);
			}
		}

		if( !empty( $orders ) && is_array( $orders ) ){
			$orders=array_values(array_filter($orders));
			$orders=$this->verify_order_ids($orders);
			if(count($orders)>0)
			{
				remove_action('wp_footer', 'wp_admin_bar_render', 1000);
				$action = (isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '');

				// Set the option to show the banner after the bulk print
				$document_actions_for_banner = array(
					'print_invoice',
					'download_invoice',
					'print_packinglist',
					'download_packinglist',
				);

				if ( in_array( $action, $document_actions_for_banner ) && false === get_option( 'wt_pklist_banner_after_bulk_print_ipc' ) ) {
					update_option( 'wt_pklist_banner_after_bulk_print_ipc', 1 );
					do_action( 'wt_pklist_show_banner_after_bulk_print_ipc' );
				}

				// Removes the WooCommerce filter, that is validating the quantity to be an int
				remove_filter('woocommerce_stock_amount', 'intval');

				// Add a filter, that validates the quantity to be a float
				add_filter('woocommerce_stock_amount', 'floatval');
				
				//action for modules to hook print function
				do_action('wt_print_doc', $orders, $action);

				// remove the filter from rendering html
				remove_filter('woocommerce_stock_amount', 'floatval');
				add_filter('woocommerce_stock_amount', 'intval');
			}
		}
		exit();
	}

	/**
	 * Handles link from mail
	 * @since 4.2.0
	 * @return void
	 */
	public function print_document_from_the_mail_link() {
		$access_key 		= isset( $_REQUEST['access_key'] ) ? sanitize_text_field( $_REQUEST['access_key'] ) : '';
		$encoded_order_id	= isset( $_REQUEST['post'] ) ? sanitize_text_field( $_REQUEST['post'] ) : '';
		$mail_id			= isset( $_REQUEST['email'] ) ? sanitize_text_field( $_REQUEST['email'] ) : '';

		if ( empty( $encoded_order_id ) ) {
			self::wt_pklist_safe_redirect_or_die( null, __("Invalid order id","print-invoices-packing-slip-labels-for-woocommerce") );	
		}

		if ( empty( $access_key ) ) {
			if( empty( $mail_id ) ) {
				self::wt_pklist_safe_redirect_or_die( null, __("Invalid access key and mail id","print-invoices-packing-slip-labels-for-woocommerce") );	
			}
		}

		$decoded_mail_id 	= Wf_Woocommerce_Packing_List::wf_decode( sanitize_text_field( $mail_id ) );
		$decoded_order_id 	= Wf_Woocommerce_Packing_List::wf_decode( sanitize_text_field( $encoded_order_id ) );
		$order 				= wc_get_order($decoded_order_id);
		$orders				= array();
		$empty_order		= __("Empty order","print-invoices-packing-slip-labels-for-woocommerce");
		$not_allowed_msg	= __('You are not allowed to view this page.','print-invoices-packing-slip-labels-for-woocommerce');

		if( !empty( $order ) ){
			if ( hash_equals( $order->get_order_key(), $access_key ) ) {
				$orders	= explode(",",$decoded_order_id);
			} else {
				if ( $decoded_mail_id === ( ( WC()->version < '2.7.0' ) ? $order->billing_email : $order->get_billing_email() ) ) {
					$orders	= explode( ",", $decoded_order_id );
				} else {
					self::wt_pklist_safe_redirect_or_die( null, __( 'It seems this order is not yours.', 'print-invoices-packing-slip-labels-for-woocommerce' ) );	
				}
			}
		} else {
			self::wt_pklist_safe_redirect_or_die( null, $empty_order );	
		}
		
		if( count( $orders ) > 1 ) {
			if( !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) //Check access
			{
				self::wt_pklist_safe_redirect_or_die( '', __("You are not allowed to do this action","print-invoices-packing-slip-labels-for-woocommerce") );
			}
		}

		if (1 === count( $orders ) ) {
			$order = ( WC()->version < '2.7.0' ? new WC_Order( $orders[0] ) : new wf_order( $orders[0] ) );
			if ( empty( $order ) ) {
				// order is empty.
				self::wt_pklist_safe_redirect_or_die( null, __( 'There is no order with this id', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
			} else { 
				if( !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) { //Check access
					// order is not empty then check the user of the order.
					$order_user_id	= (int)$order->get_user_id();
					$user_id 		= (int)get_current_user_id();
					if( ( 0 !== $user_id ) && ( $user_id !== $order_user_id ) ) {
						// check if the order is belonging to this user by checking the product author.
						$i = 0;
						$order_items = $order->get_items();
						if( !empty( $order_items ) ) {
							foreach ($order_items as $item_id => $item) {
								if( method_exists( $item, 'get_product')  ) {
									$product = $item->get_product();
									if ( $product ) {
										// check if the product is belonging to the current user
										$product_id	= $product->get_id();
										$vendor_id	= (int) get_post_field('post_author', $product_id);
										if($user_id !== $vendor_id){
											// the product is not belonging to this user.
											self::wt_pklist_safe_redirect_or_die( null, __( 'It seems this order is not yours', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
											break;
										}
									} else {
										// increase the deleted product count.
										$i++;
									}
								} else {
									// increase the deleted product count.
									$i++;
								}
							}

							if( $i === count( $order_items ) ) {
								// all the items in the order were deleted.
								self::wt_pklist_safe_redirect_or_die( null, __( 'It seems the products in this order were removed, we cannot proceed this actoin now.', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
							}
						} else {
							// Order does not have the items.
							self::wt_pklist_safe_redirect_or_die( null, __( 'There is no items in this order.', 'print-invoices-packing-slip-labels-for-woocommerce' ) );
						}
					}
				}
			}
			if( isset( $order ) && !empty( $order ) ) {
				// clear the variable to save the memory
				unset($order);
			}
		}

		if( !empty( $orders ) && is_array( $orders ) ){
			$orders=array_values(array_filter($orders));
			$orders=$this->verify_order_ids($orders);
			if(count($orders)>0)
			{
				remove_action('wp_footer', 'wp_admin_bar_render', 1000);
				$action = (isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '');
				
				// Removes the WooCommerce filter, that is validating the quantity to be an int
				remove_filter('woocommerce_stock_amount', 'intval');

				// Add a filter, that validates the quantity to be a float
				add_filter('woocommerce_stock_amount', 'floatval');
				
				//action for modules to hook print function
				do_action('wt_print_doc', $orders, $action);

				// remove the filter from rendering html
				remove_filter('woocommerce_stock_amount', 'floatval');
				add_filter('woocommerce_stock_amount', 'intval');

			}
		}
		exit();
	}

	/**
	* Check for valid order ids
	* @since 2.5.4
	* @since 2.5.7 Added compatiblity for `Sequential Order Numbers for WooCommerce`
	*/
    public static function verify_order_ids($order_ids)
    {
    	$out=array();
    	foreach ($order_ids as $order_id)
    	{
    		if(false === wc_get_order($order_id))
    		{
    			/* compatibility for sequential order number */
    			$order_data=wc_get_orders(
    				array(
    					'limit' => 1,
    					'return' => 'ids',
    					'meta_query'=>array(
    						'key'=>'_order_number',
    						'value'=>$order_id,
    					)
    			));
    			if(false !== $order_data && is_array($order_data) && 1 === count($order_data))
    			{
    				$order_id=(int) $order_data[0];
    				if($order_id>0 && false !== wc_get_order($order_id))
    				{
    					$out[]=$order_id;
    				}
    			}
    		}else
    		{
    			$out[]=$order_id;
    		}
    	}
    	return $out;
    }

    public function load_address_from_woo()
    {
    	if(!self::check_write_access()) 
		{
			exit();
		}
    	$out=array(
    		'status'=>1,
    		'address_line1'=>get_option('woocommerce_store_address'),
    		'address_line2'=>get_option('woocommerce_store_address_2'),
    		'city'=>get_option('woocommerce_store_city'),
    		'country'=>get_option('woocommerce_default_country'),
    		'postalcode'=>get_option('woocommerce_store_postcode'),
    	);
    	echo json_encode($out);
    	exit();
    }

	private function dismiss_notice()
	{
		$allowd_items=array();
		if(isset($_GET['wf_pklist_notice_dismiss']) && "" !== trim($_GET['wf_pklist_notice_dismiss']))
		{
			if(in_array(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),$allowd_items))
			{
				update_option(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),1);
			}
		}
	}

	/**
	 * WebToffee extension page
	 * @since 4.0.8
	 */
	public function admin_premium_extension_page()
	{
		wp_enqueue_style( 'woocommerce_admin_styles' );
		include_once WF_PKLIST_PLUGIN_PATH.'admin/views/premium_extension_page.php';
	}

	/**
	 * Admin settings page
	 *
	 * @since    2.5.0
	 * 
	 * @since 4.2.0 - [Tweak] - Update the new install flag when skip or complete the wizard
	 */
	public function admin_settings_page()
	{
		if(isset($_GET['skip_wizard']) && 1 === absint($_GET['skip_wizard'])){
			update_option('wt_pklist_new_install',0);
		}

		if(isset($_GET['complete_wizard']) && 1 === absint($_GET['complete_wizard'])){
			update_option('wt_pklist_new_install',0);
		}

		//dismiss the notice if exists
		$this->dismiss_notice();

		$the_options=Wf_Woocommerce_Packing_List::get_settings();
		$no_image=Wf_Woocommerce_Packing_List::$no_image;
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus');
		
		/**
		*	@since 2.6.6
		*	Get available PDF libraries
		*/
		$pdf_libs=Wf_Woocommerce_Packing_List::get_pdf_libraries();

		wp_enqueue_media();
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');

		/* enable/disable modules */
		if(isset($_POST['wf_update_module_status']))
		{
			// Check nonce:
	        if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    		{
    			exit();
    		}

		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if(false === $wt_pklist_common_modules)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		if( 0 === absint( get_option('wt_pklist_new_install') ) ){
			include WF_PKLIST_PLUGIN_PATH.'admin/partials/admin-settings.php';
		} else {
			include WF_PKLIST_PLUGIN_PATH.'admin/partials/form_wizard/form_wizard.php';
		}
	}

	/**
	* @since 2.6.2
	* Is user allowed 
	*/
	public static function check_write_access($nonce_id='')
	{
		$er=true;
		//checkes user is logged in
    	if(!is_user_logged_in())
    	{
    		$er=false;
    	}

    	if(true === $er) //no error then proceed
    	{
    		$nonce=sanitize_text_field($_REQUEST['_wpnonce']);
    		$nonce=(is_array($nonce) ? $nonce[0] : $nonce);
    		$nonce_id=("" === $nonce_id ? WF_PKLIST_PLUGIN_NAME : $nonce_id);
    		if(!(wp_verify_nonce($nonce, $nonce_id)))
	        {
	            $er=false;
	        }else
	        {
	        	if(!Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	            {
	            	$er=false;
	            }
	        }
    	}
    	return $er;
	}

	/**
	* Form action for debug settings tab
	* @since 2.6.7
	*/
	public function debug_save()
	{	
		if(isset($_POST['wt_pklist_admin_modules_btn']))
		{
		    if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
	    	{
	    		return;
	    	}
	        
		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if(false === $wt_pklist_common_modules)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }

		    $wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		    if(false === $wt_pklist_admin_modules)
		    {
		        $wt_pklist_admin_modules=array();
		    }
		    if(isset($_POST['wt_pklist_admin_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_admin_modules']);
		        foreach($wt_pklist_admin_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && (1 === $wt_pklist_post[$k] || "1" === $wt_pklist_post[$k]))
		            {
		                $wt_pklist_admin_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_admin_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_admin_modules as $k=>$v)
		        {
					$wt_pklist_admin_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_admin_modules',$wt_pklist_admin_modules);
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		if(Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	    {
			//module debug settings saving hook
	    	do_action('wt_pklist_module_save_debug_settings');
	    }
	}

	/**
	*	@since 2.6.2 
	* 	Validate array data
	*/
	public static function sanitize_text_arr($arr, $type='text')
	{
		if(is_array($arr))
		{
			$out=array();
			foreach($arr as $k=>$arrv)
			{
				if(is_array($arrv))
				{
					$out[$k]=self::sanitize_text_arr($arrv, $type);
				}else
				{
					if("int" === $type)
					{
						$out[$k]=intval($arrv);
					}else
					{
						$out[$k]=sanitize_text_field($arrv);
					}
				}
			}
			return $out;
		}else
		{
			if("int" === $type)
			{
				return intval($arr);
			}else
			{
				return sanitize_text_field($arr);
			}
		}
	}

	/**
	*	@since 2.6.2 
	* 	Settings validation function for modules and plugin settings
	*/
	public function validate_settings_data($val, $key, $validation_rule=array())
	{		
		if(isset($validation_rule[$key]) && is_array($validation_rule[$key])) /* rule declared/exists */
		{
			if(isset($validation_rule[$key]['type']))
			{
				if("text" === $validation_rule[$key]['type'])
				{
					$val=sanitize_text_field($val);
				}elseif("text_arr" === $validation_rule[$key]['type'])
				{
					$val=self::sanitize_text_arr($val);
				}elseif("int" === $validation_rule[$key]['type'])
				{
					$val=intval($val);
				}elseif("float" === $validation_rule[$key]['type'])
				{
					$val=floatval($val);
				}elseif("int_arr" === $validation_rule[$key]['type'])
				{
					$val=self::sanitize_text_arr($val, 'int');
				}
				elseif("textarea" === $validation_rule[$key]['type'])
				{
					$val=sanitize_textarea_field($val);
				}else
				{
					$val=sanitize_text_field($val);
				}
			}
		}else
		{
			$val=sanitize_text_field($val);
		}
		return $val;
	}

	public function validate_box_packing_field($value)
	{           
        $new_boxes = array();
        foreach ($value as $key => $value)
        {
            if ($value['length'] != '') {
                $value['enabled'] = isset($value['enabled']) ? true : false;
                $new_boxes[] = $value;
            }
        }
        return $new_boxes;
    }

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if("" !== $view_file && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Envelope settings subtab content with subtab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_subtabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf_sub_tab_content" data-id="<?php echo $target_id;?>">
			<?php
			if("" !== $view_file && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if(1 === $need_submit_btn || "1" === $need_submit_btn)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Registers modules: public+admin	 
	 */
	public function admin_modules()
	{ 	
		$admin_module_save = 0;
		$wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		if(false === $wt_pklist_admin_modules || !is_array( $wt_pklist_admin_modules) )
		{
			$wt_pklist_admin_modules=array();
			$admin_module_save = 1;
		}elseif(empty($wt_pklist_admin_modules)){
			$admin_module_save = 1;
		}

		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_pklist_admin_modules[$module]))
			{
				$is_active=$wt_pklist_admin_modules[$module]; //checking module status
			}else
			{
				$wt_pklist_admin_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";	
			if("customizer" === $module){
				$cus_file_name = "basic_customizer.php";
				$module_file=plugin_dir_path( __FILE__ )."modules/$module/".$cus_file_name;	
			}
			
			if(file_exists($module_file) && (1 === $is_active || "1" === $is_active))
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_pklist_admin_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_pklist_admin_modules as $k=>$m)
		{
			if(in_array($k,self::$modules))
			{
				$out[$k]=$m;
			}
		}

		if(1 === $admin_module_save || "1" === $admin_module_save){
			update_option('wt_pklist_admin_modules',$out);
		}
	}

	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}

	/**
	*	@since 2.6.2
	* 	Save admin settings and module settings ajax hook
	*/
	public function save_settings()
	{
		$base		= ( isset( $_POST['wf_settings_base'] ) ? sanitize_text_field( $_POST['wf_settings_base'] ) : 'main' );
		$base_id	= ( "main" === $base ? '' : Wf_Woocommerce_Packing_List::get_module_id( $base ) );
		$tab_name 	= ( isset( $_POST['wt_tab_name'] ) ? sanitize_text_field( $_POST['wt_tab_name'] ) : "" );
		$out		= array(
			'status'	=> false,
			'msg'		=> __('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
		);

		if( Wf_Woocommerce_Packing_List_Admin::check_write_access() ) {

    		$the_options				= Wf_Woocommerce_Packing_List::get_settings( $base_id );
    		$single_checkbox_fields		= Wf_Woocommerce_Packing_List::get_single_checkbox_fields( $base_id, $tab_name );
    		$multi_checkbox_fields		= Wf_Woocommerce_Packing_List::get_multi_checkbox_fields( $base_id, $tab_name );
	        
			//multi select form fields array. (It will not return a $_POST val if it's value is empty so we need to set default value).
			$default_val_needed_fields	= apply_filters( 'wt_pklist_intl_alter_multi_select_fields', array(), $base_id ); // this is an internal filter.    
			
			$validation_rule			= array(				
				'woocommerce_wf_packinglist_boxes'			=> array( 'type'	=> 'text_arr' ),
				'woocommerce_wf_packinglist_footer'			=> array( 'type'	=> 'textarea' ),
				'woocommerce_wf_generate_for_taxstatus'		=> array( 'type'	=> 'text_arr' ),
				'wf_woocommerce_invoice_show_print_button'	=> array( 'type'	=> 'text_arr' ),
				'wt_pklist_auto_temp_clear_interval'		=> array( 'type'	=> 'int' ),
				'wt_pklist_separate_print_button_enable'	=> array( 'type'	=> 'text_arr' ),
		    ); //this is for plugin settings default. Modules can alter
	        $validation_rule			= apply_filters( 'wt_pklist_intl_alter_validation_rule', $validation_rule, $base_id );
	       	
			$run_empty_count 			= false;
	        //invoice number empty count trigger when changing the order status in invoice settings page
	        if ( isset( $_POST['woocommerce_wf_generate_for_orderstatus'] ) ) {
	        	if ( is_array( $the_options['woocommerce_wf_generate_for_orderstatus'] ) && is_array( $_POST['woocommerce_wf_generate_for_orderstatus'] ) ) {
	        		$find_diff = array_merge( array_diff( $the_options['woocommerce_wf_generate_for_orderstatus'], $_POST['woocommerce_wf_generate_for_orderstatus'] ), array_diff( $_POST['woocommerce_wf_generate_for_orderstatus'], $the_options['woocommerce_wf_generate_for_orderstatus'] ) );
		        	if ( !empty( $find_diff ) ) {
		        		$run_empty_count = true;
		        	}
	        	}
	        }

	        // invoice number empty count trigger when enable or disable the old orders
	        if ( isset( $the_options['wf_woocommerce_invoice_prev_install_orders'] ) ) {
	        	$prev_val	= isset($_POST['wf_woocommerce_invoice_prev_install_orders']) ? sanitize_text_field($_POST['wf_woocommerce_invoice_prev_install_orders']) : "";
	        	if ( ( "" !== $prev_val ) && ( $prev_val !== $the_options['wf_woocommerce_invoice_prev_install_orders'] ) ) {
	        		$run_empty_count = true;
		        } elseif ( ( "" === $prev_val ) && ( "No" !== $the_options['wf_woocommerce_invoice_prev_install_orders'] ) ) {
	        		$run_empty_count = true;
		        }
	        }

			/**
			 * @since 4.2.0
			 * To avoid the key conflict with new UI of Invoice number settings in invoice, credit note and proforma invoice number
			 * The new keys are appended with keyword `_pdf_fw`
			 */
			$invoice_number_keys	= array(
				"woocommerce_wf_invoice_number_format",
				"woocommerce_wf_Current_Invoice_number",
				"woocommerce_wf_invoice_start_number",
				"woocommerce_wf_invoice_number_prefix",
				"woocommerce_wf_invoice_padding_number",
				"woocommerce_wf_invoice_number_postfix",
				"woocommerce_wf_invoice_as_ordernumber",
			);

	        foreach ( $the_options as $key => $value ) {
				if ( in_array( $key, $invoice_number_keys ) ) {
					$modified_key	= $key.'_pdf_fw';
					$post_key		= isset( $_POST[$modified_key] ) ? $modified_key :  $key;
				} else {
					$post_key 		= $key;
				}

	            if ( isset( $_POST[$post_key] ) ) {
	            	$the_options[$key]		= $this->validate_settings_data( $_POST[$post_key], $key, $validation_rule );
	            	
					if( "woocommerce_wf_packinglist_boxes" === $key ) {
	            		$the_options[$key]	= $this->validate_box_packing_field( $_POST[$key] );
	            	}

					if ( "wt_pklist_auto_temp_clear_interval" === $key && "" === trim( $_POST[$key] ) ) {
						$the_options[$key] 	= 0;
					}

	            	if ( isset( $multi_checkbox_fields[$key] ) ) {
	            		$the_options[$key] 	= apply_filters( 'wf_module_save_multi_checkbox_fields', $the_options[$key], $key, $multi_checkbox_fields, $base_id );
	            	}
	            } elseif ( array_key_exists( $key, $single_checkbox_fields ) ) {
	            	if( !isset( $_POST['update_sequential_number'] ) ) { //since the settings of the invoice are divided into 2
	            		$the_options[$key] 	= $single_checkbox_fields[$key]; //if unchecked,PHP will not send the values, so get the unchecked value from the respective modules
	            	}
	            } elseif ( array_key_exists( $key, $multi_checkbox_fields ) ) {
		            $the_options[$key] 		= $multi_checkbox_fields[$key];
	            } else {
	            	if ( array_key_exists( $key, $default_val_needed_fields ) ) {
	            		/* Set a hidden field for every multi-select field in the form. This will be used to populate the multi-select field with an empty array when it does not have any value. */
	            		if ( isset( $_POST[$key.'_hidden'] ) )
	            		{
	            			$the_options[$key]	= $default_val_needed_fields[$key];
	            		}
	            	}
	            }
	        }

	        Wf_Woocommerce_Packing_List::update_settings($the_options, $base_id);
	        do_action('wf_pklist_intl_after_setting_update', $the_options, $base_id);

	        if ( true === $run_empty_count ) {
	        	$this->wt_get_empty_invoice_number_count();
	        }

	        $out['status']		= true;
	        $out['msg']			= __('Settings Updated', 'print-invoices-packing-slip-labels-for-woocommerce');
			$out['saved_data'] 	= $this->get_wt_pklist_plugin_data( true );
    	}
		echo json_encode( $out );
		exit();
	}

	public static function strip_unwanted_tags($html)
	{
		$html	= html_entity_decode(stripcslashes($html));
		$html 	= preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
		$html 	= preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
		$html 	= preg_replace('#<audio(.*?)>(.*?)</audio>#is', '', $html);
		$html 	= preg_replace('#<video(.*?)>(.*?)</video>#is', '', $html);
		return $html;
	}

	/**
	*	@since 2.6.6
	* 	List of all languages with locale name and native name
	*  	@since 4.0.8 - Add all the languages to option table to avoid the memory exhausted error
	* 	@return array An associative array of languages.
	*/
	public static function get_language_list()
	{
		if(false === get_option('wt_pklist_languages_list') || empty(get_option('wt_pklist_languages_list'))){
			update_option('wt_pklist_languages_list',self::all_wt_pklist_languages());
		}

		/**
		*	Alter language list.
		*	@param array An associative array of languages.
		*/
		$wt_pklist_language_list=apply_filters('wt_pklist_alter_language_list', get_option('wt_pklist_languages_list',array()));

		return $wt_pklist_language_list;
	}

	/**
	*	@since 2.6.6 Get list of RTL languages
	*	@return array an associative array of RTL languages with locale name, native name, locale code, WP locale code
	*/
	public static function get_rtl_languages()
	{
		$rtl_lang_keys=array('ar', 'dv', 'he_IL', 'ps', 'fa_IR', 'ur');

		/**
		*	Alter RTL language list.
		*	@param array RTL language locale codes (WP specific locale codes)
		*/
		$rtl_lang_keys=apply_filters('wt_pklist_alter_rtl_language_list', $rtl_lang_keys);

		$lang_list=self::get_language_list(); //taking full language list		
		
		$rtl_lang_keys=array_flip($rtl_lang_keys);
		return array_intersect_key($lang_list, $rtl_lang_keys);
	}

	/**
	*	@since 2.6.6 Checks user enabled RTL and current language needs RTL support.
	*	@return boolean
	*/
	public static function is_enable_rtl_support()
	{
		if(!is_null(self::$is_enable_rtl)) /* already checked then return the stored result */
		{
			return self::$is_enable_rtl;
		}
		$rtl_languages=self::get_rtl_languages();
		$current_lang=get_locale();
		
		self::$is_enable_rtl=isset($rtl_languages[$current_lang]); 
		return self::$is_enable_rtl;
	}

	/**
    * @since 2.7.8
    * Compatible with multi currency and currency switcher plugin
    * 2.7.9 - bug fix - compatible with WC version below 4.1.0
    */
    public static function wf_display_price( $user_currency, $order, $price, $from="" ) {
    	$order_id			= WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$price 				= (float)$price;
		$negative_price 	= ( 0 > $price ) ? true : false;
		$price 				= abs( (float)$price );
		$symbols			= ( '4.1.0' > WC()->version ) ? self::wf_get_woocommerce_currency_symbols() : get_woocommerce_currency_symbols();
		$currency_pos		= get_option('woocommerce_currency_pos') ? get_option('woocommerce_currency_pos') : 'left';
    	$wc_currency_symbol = isset( $symbols[ $user_currency ] ) ? $symbols[ $user_currency ] : '';
		$wc_currency_symbol = apply_filters( 'woocommerce_currency_symbol', $wc_currency_symbol, $user_currency );
		$decimal			= get_option('woocommerce_price_num_decimals') ? wc_get_price_decimals() : 0;
    	$decimal_sep		= get_option('woocommerce_price_decimal_sep') ? wc_get_price_decimal_separator() : '.';
		$thousand_sep		= get_option('woocommerce_price_thousand_sep') ? wc_get_price_thousand_separator() : ',';

    	if ( is_plugin_active( 'woocommerce-currency-switcher/index.php' ) && class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$multi_currencies		= $WOOCS->get_currencies();
			$user_selected_currency = $multi_currencies[$user_currency];
			if ( !empty( $user_selected_currency ) ) {
				$currency_pos		= isset( $user_selected_currency["position"] ) ? $user_selected_currency["position"] : $currency_pos;
				$decimal 			= isset( $user_selected_currency["decimals"] ) ? $user_selected_currency["decimals"] : $decimal;
				$wc_currency_symbol = isset( $user_selected_currency["symbol"] ) ? $user_selected_currency["symbol"] : $$wc_currency_symbol;
			}
		} elseif ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) ) {
			$wmc_order_info 		= Wt_Pklist_Common::get_order_meta($order_id,'wmc_order_info',true);
			
			if ( !empty( $wmc_order_info ) && is_array( $wmc_order_info ) && isset( $wmc_order_info[$user_currency] ) ) {
				$currency_pos 		= isset( $wmc_order_info[$user_currency]['pos'] ) ? isset( $wmc_order_info[$user_currency]['pos'] ) : $currency_pos;
				$decimal 			= isset( $wmc_order_info[$user_currency]['decimals'] ) ? $wmc_order_info[$user_currency]['decimals'] : $decimal;
			}
		}

		$decimal			= ( "" === trim( $decimal ) ) ? 0 : $decimal;
		$decimal_sep 		= ( "" === trim( $decimal_sep ) ) ? "." : $decimal_sep;
		$thousand_sep		= ( "" === trim( $thousand_sep ) ) ? ',' : $thousand_sep;

		$wc_currency_symbol = apply_filters( 'wt_pklist_alter_currency_symbol', $wc_currency_symbol, $symbols, $user_currency, $order, $price );

		$currency_pos 		= apply_filters( 'wt_pklist_alter_currency_symbol_position', $currency_pos, $symbols, $wc_currency_symbol, $user_currency, $order, $price );
		$decimal 			= apply_filters( 'wt_pklist_alter_currency_decimal', $decimal, $wc_currency_symbol, $user_currency, $order, $price );
		$decimal_sep 		= apply_filters( 'wt_pklist_alter_currency_decimal_seperator', $decimal_sep, $symbols, $wc_currency_symbol, $user_currency, $order, $price );
    	$thousand_sep 		= apply_filters( 'wt_pklist_alter_currency_thousand_seperator', $thousand_sep, $symbols, $wc_currency_symbol, $user_currency, $order, $price );
    	$wf_formatted_price = number_format( $price, $decimal, $decimal_sep, $thousand_sep );

    	if( "qrcode" === $from ) {
			return wp_kses_post( $wf_formatted_price.' '.$user_currency );
		}

		if("" !== trim( $wc_currency_symbol ) ) {
			switch ( $currency_pos ) {
				case 'left':
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
				case 'right':
					$result = $wf_formatted_price.$wc_currency_symbol;
					break;
				case 'left_space':
					$result = $wc_currency_symbol.' '.$wf_formatted_price;
					break;
				case 'right_space':
					$result = $wf_formatted_price.' '.$wc_currency_symbol;
					break;
				default:
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
			}
		} else {
			$result = $wf_formatted_price.' '.$user_currency;
		}

		$result = (true === $negative_price) ? '-'. $result : $result;
		$result = apply_filters( 'wt_pklist_change_currency_format', $result, $symbols, $wc_currency_symbol, $currency_pos, $decimal, $decimal_sep, $thousand_sep, $user_currency, $price, $order );
		return "<span>".wp_kses_post( $result )."</span>";	
    }

    public static function wf_get_decimal_price($user_currency,$order){
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	if(true === get_option('woocommerce_price_num_decimals')){
    		$decimal = wc_get_price_decimals();
    	}else{
    		$decimal = 0;
    	}

    	if(is_plugin_active('woocommerce-currency-switcher/index.php'))
		{
			if(class_exists('WOOCS')){
				global $WOOCS;
				$multi_currencies = $WOOCS->get_currencies();
				$user_selected_currency = $multi_currencies[$user_currency];
				if(!empty($user_selected_currency)){
					if(array_key_exists('decimals', $user_selected_currency))
					{
						$decimal = $user_selected_currency["decimals"];
					}
				}
			}
		}elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php'))
		{
			$wmc_order_info = Wt_Pklist_Common::get_order_meta($order_id,'wmc_order_info',true);
			if(!empty($wmc_order_info) && is_array($wmc_order_info) && isset($wmc_order_info[$user_currency])){
				$decimal	= isset($wmc_order_info[$user_currency]['decimals']) ? $wmc_order_info[$user_currency]['decimals'] : $decimal;
			}
		}

		if("" === trim($decimal)){
			$decimal = 0;
		}
		return $decimal;
    }
    /**
    * @since 2.7.8
    * Convert the price with multi currency and currency switcher plugin
    */
    public static function wf_convert_to_user_currency($item_price,$user_currency,$order){

    	$rate = 1;
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$item_price = (float)$item_price;

		$woocs_rate 	= Wt_Pklist_Common::get_order_meta( $order_id, '_woocs_order_rate', true );
		$wmc_order_info = Wt_Pklist_Common::get_order_meta($order_id,'wmc_order_info',true);
    	/* currency switcher - packinglist product table */
    	if(is_plugin_active('woocommerce-currency-switcher/index.php')) 
		{
			if(!empty($woocs_rate)){
				$rate = $woocs_rate;
			}elseif(!empty($wmc_order_info) && is_array($wmc_order_info) && isset($wmc_order_info[$user_currency]['rate'])){
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php')) /* Multi currency - packinglist product table */
		{
			if(!empty($wmc_order_info) && is_array($wmc_order_info) && isset($wmc_order_info[$user_currency]['rate'])){
				$rate = $wmc_order_info[$user_currency]['rate'];
			}elseif(!empty($woocs_rate)){
				$rate = $woocs_rate;
			}
		}
		else
		{
			/* currency switcher / multicurrency even plugins are not available - packinglist product table */
			if(!empty($woocs_rate)){
				$rate = $woocs_rate;
			}elseif(!empty($wmc_order_info) && is_array($wmc_order_info) && isset($wmc_order_info[$user_currency]['rate'])){
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		return $item_price * (float)$rate;
    }

    /**
    * @since 2.7.9
    * Get the currecy symbols array for the WC < 4.1.0
    */
    public static function wf_get_woocommerce_currency_symbols(){
    	$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => '&#8376;',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#1088;&#1089;&#1076;',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		return $symbols;
    }

    /**
    * @since 2.8.0
    * Shipping address with order currency symbol
    */
	public static function wf_shipping_formated_price($order){
		$order_id=(WC()->version<'2.7.0' ? $order->id : $order->get_id());
		$user_currency = Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
		$tax_display = get_option( 'woocommerce_tax_display_cart' );

		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);

		if ( 0 < abs( (float) $order->get_shipping_total() ) ) {
			if(!$incl_tax){
				// Show shipping excluding tax.
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$order->get_shipping_total());
				if ( (float) $order->get_shipping_tax() > 0 && $order->get_prices_include_tax() ) {
					$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>', $order, $tax_display );
				}
			} else {
				// Show shipping including tax.
				$tot_shipping_amount = $order->get_shipping_total() + $order->get_shipping_tax();
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$tot_shipping_amount);
				if ( (float) $order->get_shipping_tax() > 0 && ! $order->get_prices_include_tax() ) {
				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>', $order, $tax_display );
				}
			}
			/* translators: %s: method */
			$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );

		} elseif ( $order->get_shipping_method() ) {
			$shipping = $order->get_shipping_method();
		} else {
			$shipping = __( 'Free!', 'woocommerce' );
		}
		return $shipping;
	}

    /**
    * @since 2.8.0
    * Generate PDF file name for invoice template
    */

    public static function get_invoice_pdf_name($template_type,$order_ids,$module_id){

		$order = wc_get_order( $order_ids[0] );

		Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,true,'set');
		
		$wf_invoice_pdf_name_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name', $module_id);
		$wf_invoice_pdf_name_prefix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name_prefix', $module_id);

		if("[prefix][order_no]" === $wf_invoice_pdf_name_format){
			$invoice_pdf_name_number_pos = $order->get_order_number();
		}else{
			$invoice_pdf_name_number_pos = Wt_Pklist_Common::get_order_meta($order_ids[0],'wf_invoice_number',true);
		}

		if("" === trim($wf_invoice_pdf_name_prefix)){
			$invoice_pdf_name_prefix_pos = "Invoice_";
		}else{
			$invoice_pdf_name_prefix_pos = $wf_invoice_pdf_name_prefix;
		}

		if("[prefix][invoice_no]" === $wf_invoice_pdf_name_format){
			$invoice_pdf_name_format = $wf_invoice_pdf_name_format;
		}else{
			$invoice_pdf_name_format = "[prefix][order_no]";
		}

		return str_replace(array('[prefix]','[order_no]','[invoice_no]'),array($invoice_pdf_name_prefix_pos,$invoice_pdf_name_number_pos,$invoice_pdf_name_number_pos),$invoice_pdf_name_format); 
	}

	public static function wf_search_order_by_invoice_number($search_fields){
		array_push($search_fields, 'wf_invoice_number');
		return $search_fields;
	}

	public static function check_if_mpdf_used(){
		$active_pdf_library = Wf_Woocommerce_Packing_List::get_option('active_pdf_library');
		if("mpdf" === $active_pdf_library && is_plugin_active('mpdf-addon-for-pdf-invoices/wt-woocommerce-packing-list-mpdf.php')){
			return true;
		}
		return false;
	}

	public static function qrcode_barcode_visibility($html,$template_type){
		$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
		if(!$show_qrcode_placeholder){
			$html = str_replace('wfte_img_barcode wfte_hidden','wfte_img_barcode',$html);
			if (false !== strpos($html, 'wfte_img_qrcode') && false === strpos($html, 'wfte_hidden')){
				$html = str_replace('wfte_img_qrcode','wfte_img_qrcode wfte_hidden',$html);
			}
			$html = preg_replace('/\b(wfte_img_qrcode\s*(?:(?:\s*wfte_hidden)+\s*)+)\b/', 'wfte_img_qrcode', $html);
		}
		return $html;
	}

	public function advanced_settings()
	{
		$out=array('key'=>'', 'val'=>'', 'success'=>false, 'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'));
		$warn_msg=__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce');
		
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
			if(isset($_POST['wt_pklist_custom_field_btn']))  
			{
			    //additional fields for checkout
				if(isset($_POST['wt_pklist_new_custom_field_title']) && isset($_POST['wt_pklist_new_custom_field_key']) && isset($_POST['wt_pklist_custom_field_type'])) 
		        {
		        	if("" !== trim($_POST['wt_pklist_new_custom_field_title']) && "" !== trim($_POST['wt_pklist_new_custom_field_key']))
		        	{
		        		$custom_field_type=sanitize_text_field($_POST['wt_pklist_custom_field_type']);
		        		if("order_meta" === $custom_field_type)
		        		{
		        			$module_base = (isset($_POST['wt_pklist_settings_base']) ? sanitize_text_field($_POST['wt_pklist_settings_base']) : 'main');
							$module_id = ("main" === $module_base ? '' : Wf_Woocommerce_Packing_List::get_module_id($module_base));
							$add_only = (isset($_POST['add_only']) ? true : false);
		        			$field_config=array(
		        				'order_meta'=>array(
		        					'list'=>'wf_additional_data_fields',
		        					'selected'=>'wf_'.$module_base.'_contactno_email',
		        				),
		        			);

		        			/* form input */
		        			$new_meta_key=sanitize_text_field($_POST['wt_pklist_new_custom_field_key']);		            
        					$new_meta_vl=sanitize_text_field($_POST['wt_pklist_new_custom_field_title']);

        					/* option key names for full list, selected list */
        					$list_field=$field_config[$custom_field_type]['list'];
        					$val_field=$field_config[$custom_field_type]['selected'];
        					
        					/* list of user created items */
        					$user_created=Wf_Woocommerce_Packing_List::get_option($list_field); //this is plugin main setting so no need to specify module base

        					/* updating new item to user created list */
        					$old_meta_key = "";
        					$old_meta_key_label = "";
        					if(!empty($user_created) && is_array($user_created)){
        						$old_meta_key = function_exists('array_key_first') ? array_key_first($user_created): key( array_slice( $user_created, 0, 1, true ) );
								if (null === $old_meta_key) {
								    $old_meta_key = ""; // An error should be handled here
								} else {
								    $old_meta_key_label = $user_created[$old_meta_key];
								}
        					}

        					$user_created = array();
        					$action=(isset($user_created[$new_meta_key]) ? 'edit' : 'add');
				            
				            $can_add_item=true;
        					if("edit" === $action && $add_only)
        					{
        						$can_add_item=false;
        					}

        					if($can_add_item)
        					{	

				            	$user_created[$new_meta_key] = $new_meta_vl;
				            	Wf_Woocommerce_Packing_List::update_option($list_field, $user_created);
				            }

				            if(!$add_only)
				            {
					            $vl=Wf_Woocommerce_Packing_List::get_option($val_field, $module_id);
					            $user_selected_arr =("" !== $vl && is_array($vl) ? $vl : array());			            

					            if(!in_array($new_meta_key, $user_selected_arr)) 
					            {
					                $user_selected_arr[] = $new_meta_key;
					                Wf_Woocommerce_Packing_List::update_option($val_field, $user_selected_arr, $module_id);			                
					            }
					        }

					        if($can_add_item)
					        {
					            $new_meta_key_display=Wf_Woocommerce_Packing_List::get_display_key($new_meta_key);

					            $dc_slug=self::sanitize_css_class_name($new_meta_key_display); /* This is for Dynamic customizer */

					            $out=array('key'=>$new_meta_key, 'val'=>$new_meta_vl.$new_meta_key_display, 'dc_slug'=>$dc_slug, 'success'=>true, 'action'=>$action, 'old_meta_key' => $old_meta_key, 'old_meta_key_label' => $old_meta_key_label, 'new_meta_label' => $new_meta_vl);
					        }else
					        {
					        	$out['msg']=__('Item with same meta key already exists', 'print-invoices-packing-slip-labels-for-woocommerce');
					        }
		        		}

		        	}else
		        	{
		        		$out['msg']=$warn_msg;
		        	}
		        }
		    }
		}
	    echo json_encode($out);
		exit();
	}

	public static function sanitize_css_class_name($str)
	{
		return preg_replace('/[^\-_a-zA-Z0-9]+/', '', $str);
	}

    public static function order_meta_dropdown_list(){
    	$order_meta_query = array();
    	if(isset($_GET['page'])){
    		if("wf_woocommerce_packing_list_invoice" === $_GET['page']){
    			global $wpdb;
		    	$order_meta_selected_list = Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');
		    	$first_meta_key = function_exists('array_key_first') ? array_key_first($order_meta_selected_list): key( array_slice( $order_meta_selected_list, 0, 1, true ) );
		    	$user_added_arr = array();
		    	if (null !== $first_meta_key) {
				    $user_added_arr[] = array('label' => $first_meta_key);
				}
		        $order_meta_query = $user_added_arr;
    		}
    	}
        return $order_meta_query;
    }

    /**
     * @since 3.0.2
     * Added target=_blank to the print invoice button on order listing of my account page
     */
    public function action_after_account_orders_js() {
	    ?>
	    <script>
	    (function($){
            $('a.wt_pklist_invoice_print').attr('target','_blank');
	    })(jQuery);
	    </script>
	    <?php
	}

	/**
	 * @since 3.0.3
	 * Tool for deleting all the invoice numbers
	 */
	public function wt_pklist_delete_all_invoice_numbers_tool($tools){
		$article_url = "https://www.webtoffee.com/reset-delete-existing-invoice-numbers";

		$tool_description = sprintf('%1$s<br><strong class="red">%2$s</strong>',__( 'This will remove all invoice numbers created by WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels by WebToffee.', 'print-invoices-packing-slip-labels-for-woocommerce' ),__( 'Note:', 'print-invoices-packing-slip-labels-for-woocommerce' ))." ".sprintf(__( 'Before using this tool, please make sure you followed the steps described in this article %1$s how to reset/delete existing invoice numbers%2$s.', 'print-invoices-packing-slip-labels-for-woocommerce' ),'<a href="' . esc_url( $article_url ) . '">','</a>');
		
		$tools['wf_pklist_delete_all_invoice_number'] = array(
	        'name' => __('Delete all generated invoice numbers', 'print-invoices-packing-slip-labels-for-woocommerce'),
	        'button' => __('Delete',  'print-invoices-packing-slip-labels-for-woocommerce'), 
	        'desc'   => $tool_description,
	        'callback' => array( $this, 'wf_pklist_delete_all_invoice_numbers_func' ),
	    );
	    return $tools;
	}

	public function wf_pklist_delete_all_invoice_numbers_func(){
		Wt_Pklist_Common::delete_order_meta_by_key('wf_invoice_number');
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			$this->wt_get_empty_invoice_number_count();
		}
	}

	public function wt_pklist_action_scheduler_for_invoice_number_count(){
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		$group = "wt_pklist_get_invoice_number_count_auto_generation";
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			if ( false === as_next_scheduled_action( 'update_empty_invoice_number_count' ) ) {
			        as_schedule_recurring_action( time(), 86400, 'update_empty_invoice_number_count', array(), $group );
			}
		}else{
			if (as_next_scheduled_action('update_empty_invoice_number_count', array(), $group) === true) {
	            as_unschedule_all_actions('update_empty_invoice_number_count', array(), $group);
	        }
		}
	}

	public function wt_get_empty_invoice_number_count(){
		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		$empty_count = count(self::get_order_ids_for_invoice_number_generation($invoice_module_id));
		update_option('invoice_empty_count',$empty_count);
	}

	public function wt_pklist_action_scheduler_for_invoice_number(){
		$data = self::check_before_auto_generating_invoice_no();
		$group = "wt_pklist_invoice_number_auto_generation";
		if(true === $data["invoice_enabled"] && $data["order_empty_invoice_count"] > 0){
			if((true === $data["auto_generate"]) && (10 < $data["order_empty_invoice_count"]) && (as_next_scheduled_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group) === false)){
				as_enqueue_async_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group);
			}elseif(true === $data["auto_generate"] && (10 >= $data["order_empty_invoice_count"] && 0 < $data["order_empty_invoice_count"])){
				do_action('wt_pklist_auto_generate_invoice_number_module');
			}
		}else{
			if (as_next_scheduled_action('wt_pklist_schedule_auto_generate_invoice_number', array(), $group) === true && false === $data["invoice_enabled"]) {
	            as_unschedule_all_actions('wt_pklist_schedule_auto_generate_invoice_number', array(), $group);
	        }
		}
	}

	public static function check_before_auto_generating_invoice_no(){
		global $pagenow, $typenow, $post;
		$auto_generate = false;
		$invoice_enabled = false;
		$result = array('auto_generate' => false, 'order_empty_invoice_count' => 0, 'invoice_enabled' => false);
		if(Wt_Pklist_Common::is_wc_hpos_enabled()){
			// order listing page and order edit page will have these url parameters
			if('admin.php' === $pagenow && isset($_GET['page']) && "wc-orders" === $_GET['page']){
				$result["auto_generate"] = true;
			}
		}else{
			if('edit.php' === $pagenow && (isset($_GET['post_type']) && "shop_order" === $_GET['post_type'])){
				$result["auto_generate"] = true;
			}elseif('post.php' === $pagenow){
				$req_type = "";
				if ('post' === $typenow && isset($_GET['post']) && !empty($_GET['post'])) {
					$req_type = $post->post_type;
				} elseif (empty($typenow) && !empty($_GET['post'])) {
					$post = get_post($_GET['post']);
					$req_type = $post->post_type;
				}

				if("shop_order" === $req_type){
					$result["auto_generate"] = true;
				}
			}
		}

		$invoice_module_id=Wf_Woocommerce_Packing_List::get_module_id('invoice');
		$enable_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$invoice_module_id);
		if((Wf_Woocommerce_Packing_List_Public::module_exists('invoice')) && ("Yes" === $enable_invoice)){
			$result["invoice_enabled"] = true;
		}
		
		if(true === $result["auto_generate"] && true === $result["invoice_enabled"]){
			$result["order_empty_invoice_count"] = (int)get_option('invoice_empty_count',true);
		}
		return $result;
	}

	public function action_for_auto_generate_invoice_number()
	{
		do_action('wt_pklist_auto_generate_invoice_number_module');
	}

	public static function get_order_ids_for_invoice_number_generation($module_id){
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$module_id);
		$order_meta_query_arr = array();
		if(!empty($generate_invoice_for)){
			$invoice_for_prev_install_order = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_prev_install_orders',$module_id);
	   		$args = array(
						'orderby'	=> 'ID',
						'order' => 'ASC',
					    'posts_per_page' => -1,
					    'post_type' => 'shop_order',
					    'post_status' => $generate_invoice_for,
					    'fields' => 'ids',
					    'meta_query' => array(
						   'relation' => 'OR',
						    array(
						     'key' => 'wf_invoice_number',
						     'compare' => 'NOT EXISTS'
						    ),
						    array(
						     'key' => 'wf_invoice_number',
						     'value' => ''
						    ),
						    array(
						     'key' => 'wf_invoice_number',
						     'value' => NULL
						    )
						)
					);

	   		if("No" === $invoice_for_prev_install_order){
	   			$utc_timestamp = get_option('wt_pklist_installation_date');
				$utc_timestamp_converted = date( 'Y-m-d h:i:s', $utc_timestamp );
				$local_timestamp = get_date_from_gmt( $utc_timestamp_converted, 'Y-m-d h:i:s' );
				$args['date_query'] = array('after' => $local_timestamp);
	   		}
			if(Wt_Pklist_Common::is_wc_hpos_enabled()){
				$empty_invoice_order_qry = new WC_Order_Query($args);
	   			$order_meta_query_arr = $empty_invoice_order_qry->get_orders();
			}else{
				$empty_invoice_order_qry = new WP_Query($args);
	   			$order_meta_query_arr = $empty_invoice_order_qry->posts;
			}
		}
		update_option('invoice_empty_count',count($order_meta_query_arr));
		return $order_meta_query_arr;
	}

	/**
	 * @since 3.0.5
	 * Function to add the count when user generating the documents
	 */
	public static function created_document_count($order_id,$template_type){
		$order = wc_get_order( $order_id );
		$check_old_order = self::check_the_order_is_old($order_id);
		$update_count = false;
		if(!$check_old_order){
			$meta_key = '_created_document';
		}else{
			$meta_key = '_created_document_old';
		}
		$order_docs = Wt_Pklist_Common::get_order_meta($order_id,$meta_key,true);
		if($order_docs){
			if(is_array($order_docs) && !in_array($template_type, $order_docs)){
				array_push($order_docs,$template_type);
				Wt_Pklist_Common::update_order_meta($order_id,$meta_key,$order_docs);
				$update_count = (false === $check_old_order) ? true : false;
			}
		}else{
			$order_docs = array($template_type);
			Wt_Pklist_Common::update_order_meta($order_id,$meta_key,$order_docs);
			$update_count = (false === $check_old_order) ? true : false;
		}
		
		if($update_count){
			if ( false !== get_option( 'wt_created_document_count' )) {
				$count = (int)get_option( 'wt_created_document_count' );
				update_option('wt_created_document_count',$count+1);
			}else{
				update_option('wt_created_document_count',1);
			}
		}
	}

	/**
	 * @since 3.0.5
	 * Function to check whether the order is old or not from the installation date
	 */
	public static function check_the_order_is_old($order_id){
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
	   	return false;
	}

	/**
	 *  @since 4.0.0
	 *	Enable/disable the document modules using ajax
	 */

	public function document_module_enable_disable(){
		// Check nonce:
        if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
		{
			echo json_encode(array('status' => true, 'doc_set' => 0, 'message' => __('You are not allowed to do this action','print-invoices-packing-slip-labels-for-woocommerce')));
			exit();
		}

		$output = array('status' => true);
	    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
	    if(false === $wt_pklist_common_modules)
	    {
	        $wt_pklist_common_modules=array();
	    }
	    if(isset($_POST['doc_module_name']))
	    {
	    	$wt_pklist_post = explode('wt_pklist_',$_POST['doc_module_name']);
	    	$wt_pklist_common_modules[$wt_pklist_post[1]]=$_POST['doc_module_set'];
	    	$output['doc_set'] = 1;
	    	$output['message'] = __('Updated','print-invoices-packing-slip-labels-for-woocommerce');	
	    }else{
	    	foreach($wt_pklist_common_modules as $k=>$v)
	        {
				$wt_pklist_common_modules[$k]=0;
	        }
	        $output['doc_set'] = 2;
	        $output['message'] = __('No modules','print-invoices-packing-slip-labels-for-woocommerce');
	    }
	    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
	    echo json_encode($output);
		exit();
	}

	public static function check_full_refunded_property($order){
		$all_refund_orders = $order->get_refunds();
		$number_of_refunds = count($all_refund_orders);
		$order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
		if(1 === $number_of_refunds && $order_status == "refunded"){
			$order->full_refunded = 1;
		}else{
			$order->full_refunded = 0;
		}
		return $order;
	}

	public static function not_activated_pro_addons($excl_addon = ""){
		$pro_addons_list = array(
            'wt_ipc_addon'  => 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php',
            'wt_sdd_addon'  => 'wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php',
            'wt_pl_addon'   => 'wt-woocommerce-picklist-addon/wt-woocommerce-picklist-addon.php',
            'wt_pi_addon'   => 'wt-woocommerce-proforma-addon/wt-woocommerce-proforma-addon.php',
            'wt_al_addon'   => 'wt-woocommerce-addresslabel-addon/wt-woocommerce-addresslabel-addon.php',
            'wt_qr_addon'   => 'qrcode-addon-for-woocommerce-pdf-invoices/qrcode-addon-for-woocommerce-pdf-invoices.php',
        );
        if("" !== $excl_addon && isset($pro_addons_list[$excl_addon])){
        	unset($pro_addons_list[$excl_addon]);
        }
        $not_activated_pro_addons = array();
        foreach($pro_addons_list as $pro_addon_key => $pro_addon){
            if(!is_plugin_active($pro_addon)){
                array_push($not_activated_pro_addons, $pro_addon_key);
            }
        }
        return $not_activated_pro_addons;
	}

	/**
	 * Added to hide the shipping address if it is an empty
	 * Added to use the billing address as shipping address
	 * @since 4.0.4
	 */
	public static function hide_empty_shipping_address($html,$template_type,$order){
		$use_billing_address = apply_filters('wt_pklist_use_billing_address_for_shipping_address',true,$template_type,$order);
		if(!empty($order) && !$use_billing_address){
			$shipping_address = $order->get_formatted_shipping_address();
			if(empty($shipping_address))
			{
				$html .='<style>
				.wfte_shipping_address{
					display:none !important;
				}
				</style>';
			}
		}
		return $html;
	}
	
	/**
	 * Function to get all the language list for pdf
	 * @since 4.0.8
	 * @return array
	 */
	public static function all_wt_pklist_languages(){
		$wt_pklist_language_list =
			array (
				'af' => array (
					'name' => 'Afrikaans',
					'native_name' => 'Afrikaans',
					'locale_code' => 'af',
					'wp_locale_code' => 'af',
					'is_rtl' => false,
				),
				'ak' => array (
					'name' => 'Akan',
					'native_name' => 'Akan',
					'locale_code' => 'ak',
					'wp_locale_code' => 'ak',
					'is_rtl' => false,
				),
				'sq' => array (
					'name' => 'Albanian',
					'native_name' => 'Shqip',
					'locale_code' => 'sq',
					'wp_locale_code' => 'sq',
					'is_rtl' => false,
				),
				'arq' => array (
					'name' => 'Algerian Arabic',
					'native_name' => 'الدارجة الجزايرية',
					'locale_code' => 'arq',
					'wp_locale_code' => 'arq',
					'is_rtl' => false,
				),
				'am' => array (
					'name' => 'Amharic',
					'native_name' => 'አማርኛ',
					'locale_code' => 'am',
					'wp_locale_code' => 'am',
					'is_rtl' => false,
				),
				'ar' => array (
					'name' => 'Arabic',
					'native_name' => 'العربية',
					'locale_code' => 'ar',
					'wp_locale_code' => 'ar',
					'is_rtl' => true,
				),
				'hy' => array (
					'name' => 'Armenian',
					'native_name' => 'Հայերեն',
					'locale_code' => 'hy',
					'wp_locale_code' => 'hy',
					'is_rtl' => false,
				),
				'rup_MK' => array (
					'name' => 'Aromanian',
					'native_name' => 'Armãneashce',
					'locale_code' => 'rup',
					'wp_locale_code' => 'rup_MK',
					'is_rtl' => false,
				),
				'frp' => array (
					'name' => 'Arpitan',
					'native_name' => 'Arpitan',
					'locale_code' => 'frp',
					'wp_locale_code' => 'frp',
					'is_rtl' => false,
				),
				'as' => array (
					'name' => 'Assamese',
					'native_name' => 'অসমীয়া',
					'locale_code' => 'as',
					'wp_locale_code' => 'as',
					'is_rtl' => false,
				),
				'az' => 
				array (
					'name' => 'Azerbaijani',
					'native_name' => 'Azərbaycan dili',
					'locale_code' => 'az',
					'wp_locale_code' => 'az',
					'is_rtl' => false,
				),
				'az_TR' => 
				array (
					'name' => 'Azerbaijani (Turkey)',
					'native_name' => 'Azərbaycan Türkcəsi',
					'locale_code' => 'az-tr',
					'wp_locale_code' => 'az_TR',
					'is_rtl' => false,
				),
				'bcc' => 
				array (
					'name' => 'Balochi Southern',
					'native_name' => 'بلوچی مکرانی',
					'locale_code' => 'bcc',
					'wp_locale_code' => 'bcc',
					'is_rtl' => false,
				),
				'ba' => 
				array (
					'name' => 'Bashkir',
					'native_name' => 'башҡорт теле',
					'locale_code' => 'ba',
					'wp_locale_code' => 'ba',
					'is_rtl' => false,
				),
				'eu' => 
				array (
					'name' => 'Basque',
					'native_name' => 'Euskara',
					'locale_code' => 'eu',
					'wp_locale_code' => 'eu',
					'is_rtl' => false,
				),
				'bel' => 
				array (
					'name' => 'Belarusian',
					'native_name' => 'Беларуская мова',
					'locale_code' => 'bel',
					'wp_locale_code' => 'bel',
					'is_rtl' => false,
				),
				'bn_BD' => 
				array (
					'name' => 'Bengali',
					'native_name' => 'বাংলা',
					'locale_code' => 'bn',
					'wp_locale_code' => 'bn_BD',
					'is_rtl' => false,
				),
				'bs_BA' => 
				array (
					'name' => 'Bosnian',
					'native_name' => 'Bosanski',
					'locale_code' => 'bs',
					'wp_locale_code' => 'bs_BA',
					'is_rtl' => false,
				),
				'bre' => 
				array (
					'name' => 'Breton',
					'native_name' => 'Brezhoneg',
					'locale_code' => 'br',
					'wp_locale_code' => 'bre',
					'is_rtl' => false,
				),
				'bg_BG' => 
				array (
					'name' => 'Bulgarian',
					'native_name' => 'Български',
					'locale_code' => 'bg',
					'wp_locale_code' => 'bg_BG',
					'is_rtl' => false,
				),
				'ca' => 
				array (
					'name' => 'Catalan',
					'native_name' => 'Català',
					'locale_code' => 'ca',
					'wp_locale_code' => 'ca',
					'is_rtl' => false,
				),
				'bal' => 
				array (
					'name' => 'Catalan (Balear)',
					'native_name' => 'Català (Balear)',
					'locale_code' => 'bal',
					'wp_locale_code' => 'bal',
					'is_rtl' => false,
				),
				'ceb' => 
				array (
					'name' => 'Cebuano',
					'native_name' => 'Cebuano',
					'locale_code' => 'ceb',
					'wp_locale_code' => 'ceb',
					'is_rtl' => false,
				),
				'zh_CN' => 
				array (
					'name' => 'Chinese (China)',
					'native_name' => '简体中文',
					'locale_code' => 'zh-cn',
					'wp_locale_code' => 'zh_CN',
					'is_rtl' => false,
				),
				'zh_HK' => 
				array (
					'name' => 'Chinese (Hong Kong)',
					'native_name' => '香港中文版',
					'locale_code' => 'zh-hk',
					'wp_locale_code' => 'zh_HK',
					'is_rtl' => false,
				),
				'zh_TW' => 
				array (
					'name' => 'Chinese (Taiwan)',
					'native_name' => '繁體中文',
					'locale_code' => 'zh-tw',
					'wp_locale_code' => 'zh_TW',
					'is_rtl' => false,
				),
				'co' => 
				array (
					'name' => 'Corsican',
					'native_name' => 'Corsu',
					'locale_code' => 'co',
					'wp_locale_code' => 'co',
					'is_rtl' => false,
				),
				'hr' => 
				array (
					'name' => 'Croatian',
					'native_name' => 'Hrvatski',
					'locale_code' => 'hr',
					'wp_locale_code' => 'hr',
					'is_rtl' => false,
				),
				'cs_CZ' => 
				array (
					'name' => 'Czech',
					'native_name' => 'Čeština‎',
					'locale_code' => 'cs',
					'wp_locale_code' => 'cs_CZ',
					'is_rtl' => false,
				),
				'da_DK' => 
				array (
					'name' => 'Danish',
					'native_name' => 'Dansk',
					'locale_code' => 'da',
					'wp_locale_code' => 'da_DK',
					'is_rtl' => false,
				),
				'dv' => 
				array (
					'name' => 'Dhivehi',
					'native_name' => 'ދިވެހި',
					'locale_code' => 'dv',
					'wp_locale_code' => 'dv',
					'is_rtl' => true,
				),
				'nl_NL' => 
				array (
					'name' => 'Dutch',
					'native_name' => 'Nederlands',
					'locale_code' => 'nl',
					'wp_locale_code' => 'nl_NL',
					'is_rtl' => false,
				),
				'nl_BE' => 
				array (
					'name' => 'Dutch (Belgium)',
					'native_name' => 'Nederlands (België)',
					'locale_code' => 'nl-be',
					'wp_locale_code' => 'nl_BE',
					'is_rtl' => false,
				),
				'dzo' => 
				array (
					'name' => 'Dzongkha',
					'native_name' => 'རྫོང་ཁ',
					'locale_code' => 'dzo',
					'wp_locale_code' => 'dzo',
					'is_rtl' => false,
				),
				'art_xemoji' => 
				array (
					'name' => 'Emoji',
					'native_name' => '🌏🌍🌎 (Emoji)',
					'locale_code' => 'art-xemoji',
					'wp_locale_code' => 'art_xemoji',
					'is_rtl' => false,
				),
				'en_US' => 
				array (
					'name' => 'English',
					'native_name' => 'English',
					'locale_code' => 'en',
					'wp_locale_code' => 'en_US',
					'is_rtl' => false,
				),
				'en_AU' => 
				array (
					'name' => 'English (Australia)',
					'native_name' => 'English (Australia)',
					'locale_code' => 'en-au',
					'wp_locale_code' => 'en_AU',
					'is_rtl' => false,
				),
				'en_CA' => 
				array (
					'name' => 'English (Canada)',
					'native_name' => 'English (Canada)',
					'locale_code' => 'en-ca',
					'wp_locale_code' => 'en_CA',
					'is_rtl' => false,
				),
				'en_NZ' => 
				array (
					'name' => 'English (New Zealand)',
					'native_name' => 'English (New Zealand)',
					'locale_code' => 'en-nz',
					'wp_locale_code' => 'en_NZ',
					'is_rtl' => false,
				),
				'en_ZA' => 
				array (
					'name' => 'English (South Africa)',
					'native_name' => 'English (South Africa)',
					'locale_code' => 'en-za',
					'wp_locale_code' => 'en_ZA',
					'is_rtl' => false,
				),
				'en_GB' => 
				array (
					'name' => 'English (UK)',
					'native_name' => 'English (UK)',
					'locale_code' => 'en-gb',
					'wp_locale_code' => 'en_GB',
					'is_rtl' => false,
				),
				'eo' => 
				array (
					'name' => 'Esperanto',
					'native_name' => 'Esperanto',
					'locale_code' => 'eo',
					'wp_locale_code' => 'eo',
					'is_rtl' => false,
				),
				'et' => 
				array (
					'name' => 'Estonian',
					'native_name' => 'Eesti',
					'locale_code' => 'et',
					'wp_locale_code' => 'et',
					'is_rtl' => false,
				),
				'fo' => 
				array (
					'name' => 'Faroese',
					'native_name' => 'Føroyskt',
					'locale_code' => 'fo',
					'wp_locale_code' => 'fo',
					'is_rtl' => false,
				),
				'fi' => 
				array (
					'name' => 'Finnish',
					'native_name' => 'Suomi',
					'locale_code' => 'fi',
					'wp_locale_code' => 'fi',
					'is_rtl' => false,
				),
				'fr_BE' => 
				array (
					'name' => 'French (Belgium)',
					'native_name' => 'Français de Belgique',
					'locale_code' => 'fr-be',
					'wp_locale_code' => 'fr_BE',
					'is_rtl' => false,
				),
				'fr_CA' => 
				array (
					'name' => 'French (Canada)',
					'native_name' => 'Français du Canada',
					'locale_code' => 'fr-ca',
					'wp_locale_code' => 'fr_CA',
					'is_rtl' => false,
				),
				'fr_FR' => 
				array (
					'name' => 'French (France)',
					'native_name' => 'Français',
					'locale_code' => 'fr',
					'wp_locale_code' => 'fr_FR',
					'is_rtl' => false,
				),
				'fy' => 
				array (
					'name' => 'Frisian',
					'native_name' => 'Frysk',
					'locale_code' => 'fy',
					'wp_locale_code' => 'fy',
					'is_rtl' => false,
				),
				'fur' => 
				array (
					'name' => 'Friulian',
					'native_name' => 'Friulian',
					'locale_code' => 'fur',
					'wp_locale_code' => 'fur',
					'is_rtl' => false,
				),
				'fuc' => 
				array (
					'name' => 'Fulah',
					'native_name' => 'Pulaar',
					'locale_code' => 'fuc',
					'wp_locale_code' => 'fuc',
					'is_rtl' => false,
				),
				'gl_ES' => 
				array (
					'name' => 'Galician',
					'native_name' => 'Galego',
					'locale_code' => 'gl',
					'wp_locale_code' => 'gl_ES',
					'is_rtl' => false,
				),
				'ka_GE' => 
				array (
					'name' => 'Georgian',
					'native_name' => 'ქართული',
					'locale_code' => 'ka',
					'wp_locale_code' => 'ka_GE',
					'is_rtl' => false,
				),
				'de_DE' => 
				array (
					'name' => 'German',
					'native_name' => 'Deutsch',
					'locale_code' => 'de',
					'wp_locale_code' => 'de_DE',
					'is_rtl' => false,
				),
				'de_CH' => 
				array (
					'name' => 'German (Switzerland)',
					'native_name' => 'Deutsch (Schweiz)',
					'locale_code' => 'de-ch',
					'wp_locale_code' => 'de_CH',
					'is_rtl' => false,
				),
				'el' => 
				array (
					'name' => 'Greek',
					'native_name' => 'Ελληνικά',
					'locale_code' => 'el',
					'wp_locale_code' => 'el',
					'is_rtl' => false,
				),
				'kal' => 
				array (
					'name' => 'Greenlandic',
					'native_name' => 'Kalaallisut',
					'locale_code' => 'kal',
					'wp_locale_code' => 'kal',
					'is_rtl' => false,
				),
				'gn' => 
				array (
					'name' => 'Guaraní',
					'native_name' => 'Avañe\'ẽ',
					'locale_code' => 'gn',
					'wp_locale_code' => 'gn',
					'is_rtl' => false,
				),
				'gu' => 
				array (
					'name' => 'Gujarati',
					'native_name' => 'ગુજરાતી',
					'locale_code' => 'gu',
					'wp_locale_code' => 'gu',
					'is_rtl' => false,
				),
				'haw_US' => 
				array (
					'name' => 'Hawaiian',
					'native_name' => 'Ōlelo Hawaiʻi',
					'locale_code' => 'haw',
					'wp_locale_code' => 'haw_US',
					'is_rtl' => false,
				),
				'haz' => 
				array (
					'name' => 'Hazaragi',
					'native_name' => 'هزاره گی',
					'locale_code' => 'haz',
					'wp_locale_code' => 'haz',
					'is_rtl' => false,
				),
				'he_IL' => 
				array (
					'name' => 'Hebrew',
					'native_name' => 'עִבְרִית',
					'locale_code' => 'he',
					'wp_locale_code' => 'he_IL',
					'is_rtl' => true,
				),
				'hi_IN' => 
				array (
					'name' => 'Hindi',
					'native_name' => 'हिन्दी',
					'locale_code' => 'hi',
					'wp_locale_code' => 'hi_IN',
					'is_rtl' => false,
				),
				'hu_HU' => 
				array (
					'name' => 'Hungarian',
					'native_name' => 'Magyar',
					'locale_code' => 'hu',
					'wp_locale_code' => 'hu_HU',
					'is_rtl' => false,
				),
				'is_IS' => 
				array (
					'name' => 'Icelandic',
					'native_name' => 'Íslenska',
					'locale_code' => 'is',
					'wp_locale_code' => 'is_IS',
					'is_rtl' => false,
				),
				'ido' => 
				array (
					'name' => 'Ido',
					'native_name' => 'Ido',
					'locale_code' => 'ido',
					'wp_locale_code' => 'ido',
					'is_rtl' => false,
				),
				'id_ID' => 
				array (
					'name' => 'Indonesian',
					'native_name' => 'Bahasa Indonesia',
					'locale_code' => 'id',
					'wp_locale_code' => 'id_ID',
					'is_rtl' => false,
				),
				'ga' => 
				array (
					'name' => 'Irish',
					'native_name' => 'Gaelige',
					'locale_code' => 'ga',
					'wp_locale_code' => 'ga',
					'is_rtl' => false,
				),
				'it_IT' => 
				array (
					'name' => 'Italian',
					'native_name' => 'Italiano',
					'locale_code' => 'it',
					'wp_locale_code' => 'it_IT',
					'is_rtl' => false,
				),
				'ja' => 
				array (
					'name' => 'Japanese',
					'native_name' => '日本語',
					'locale_code' => 'ja',
					'wp_locale_code' => 'ja',
					'is_rtl' => false,
				),
				'jv_ID' => 
				array (
					'name' => 'Javanese',
					'native_name' => 'Basa Jawa',
					'locale_code' => 'jv',
					'wp_locale_code' => 'jv_ID',
					'is_rtl' => false,
				),
				'kab' => 
				array (
					'name' => 'Kabyle',
					'native_name' => 'Taqbaylit',
					'locale_code' => 'kab',
					'wp_locale_code' => 'kab',
					'is_rtl' => false,
				),
				'kn' => 
				array (
					'name' => 'Kannada',
					'native_name' => 'ಕನ್ನಡ',
					'locale_code' => 'kn',
					'wp_locale_code' => 'kn',
					'is_rtl' => false,
				),
				'kk' => 
				array (
					'name' => 'Kazakh',
					'native_name' => 'Қазақ тілі',
					'locale_code' => 'kk',
					'wp_locale_code' => 'kk',
					'is_rtl' => false,
				),
				'km' => 
				array (
					'name' => 'Khmer',
					'native_name' => 'ភាសាខ្មែរ',
					'locale_code' => 'km',
					'wp_locale_code' => 'km',
					'is_rtl' => false,
				),
				'kin' => 
				array (
					'name' => 'Kinyarwanda',
					'native_name' => 'Ikinyarwanda',
					'locale_code' => 'kin',
					'wp_locale_code' => 'kin',
					'is_rtl' => false,
				),
				'ky_KY' => 
				array (
					'name' => 'Kirghiz',
					'native_name' => 'кыргыз тили',
					'locale_code' => 'ky',
					'wp_locale_code' => 'ky_KY',
					'is_rtl' => false,
				),
				'ko_KR' => 
				array (
					'name' => 'Korean',
					'native_name' => '한국어',
					'locale_code' => 'ko',
					'wp_locale_code' => 'ko_KR',
					'is_rtl' => false,
				),
				'ckb' => 
				array (
					'name' => 'Kurdish (Sorani)',
					'native_name' => 'كوردی‎',
					'locale_code' => 'ckb',
					'wp_locale_code' => 'ckb',
					'is_rtl' => false,
				),
				'lo' => 
				array (
					'name' => 'Lao',
					'native_name' => 'ພາສາລາວ',
					'locale_code' => 'lo',
					'wp_locale_code' => 'lo',
					'is_rtl' => false,
				),
				'lv' => 
				array (
					'name' => 'Latvian',
					'native_name' => 'Latviešu valoda',
					'locale_code' => 'lv',
					'wp_locale_code' => 'lv',
					'is_rtl' => false,
				),
				'li' => 
				array (
					'name' => 'Limburgish',
					'native_name' => 'Limburgs',
					'locale_code' => 'li',
					'wp_locale_code' => 'li',
					'is_rtl' => false,
				),
				'lin' => 
				array (
					'name' => 'Lingala',
					'native_name' => 'Ngala',
					'locale_code' => 'lin',
					'wp_locale_code' => 'lin',
					'is_rtl' => false,
				),
				'lt_LT' => 
				array (
					'name' => 'Lithuanian',
					'native_name' => 'Lietuvių kalba',
					'locale_code' => 'lt',
					'wp_locale_code' => 'lt_LT',
					'is_rtl' => false,
				),
				'lb_LU' => 
				array (
					'name' => 'Luxembourgish',
					'native_name' => 'Lëtzebuergesch',
					'locale_code' => 'lb',
					'wp_locale_code' => 'lb_LU',
					'is_rtl' => false,
				),
				'mk_MK' => 
				array (
					'name' => 'Macedonian',
					'native_name' => 'Македонски јазик',
					'locale_code' => 'mk',
					'wp_locale_code' => 'mk_MK',
					'is_rtl' => false,
				),
				'mg_MG' => 
				array (
					'name' => 'Malagasy',
					'native_name' => 'Malagasy',
					'locale_code' => 'mg',
					'wp_locale_code' => 'mg_MG',
					'is_rtl' => false,
				),
				'ms_MY' => 
				array (
					'name' => 'Malay',
					'native_name' => 'Bahasa Melayu',
					'locale_code' => 'ms',
					'wp_locale_code' => 'ms_MY',
					'is_rtl' => false,
				),
				'ml_IN' => 
				array (
					'name' => 'Malayalam',
					'native_name' => 'മലയാളം',
					'locale_code' => 'ml',
					'wp_locale_code' => 'ml_IN',
					'is_rtl' => false,
				),
				'mri' => 
				array (
					'name' => 'Maori',
					'native_name' => 'Te Reo Māori',
					'locale_code' => 'mri',
					'wp_locale_code' => 'mri',
					'is_rtl' => false,
				),
				'mr' => 
				array (
					'name' => 'Marathi',
					'native_name' => 'मराठी',
					'locale_code' => 'mr',
					'wp_locale_code' => 'mr',
					'is_rtl' => false,
				),
				'xmf' => 
				array (
					'name' => 'Mingrelian',
					'native_name' => 'მარგალური ნინა',
					'locale_code' => 'xmf',
					'wp_locale_code' => 'xmf',
					'is_rtl' => false,
				),
				'mn' => 
				array (
					'name' => 'Mongolian',
					'native_name' => 'Монгол',
					'locale_code' => 'mn',
					'wp_locale_code' => 'mn',
					'is_rtl' => false,
				),
				'me_ME' => 
				array (
					'name' => 'Montenegrin',
					'native_name' => 'Crnogorski jezik',
					'locale_code' => 'me',
					'wp_locale_code' => 'me_ME',
					'is_rtl' => false,
				),
				'ary' => 
				array (
					'name' => 'Moroccan Arabic',
					'native_name' => 'العربية المغربية',
					'locale_code' => 'ary',
					'wp_locale_code' => 'ary',
					'is_rtl' => false,
				),
				'my_MM' => 
				array (
					'name' => 'Myanmar (Burmese)',
					'native_name' => 'ဗမာစာ',
					'locale_code' => 'mya',
					'wp_locale_code' => 'my_MM',
					'is_rtl' => false,
				),
				'ne_NP' => 
				array (
					'name' => 'Nepali',
					'native_name' => 'नेपाली',
					'locale_code' => 'ne',
					'wp_locale_code' => 'ne_NP',
					'is_rtl' => false,
				),
				'nb_NO' => 
				array (
					'name' => 'Norwegian (Bokmål)',
					'native_name' => 'Norsk bokmål',
					'locale_code' => 'nb',
					'wp_locale_code' => 'nb_NO',
					'is_rtl' => false,
				),
				'nn_NO' => 
				array (
					'name' => 'Norwegian (Nynorsk)',
					'native_name' => 'Norsk nynorsk',
					'locale_code' => 'nn',
					'wp_locale_code' => 'nn_NO',
					'is_rtl' => false,
				),
				'oci' => 
				array (
					'name' => 'Occitan',
					'native_name' => 'Occitan',
					'locale_code' => 'oci',
					'wp_locale_code' => 'oci',
					'is_rtl' => false,
				),
				'ory' => 
				array (
					'name' => 'Oriya',
					'native_name' => 'ଓଡ଼ିଆ',
					'locale_code' => 'ory',
					'wp_locale_code' => 'ory',
					'is_rtl' => false,
				),
				'os' => 
				array (
					'name' => 'Ossetic',
					'native_name' => 'Ирон',
					'locale_code' => 'os',
					'wp_locale_code' => 'os',
					'is_rtl' => false,
				),
				'ps' => 
				array (
					'name' => 'Pashto',
					'native_name' => 'پښتو',
					'locale_code' => 'ps',
					'wp_locale_code' => 'ps',
					'is_rtl' => true,
				),
				'fa_IR' => 
				array (
					'name' => 'Persian',
					'native_name' => 'فارسی',
					'locale_code' => 'fa',
					'wp_locale_code' => 'fa_IR',
					'is_rtl' => true,
				),
				'fa_AF' => 
				array (
					'name' => 'Persian (Afghanistan)',
					'native_name' => '(فارسی (افغانستان',
					'locale_code' => 'fa-af',
					'wp_locale_code' => 'fa_AF',
					'is_rtl' => false,
				),
				'pl_PL' => 
				array (
					'name' => 'Polish',
					'native_name' => 'Polski',
					'locale_code' => 'pl',
					'wp_locale_code' => 'pl_PL',
					'is_rtl' => false,
				),
				'pt_BR' => 
				array (
					'name' => 'Portuguese (Brazil)',
					'native_name' => 'Português do Brasil',
					'locale_code' => 'pt-br',
					'wp_locale_code' => 'pt_BR',
					'is_rtl' => false,
				),
				'pt_PT' => 
				array (
					'name' => 'Portuguese (Portugal)',
					'native_name' => 'Português',
					'locale_code' => 'pt',
					'wp_locale_code' => 'pt_PT',
					'is_rtl' => false,
				),
				'pa_IN' => 
				array (
					'name' => 'Punjabi',
					'native_name' => 'ਪੰਜਾਬੀ',
					'locale_code' => 'pa',
					'wp_locale_code' => 'pa_IN',
					'is_rtl' => false,
				),
				'rhg' => 
				array (
					'name' => 'Rohingya',
					'native_name' => 'Ruáinga',
					'locale_code' => 'rhg',
					'wp_locale_code' => 'rhg',
					'is_rtl' => false,
				),
				'ro_RO' => 
				array (
					'name' => 'Romanian',
					'native_name' => 'Română',
					'locale_code' => 'ro',
					'wp_locale_code' => 'ro_RO',
					'is_rtl' => false,
				),
				'roh' => 
				array (
					'name' => 'Romansh Vallader',
					'native_name' => 'Rumantsch Vallader',
					'locale_code' => 'roh',
					'wp_locale_code' => 'roh',
					'is_rtl' => false,
				),
				'ru_RU' => 
				array (
					'name' => 'Russian',
					'native_name' => 'Русский',
					'locale_code' => 'ru',
					'wp_locale_code' => 'ru_RU',
					'is_rtl' => false,
				),
				'rue' => 
				array (
					'name' => 'Rusyn',
					'native_name' => 'Русиньскый',
					'locale_code' => 'rue',
					'wp_locale_code' => 'rue',
					'is_rtl' => false,
				),
				'sah' => 
				array (
					'name' => 'Sakha',
					'native_name' => 'Сахалыы',
					'locale_code' => 'sah',
					'wp_locale_code' => 'sah',
					'is_rtl' => false,
				),
				'sa_IN' => 
				array (
					'name' => 'Sanskrit',
					'native_name' => 'भारतम्',
					'locale_code' => 'sa-in',
					'wp_locale_code' => 'sa_IN',
					'is_rtl' => false,
				),
				'srd' => 
				array (
					'name' => 'Sardinian',
					'native_name' => 'Sardu',
					'locale_code' => 'srd',
					'wp_locale_code' => 'srd',
					'is_rtl' => false,
				),
				'gd' => 
				array (
					'name' => 'Scottish Gaelic',
					'native_name' => 'Gàidhlig',
					'locale_code' => 'gd',
					'wp_locale_code' => 'gd',
					'is_rtl' => false,
				),
				'sr_RS' => 
				array (
					'name' => 'Serbian',
					'native_name' => 'Српски језик',
					'locale_code' => 'sr',
					'wp_locale_code' => 'sr_RS',
					'is_rtl' => false,
				),
				'szl' => 
				array (
					'name' => 'Silesian',
					'native_name' => 'Ślōnskŏ gŏdka',
					'locale_code' => 'szl',
					'wp_locale_code' => 'szl',
					'is_rtl' => false,
				),
				'snd' => 
				array (
					'name' => 'Sindhi',
					'native_name' => 'سنڌي',
					'locale_code' => 'snd',
					'wp_locale_code' => 'snd',
					'is_rtl' => false,
				),
				'si_LK' => 
				array (
					'name' => 'Sinhala',
					'native_name' => 'සිංහල',
					'locale_code' => 'si',
					'wp_locale_code' => 'si_LK',
					'is_rtl' => false,
				),
				'sk_SK' => 
				array (
					'name' => 'Slovak',
					'native_name' => 'Slovenčina',
					'locale_code' => 'sk',
					'wp_locale_code' => 'sk_SK',
					'is_rtl' => false,
				),
				'sl_SI' => 
				array (
					'name' => 'Slovenian',
					'native_name' => 'Slovenščina',
					'locale_code' => 'sl',
					'wp_locale_code' => 'sl_SI',
					'is_rtl' => false,
				),
				'so_SO' => 
				array (
					'name' => 'Somali',
					'native_name' => 'Afsoomaali',
					'locale_code' => 'so',
					'wp_locale_code' => 'so_SO',
					'is_rtl' => false,
				),
				'azb' => 
				array (
					'name' => 'South Azerbaijani',
					'native_name' => 'گؤنئی آذربایجان',
					'locale_code' => 'azb',
					'wp_locale_code' => 'azb',
					'is_rtl' => false,
				),
				'es_AR' => 
				array (
					'name' => 'Spanish (Argentina)',
					'native_name' => 'Español de Argentina',
					'locale_code' => 'es-ar',
					'wp_locale_code' => 'es_AR',
					'is_rtl' => false,
				),
				'es_CL' => 
				array (
					'name' => 'Spanish (Chile)',
					'native_name' => 'Español de Chile',
					'locale_code' => 'es-cl',
					'wp_locale_code' => 'es_CL',
					'is_rtl' => false,
				),
				'es_CO' => 
				array (
					'name' => 'Spanish (Colombia)',
					'native_name' => 'Español de Colombia',
					'locale_code' => 'es-co',
					'wp_locale_code' => 'es_CO',
					'is_rtl' => false,
				),
				'es_GT' => 
				array (
					'name' => 'Spanish (Guatemala)',
					'native_name' => 'Español de Guatemala',
					'locale_code' => 'es-gt',
					'wp_locale_code' => 'es_GT',
					'is_rtl' => false,
				),
				'es_MX' => 
				array (
					'name' => 'Spanish (Mexico)',
					'native_name' => 'Español de México',
					'locale_code' => 'es-mx',
					'wp_locale_code' => 'es_MX',
					'is_rtl' => false,
				),
				'es_PE' => 
				array (
					'name' => 'Spanish (Peru)',
					'native_name' => 'Español de Perú',
					'locale_code' => 'es-pe',
					'wp_locale_code' => 'es_PE',
					'is_rtl' => false,
				),
				'es_PR' => 
				array (
					'name' => 'Spanish (Puerto Rico)',
					'native_name' => 'Español de Puerto Rico',
					'locale_code' => 'es-pr',
					'wp_locale_code' => 'es_PR',
					'is_rtl' => false,
				),
				'es_ES' => 
				array (
					'name' => 'Spanish (Spain)',
					'native_name' => 'Español',
					'locale_code' => 'es',
					'wp_locale_code' => 'es_ES',
					'is_rtl' => false,
				),
				'es_VE' => 
				array (
					'name' => 'Spanish (Venezuela)',
					'native_name' => 'Español de Venezuela',
					'locale_code' => 'es-ve',
					'wp_locale_code' => 'es_VE',
					'is_rtl' => false,
				),
				'su_ID' => 
				array (
					'name' => 'Sundanese',
					'native_name' => 'Basa Sunda',
					'locale_code' => 'su',
					'wp_locale_code' => 'su_ID',
					'is_rtl' => false,
				),
				'sw' => 
				array (
					'name' => 'Swahili',
					'native_name' => 'Kiswahili',
					'locale_code' => 'sw',
					'wp_locale_code' => 'sw',
					'is_rtl' => false,
				),
				'sv_SE' => 
				array (
					'name' => 'Swedish',
					'native_name' => 'Svenska',
					'locale_code' => 'sv',
					'wp_locale_code' => 'sv_SE',
					'is_rtl' => false,
				),
				'gsw' => 
				array (
					'name' => 'Swiss German',
					'native_name' => 'Schwyzerdütsch',
					'locale_code' => 'gsw',
					'wp_locale_code' => 'gsw',
					'is_rtl' => false,
				),
				'tl' => 
				array (
					'name' => 'Tagalog',
					'native_name' => 'Tagalog',
					'locale_code' => 'tl',
					'wp_locale_code' => 'tl',
					'is_rtl' => false,
				),
				'tah' => 
				array (
					'name' => 'Tahitian',
					'native_name' => 'Reo Tahiti',
					'locale_code' => 'tah',
					'wp_locale_code' => 'tah',
					'is_rtl' => false,
				),
				'tg' => 
				array (
					'name' => 'Tajik',
					'native_name' => 'Тоҷикӣ',
					'locale_code' => 'tg',
					'wp_locale_code' => 'tg',
					'is_rtl' => false,
				),
				'tzm' => 
				array (
					'name' => 'Tamazight (Central Atlas)',
					'native_name' => 'ⵜⴰⵎⴰⵣⵉⵖⵜ',
					'locale_code' => 'tzm',
					'wp_locale_code' => 'tzm',
					'is_rtl' => false,
				),
				'ta_IN' => 
				array (
					'name' => 'Tamil',
					'native_name' => 'தமிழ்',
					'locale_code' => 'ta',
					'wp_locale_code' => 'ta_IN',
					'is_rtl' => false,
				),
				'ta_LK' => 
				array (
					'name' => 'Tamil (Sri Lanka)',
					'native_name' => 'தமிழ்',
					'locale_code' => 'ta-lk',
					'wp_locale_code' => 'ta_LK',
					'is_rtl' => false,
				),
				'tt_RU' => 
				array (
					'name' => 'Tatar',
					'native_name' => 'Татар теле',
					'locale_code' => 'tt',
					'wp_locale_code' => 'tt_RU',
					'is_rtl' => false,
				),
				'te' => 
				array (
					'name' => 'Telugu',
					'native_name' => 'తెలుగు',
					'locale_code' => 'te',
					'wp_locale_code' => 'te',
					'is_rtl' => false,
				),
				'th' => 
				array (
					'name' => 'Thai',
					'native_name' => 'ไทย',
					'locale_code' => 'th',
					'wp_locale_code' => 'th',
					'is_rtl' => false,
				),
				'bo' => 
				array (
					'name' => 'Tibetan',
					'native_name' => 'བོད་སྐད',
					'locale_code' => 'bo',
					'wp_locale_code' => 'bo',
					'is_rtl' => false,
				),
				'tir' => 
				array (
					'name' => 'Tigrinya',
					'native_name' => 'ትግርኛ',
					'locale_code' => 'tir',
					'wp_locale_code' => 'tir',
					'is_rtl' => false,
				),
				'tr_TR' => 
				array (
					'name' => 'Turkish',
					'native_name' => 'Türkçe',
					'locale_code' => 'tr',
					'wp_locale_code' => 'tr_TR',
					'is_rtl' => false,
				),
				'tuk' => 
				array (
					'name' => 'Turkmen',
					'native_name' => 'Türkmençe',
					'locale_code' => 'tuk',
					'wp_locale_code' => 'tuk',
					'is_rtl' => false,
				),
				'twd' => 
				array (
					'name' => 'Tweants',
					'native_name' => 'Twents',
					'locale_code' => 'twd',
					'wp_locale_code' => 'twd',
					'is_rtl' => false,
				),
				'ug_CN' => 
				array (
					'name' => 'Uighur',
					'native_name' => 'Uyƣurqə',
					'locale_code' => 'ug',
					'wp_locale_code' => 'ug_CN',
					'is_rtl' => false,
				),
				'uk' => 
				array (
					'name' => 'Ukrainian',
					'native_name' => 'Українська',
					'locale_code' => 'uk',
					'wp_locale_code' => 'uk',
					'is_rtl' => false,
				),
				'ur' => 
				array (
					'name' => 'Urdu',
					'native_name' => 'اردو',
					'locale_code' => 'ur',
					'wp_locale_code' => 'ur',
					'is_rtl' => true,
				),
				'uz_UZ' => 
				array (
					'name' => 'Uzbek',
					'native_name' => 'O‘zbekcha',
					'locale_code' => 'uz',
					'wp_locale_code' => 'uz_UZ',
					'is_rtl' => false,
				),
				'vi' => 
				array (
					'name' => 'Vietnamese',
					'native_name' => 'Tiếng Việt',
					'locale_code' => 'vi',
					'wp_locale_code' => 'vi',
					'is_rtl' => false,
				),
				'wa' => 
				array (
					'name' => 'Walloon',
					'native_name' => 'Walon',
					'locale_code' => 'wa',
					'wp_locale_code' => 'wa',
					'is_rtl' => false,
				),
				'cy' => 
				array (
					'name' => 'Welsh',
					'native_name' => 'Cymraeg',
					'locale_code' => 'cy',
					'wp_locale_code' => 'cy',
					'is_rtl' => false,
				),
				'yor' => 
				array (
					'name' => 'Yoruba',
					'native_name' => 'Yorùbá',
					'locale_code' => 'yor',
					'wp_locale_code' => 'yor',
					'is_rtl' => false,
				),
		);
		return $wt_pklist_language_list;
	}

	/**
	 * To add the confirmation popup in order details and order listing page
	 *
	 * @return void
	 */
	public function wt_pklist_popup_on_order_edit_page(){
		global $pagenow, $post;
		$show_popup = false;
		if(Wt_Pklist_Common::is_wc_hpos_enabled()){
			// order listing page and order edit page will have these url parameters
			if('admin.php' === $pagenow && isset($_GET['page']) && "wc-orders" === $_GET['page']){
				$show_popup = true;
			}
		}else{
			if('edit.php' === $pagenow && (isset($_GET['post_type']) && "shop_order" === $_GET['post_type'])){
				$show_popup = true;
			}elseif('post.php' === $pagenow){
				$req_type = "";
				if(isset($_GET['post']) && !empty($_GET['post'])){
					$post = get_post($_GET['post']);
					$req_type = $post->post_type;
				}
				if("shop_order" == $req_type){
					$show_popup = true;
				}
			}
		}
		if(true === $show_popup){
			$data = '<div class="wt_doc_create_confirm_popup wf_pklist_popup" style="width:40%;text-align:left;">
					<div style="float:left;padding:20px;">
					<div class="wt_doc_create_confirm_popup_main wf_pklist_popup_body">
						<div class="message" style="float:left; box-sizing:border-box; width:100%; padding:0px 5px; margin-bottom:15px;">
						</div>
						<div id="wt_dont_show_again_doc_create_div" style="float: left;box-sizing: border-box;width: 100%;padding: 0px 5px;margin-bottom: 5px;">
							<input type="checkbox" id="wt_dont_show_again_doc_create"> '.__("Do not show again","print-invoices-packing-slip-labels-for-woocommerce").'
						</div>
					</div>
					
					<div class="wt_doc_create_confirm_popup_footer wf_pklist_popup_footer" style="float:left;">
						<button type="button" name="" class="button-secondary wf_pklist_popup_cancel" style="color: #3157A6;border-color: #3157A6;">
							'.__("Cancel","print-invoices-packing-slip-labels-for-woocommerce").'
						</button>
						<button type="button" name="" class="button-primary wt_doc_create_confirm_popup_yes" style="background: #3157A6;">
							'.__("Generate","print-invoices-packing-slip-labels-for-woocommerce").'
						</button>	
					</div>
					</div>
				</div>
				<div class="wt_pklist_document_generating_popup wf_pklist_popup" style="width:40%;text-align:left;">
					<div class="wt_doc_create_confirm_popup_main_loading">
						<div class="loading_message">'.__("Generating the document...", "print-invoices-packing-slip-labels-for-woocommerce").'</div>
					</div>
					<button type="button" name="" class="button-secondary wf_pklist_popup_cancel" style="">
							'.__("Cancel","print-invoices-packing-slip-labels-for-woocommerce").'
						</button>
				</div>';
			echo $data;

			$data_ublinvoice = '<div class="wt_doc_create_confirm_popup_ublinvoice wf_pklist_popup" style="width:40%;text-align:left;">
					<div style="float:left;padding:20px;">
					<div class="wt_doc_create_confirm_popup_main_ublinvoice wf_pklist_popup_body">
						<div class="message" style="float:left; box-sizing:border-box; width:100%; padding:0px 5px; margin-bottom:15px;">
						</div>
						<div id="wt_dont_show_again_doc_create_div_ublinvoice" style="float: left;box-sizing: border-box;width: 100%;padding: 0px 5px;margin-bottom: 5px;">
							<input type="checkbox" id="wt_dont_show_again_doc_create_ublinvoice"> '.__("Do not show again","print-invoices-packing-slip-labels-for-woocommerce").'
						</div>
					</div>
					
					<div class="wt_doc_create_confirm_popup_ublinvoice_footer wf_pklist_popup_footer" style="float:left;">
						<button type="button" name="" class="button-secondary wf_pklist_popup_cancel" style="color: #3157A6;border-color: #3157A6;">
							'.__("Cancel","print-invoices-packing-slip-labels-for-woocommerce").'
						</button>
						<button type="button" name="" class="button-primary wt_doc_create_confirm_popup_yes_ublinvoice" style="background: #3157A6;">
							'.__("Generate","print-invoices-packing-slip-labels-for-woocommerce").'
						</button>	
					</div>
					</div>
				</div>';
			echo $data_ublinvoice;
		}
	}

	/**
	 * To check wt plugins active using its key
	 *
	 * @param string $plugin_key
	 * @return boolean
	 */
	public static function wt_plugin_active($plugin_key=""){
		if("" === $plugin_key){
			return false;
		}
		$file_path = Wf_Woocommerce_Packing_List_Pro_Addons::get_file_path_by_addon_key($plugin_key);
		return ("" !== $file_path && false !== $file_path) ? is_plugin_active($file_path) : false;
	}

	public static function check_doc_already_created($order,$order_id,$template_type){
		$order_docs 	= Wt_Pklist_Common::get_order_meta($order_id,'_created_document', true);
		$order_docs_old = Wt_Pklist_Common::get_order_meta($order_id,'_created_document_old', true);
		if(!empty($order_docs) && is_array($order_docs) && in_array($template_type,$order_docs)){
			return true;
		}elseif(!empty($order_docs_old) && is_array($order_docs_old) && in_array($template_type,$order_docs_old)){
			return true;
		}
		return false;
	}

	public function wt_pklist_cta_banner_dismiss(){
		$nonce	= isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : ''; 
		if(!(wp_verify_nonce($nonce,WF_PKLIST_PLUGIN_NAME)))
		{
			$result = array('status' => false, 'message' => __("You are not allowed to do this action","print-invoices-packing-slip-labels-for-woocommerce"));
			echo json_encode($result);
			exit();
		}
		$banner_option	= get_option('wt_pklist_dismissible_banners');
		$banner_class	= isset($_POST['banner_class']) ? sanitize_text_field($_POST['banner_class']) : "";
		$banner_action 	= isset($_POST['banner_action']) ? sanitize_text_field($_POST['banner_action']) : 0;
		$banner_interval	= isset($_POST['banner_interval']) ? sanitize_text_field($_POST['banner_interval']): 0;

		if(!empty($banner_class)){
			$banner_option[$banner_class] = array(
				'class'			=> $banner_class,
				'status' 		=> $banner_action,
				'last_action' 	=> time(),
				'interval'		=> $banner_interval 
			);
			update_option('wt_pklist_dismissible_banners',$banner_option);
			$result = array('status' => true, 'message' => __("Success","print-invoices-packing-slip-labels-for-woocommerce"));
		}else{
			$result = array('status' => false, 'message' => __("Cannot close this banner","print-invoices-packing-slip-labels-for-woocommerce"));
		}
		echo json_encode($result);
		exit();
	}

	/**
	 * To add the meta box for debugging in order details page
	 * 
	 * @since 4.1.3
	 * @since 4.4.1 - [Fix] - Security fixes
	 * @return void
	 */
	public function wt_pklist_debug_metabox_content($post_or_order_object){
		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		$debug_result_final = array();
		$result = '';
		if ( ! empty( $order ) && isset( $_GET['wt-pklist-debug'] ) && 1 === (int)$_GET['wt-pklist-debug'] ) {	
			$debug_result_final['order_details'] = $order;
			$result = '<div style="overflow-x:auto;">';
			$result .= '<pre>' . wp_kses_post( print_r( $debug_result_final, true ) ) . '</pre>';
			$result .= '</div>';
		}

		echo wp_kses_post( $result );
	}

	/**
	 * To get all the options of this plugin stored in database
	 *
	 * @since 4.1.3
	 * @return array
	 */
	public static function get_all_option_of_this_plugin(){
		$options = array(
			// initial installation
			'wt_pklist_common_modules',
			'wt_pklist_admin_modules',
			'wt_pklist_languages_list',
			'wfpklist_basic_version_prev',
			'wfpklist_basic_version',
			'wt_pklist_installation_date',
			'wt_pklist_pro_installation_date',
			'wt_pklist_ipc_version',
			'wt_pklist_sdd_version',
			'wt_pklist_pl_version',
			'wt_pklist_pi_version',
			'wt_pklist_al_version',
			'wf_pklist_module_status_migrated',
			'wf_pklist_templates_migrated',
			'invoice_empty_count',
			'wt_created_document_count',
			
			// Document type option
			'Wf_Woocommerce_Packing_List',
			'wf_woocommerce_packing_list_invoice',
			'wf_woocommerce_packing_list_packinglist',
			'wf_woocommerce_packing_list_deliverynote',
			'wf_woocommerce_packing_list_dispatchlabel',
			'wf_woocommerce_packing_list_shippinglabel',
			'wf_woocommerce_packing_list_creditnote',
			'wf_woocommerce_packing_list_picklist',
			'wf_woocommerce_packing_list_proformainvoice',
			
			// Documents advanced option
			'woocommerce_wf_pay_later_settings',
			'woocommerce_wt_pklist_packinglist_email_settings',
			'woocommerce_wt_pklist_picklist_email_settings',

			// other addon options
			'wf_woocommerce_packing_list_printnode',
			'wt_pklist_printnode_default_printer',
			'wt_pklist_printnode_default_printer_name',
			'wf_woocommerce_packing_list_qrcode_invoice',
			'wt_pklist_qrcode_common_modules',

			'wt_mpdf_review_request',
			'wt_pklist_review_request',
		);
		return $options;
	}

	/**
	 * Ajax function to export the plugin settings and templates as a json file from debug module
	 *
	 * @since 4.1.3
	 * @since 4.4.3 - [Fix] - Added nonce verification and role capability check
	 * @return void
	 */
	public function wt_pklist_settings_json(){
		$export_module_nonce = $_POST['_wpnonce'] ? sanitize_text_field( $_POST['_wpnonce'] ) : '';
		if( !wp_verify_nonce( $export_module_nonce, WF_PKLIST_PLUGIN_NAME . '_debug_export_form' ) || !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) {
			echo json_encode( array( 'error' => __('You are not allowed to do this action', 'print-invoices-packing-slip-labels-for-woocommerce' ) ) );
			die;
		}

		if(Wf_Woocommerce_Packing_List_Admin::check_role_access()) 
    	{
			$options = self::get_all_option_of_this_plugin();
			$response = array('wt_pklist_key' => 'wt_pklist','options' => array(),'wfpklist_template_data' => array());
			if(is_array($options)){
				foreach($options as $option){
					if(false !== get_option($option)){
						$response['options'][$option] = get_option($option);
					}
				}
			}

			global $wpdb;
			$table_name=$wpdb->prefix.Wf_Woocommerce_Packing_List::$template_data_tb;
			$qry=$wpdb->prepare("SELECT * FROM $table_name");
			$templates = $wpdb->get_results($qry);
			foreach($templates as $temp){
				$response['wfpklist_template_data'][] = array(
					'template_name' => $temp->template_name,
					'template_html' => $temp->template_html,
					'template_from' => $temp->template_from, 
					'is_dc_compatible' => $temp->is_dc_compatible,
					'is_active'		=> $temp->is_active,
					'template_type' => $temp->template_type,
					'created_at'	=> $temp->created_at,
					'updated_at'	=> $temp->updated_at,
				);
			}
			$result = array( 'success' => true, 'response' => $response );
			echo json_encode($result);	
			die;
		}
	}

	/**
	 * To import the plugin settings from the json file
	 *
	 * @since 4.1.3
	 * @since 4.3.0 - [Fix] - Update the options other than the plugin settings from the user customized json file issue
	 * @since 4.4.3 - [Fix] - Added nonce verification and role capability check
	 * @return void
	 */
	public function wt_pklist_import_settings(){
		if(isset($_POST['wt_pklist_settings_import_confirm_text']))
		{
			if("confirm" === $_POST['wt_pklist_settings_import_confirm_text']){
				$import_module_nonce = isset( $_POST['_wtpdf_debug_settings_import_nonce'] ) ? sanitize_text_field( $_POST['_wtpdf_debug_settings_import_nonce'] ) : '';
 				if( !wp_verify_nonce( $import_module_nonce, WF_PKLIST_PLUGIN_NAME . '_debug_import_form' ) || !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) {
					self::wt_pklist_safe_redirect_or_die( null, __( 'You are not allowed to do this action', 'print-invoices-packing-slip-labels-for-woocommerce') );
				} else {
					$template_import = isset($_POST['template_import']) ? sanitize_text_field($_POST['template_import']) : 'append';
					require_once(ABSPATH . 'wp-load.php');
					if ( $_FILES['wt_pklist_import_setting_file']['error'] === UPLOAD_ERR_OK ) {
						$file = $_FILES['wt_pklist_import_setting_file']['tmp_name'];
						// Read the contents of the file
						$contents = file_get_contents($file);
					
						// Parse the JSON data
						$data = json_decode($contents, true);
						$error_code = 0;
						$error_message = '';
						if ($data !== null) {
							if(!empty($data)){
								if(3 === count($data) && isset($data['wt_pklist_key']) && "wt_pklist" === $data['wt_pklist_key'] && isset($data['options']) && isset($data['wfpklist_template_data'])){
									foreach($data as $data_key => $value){
										if("options" === $data_key){
											$all_settings = self::get_all_option_of_this_plugin();
											if(is_array($value) && !empty($value)){
												foreach($value as $meta_key => $meta_value){
													if(in_array($meta_key,$all_settings)){ // check the meta key is related to this plugin settings
														update_option($meta_key,$meta_value);
													}
												}
											}else{
												$error_message 	= __("Settings are empty","print-invoices-packing-slip-labels-for-woocommerce");
												$error_code		= 5;
												break;
											}
										}elseif("wfpklist_template_data" === $data_key){
											if(is_array($value) && !empty($value)){
												global $wpdb;
												$table_name=$wpdb->prefix.Wf_Woocommerce_Packing_List::$template_data_tb;
												if('override' === $template_import){
													$wpdb->query("TRUNCATE TABLE $table_name");
												}
												foreach($value as $row_key => $row_val){
													$search_template_name = $wpdb->get_row($wpdb->prepare("SELECT `id_wfpklist_template_data` from $table_name WHERE `template_name` LIKE %s AND `template_type` LIKE %s",array(esc_sql($row_val['template_name']),esc_sql($row_val['template_type']))));
													if(!$search_template_name){
														$template_name = $row_val['template_name'];
													}else{
														$template_name = $row_val['template_name'].'_'.time();
													}

													$search_is_active = $wpdb->get_row($wpdb->prepare("SELECT `id_wfpklist_template_data` from $table_name WHERE `is_active` = %d AND `template_type` LIKE %s",array(esc_sql($row_val['is_active']),esc_sql($row_val['template_type']))));
													$is_active	= (!$search_is_active) ? $row_val['is_active'] : 0;
													$insert_data=array(
														'template_name'	=> $template_name,
														'template_html'	=> $row_val['template_html'],
														'template_from'	=> $row_val['template_from'],
														'template_type'	=> $row_val['template_type'],
														'is_dc_compatible' => $row_val['is_dc_compatible'],
														'created_at'	=> $row_val['created_at'],
														'updated_at'	=> $row_val['updated_at'],
														'is_active'		=>  $is_active
													);
													$insert_data_type=array(
														'%s','%s','%d','%s','%d','%d','%d','%d'
													); 
													$wpdb->insert($table_name,$insert_data,$insert_data_type);
												}
											}
										}
									}
									if(0 === $error_code){
										$error_message = __("Imported successfully","print-invoices-packing-slip-labels-for-woocommerce");
										update_option('wt_pklist_import_date',time());
									}
								}else{
									$error_message 	= __("Incorrect file","print-invoices-packing-slip-labels-for-woocommerce");
									$error_code 	= 4;	
								}
							}else{
								$error_message 	= __("Error: Empty JSON file","print-invoices-packing-slip-labels-for-woocommerce");
								$error_code 	= 3;
							}
						} else {
							$error_message 	= __("Error: Invalid JSON data","print-invoices-packing-slip-labels-for-woocommerce");
							$error_code 	= 2;
						}
					} else {
						$error_message 	= __("Error uploading the file","print-invoices-packing-slip-labels-for-woocommerce");
						$error_message .= $_FILES['file']['error'];
						$error_code		= 1;
					}

					$logger_res_array = array(
						'message' 		=> $error_message,
						'imported_data' => $data,
						'error_code'	=> $error_code,
						'import_date'	=> time(),
					);

					$track_log          = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || apply_filters( 'wt_pklist_debug_enable_translation_log', false );	
					if ( function_exists( 'wc_get_logger' ) && ! empty( $logger_res_array ) && true === $track_log ) {
						$logger = wc_get_logger();
						$logger->info( wc_print_r( $logger_res_array, true ), array( 'source' => 'wt_pklist_import' ) );
					}
					$_POST['wt_status'] = $error_code;
					$_POST['wt_status_message'] = $error_message;
				}
			}
		}
	}

	/**
	 * Ajax function to reset the plugin settings and templates from debug module
	 *
	 * @since 4.1.3
	 * @since 4.4.3 - [Fix] - Added nonce verification and role capability check
	 * @return void
	 */
	 public function wt_pklist_reset_settings(){

		if(isset($_POST['wt_pklist_settings_reset_confirm_text'])){

			$reset_nonce	= isset( $_POST['_wtpdf_debug_settings_reset_nonce'] ) ? sanitize_text_field( $_POST['_wtpdf_debug_settings_reset_nonce'] ) : '';
			if( !wp_verify_nonce( $reset_nonce, WF_PKLIST_PLUGIN_NAME . '_debug_reset_form' ) || !Wf_Woocommerce_Packing_List_Admin::check_role_access() ) { // added nonce verification and capability check
				
				self::wt_pklist_safe_redirect_or_die( null, __( 'You are not allowed to do this action', 'print-invoices-packing-slip-labels-for-woocommerce') );	

			} else {

				require_once WF_PKLIST_PLUGIN_PATH . 'includes/class-wf-woocommerce-packing-list-activator.php';
				require_once plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/modules/migrator/migrator.php"; 
				$options = self::get_all_option_of_this_plugin();
				
				foreach($options as $option){
					delete_option($option);
				}

				if(!isset($_POST['dont_reset_template'])){
					global $wpdb;
					$table_name=$wpdb->prefix.Wf_Woocommerce_Packing_List::$template_data_tb;
					$delete_the_template = $wpdb->query("TRUNCATE TABLE $table_name");
				}else{
					$delete_the_template = 0;
					update_option('wf_pklist_templates_migrated',1);
				}
		
				// reset to default settings
				Wf_Woocommerce_Packing_List_Activator::install_tables();
				Wf_Woocommerce_Packing_List_Activator::copy_address_from_woo();
				Wf_Woocommerce_Packing_List_Activator::save_plugin_version();
				Wf_Woocommerce_Packing_List_Migrator::migrate();
				$this->admin_modules();
				$public_obj = new Wf_Woocommerce_Packing_List_Public( $this->plugin_name, $this->version );
				$public_obj->common_modules();
				
				update_option('wt_pklist_reset_date',time());
				if(false === $delete_the_template)
				{
					$_POST['wt_status_message'] =  __("Error:","print-invoices-packing-slip-labels-for-woocommerce") . $wpdb->last_error.__("Truncation did not complete as expected.","print-invoices-packing-slip-labels-for-woocommerce");
					$_POST['wt_reset_status'] = 0;
				}
				else
				{
					$_POST['wt_reset_status'] = 1;
				}
			}
		}
	}

	/**
	 * To schedule the action to save the default templates for the documents
	 *
	 * @return void
	 */
	public function wt_pklist_action_scheduler_for_saving_default_templates(){
		$group = "wt_pklist_save_default_templates_group";
		if(false === as_next_scheduled_action( 'wt_pklist_save_default_templates' )){
			as_schedule_recurring_action( time(), 604800, 'wt_pklist_save_default_templates', array(), $group );
		}else{
			if (as_next_scheduled_action('wt_pklist_save_default_templates', array(), $group) === true) {
	            as_unschedule_all_actions('wt_pklist_save_default_templates', array(), $group);
	        }
		}
	}

	/**
	 * Call back function for the action scheduler to save the default template
	 *
	 * @return void
	 */
	public function wt_pklist_save_default_templates_func(){
		$set_default_templates_save = get_option('wt_pklist_save_default_templates');
		if(false === $set_default_templates_save || 0 === absint($set_default_templates_save)){
			$wt_pklist_common_modules=get_option('wt_pklist_common_modules');
			if(!empty($wt_pklist_common_modules)){
				$customizer_obj = Wf_Woocommerce_Packing_List::load_modules('customizer'); 
				foreach($wt_pklist_common_modules as $base => $base_val){
					$customizer_obj->save_default_template($base);
				}
				update_option('wt_pklist_save_default_templates',1);
			}
		}
	}

	/**
	*	@since 4.1.3
	* 	Recursively calculating and retriveing total files in the plugin temp directory
	*
	*/
	public static function get_total_temp_files($document_wise = false)
	{
		$doc_arr = array(
			'total_files'	=> __("Total files","print-invoices-packing-slip-labels-for-woocommerce"),
			'invoice' 		=> __("Invoice","print-invoices-packing-slip-labels-for-woocommerce"),
			'packinglist'	=> __("Packing slip","print-invoices-packing-slip-labels-for-woocommerce"),
			'shippinglabel' => __("Shipping label","print-invoices-packing-slip-labels-for-woocommerce"),
			'deliverynote' 	=> __("Delivery note","print-invoices-packing-slip-labels-for-woocommerce"),
			'dispatchlabel'	=> __("Dispatch label","print-invoices-packing-slip-labels-for-woocommerce"),
			'picklist'		=> __("Picklist","print-invoices-packing-slip-labels-for-woocommerce"),
			'profomainvoice'=> __("Proforma invoice","print-invoices-packing-slip-labels-for-woocommerce"),
			'creditnote'	=> __("Credit note","print-invoices-packing-slip-labels-for-woocommerce"),
		);
		$temp_doc = array();
		foreach($doc_arr as $doc_key => $doc){
			$temp_doc[$doc_key] = array(
				'label' => $doc,
				'pdf'	=> 0,
				'html'	=> 0,
				'total_file_count' => 0,
			);
		}
		$file_count=0;
		foreach($temp_doc as $temp_doc_key => $temp_doc_data){
			$upload_dir=Wf_Woocommerce_Packing_List::get_temp_dir('path');
			if("total_files" !== $temp_doc_key){
				$upload_dir .= '/'.$temp_doc_key;
			}
			
			if("total_files" !== $temp_doc_key && false === apply_filters('wt_pklist_show_document_temp_files_'.$temp_doc_key,false,$temp_doc_key)){
				continue;
			}

			if(is_dir($upload_dir))
			{
				$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($upload_dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::LEAVES_ONLY);		
				foreach($files as $name=>$file)
				{
					if(!$file->isDir())
					{
						$file_name=$file->getFilename();
						$file_ext_arr=explode('.', $file_name);
						$file_ext=end($file_ext_arr);
						if(($file_ext==='pdf')) //we are creating pdf files as temp files
						{
							$temp_doc[$temp_doc_key]['pdf']++;
							$temp_doc[$temp_doc_key]['total_file_count']++;
							$file_count++;
						}elseif($file_ext==='html'){
							$temp_doc[$temp_doc_key]['html']++;
							$temp_doc[$temp_doc_key]['total_file_count']++;
							$file_count++;
						}
					}
				} 
			}
		}
		return $temp_doc;
	}

	/**
	 * To download the template pdf file stored
	 *
	 * @since 4.1.3
	 * @return void
	 */
	public function download_all_temp()
	{
		$out=array('status'=>0, 'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'), 'fileurl'=>'');

		// Check permission
	    if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
		{
	    	echo json_encode($out);
	    	exit();
	    } 

		$zip 		= new ZipArchive();
		$doc_type 	= isset($_POST['doc_type']) ? sanitize_text_field($_POST['doc_type']) : 'total_files';
		$backup_dir	= Wf_Woocommerce_Packing_List::get_temp_dir('path');
		$backup_url	= Wf_Woocommerce_Packing_List::get_temp_dir('url');
		$doc_file_loc = Wf_Woocommerce_Packing_List::get_temp_dir('path');
		$backup_file_name	= 'wt_pklist_temp_backup.zip';
		if("total_files" !== $doc_type){
			$backup_file_name ='wt_pklist_temp_backup_'.$doc_type.'.zip';
			$doc_file_loc = Wf_Woocommerce_Packing_List::get_temp_dir('path').'/'.$doc_type;
		}
		$backup_file=$backup_dir.'/'.$backup_file_name;
		$backup_file_url=$backup_url.'/'.$backup_file_name;

        $zip->open($backup_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        if(is_dir($doc_file_loc))
		{
			$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($doc_file_loc),RecursiveIteratorIterator::LEAVES_ONLY);
			foreach($files as $name=>$file)
			{
				// Skip directories (they would be added automatically if not empty)
				if(!$file->isDir())
				{
					$file_name=$file->getFilename();
					$file_ext_arr=explode('.', $file_name);
					$file_ext=end($file_ext_arr);
					if(($file_ext==='pdf') || ($file_ext==='html')) //we are creating pdf files as temp files
					{
						$filePath=$file->getRealPath();
						$relativePath=substr($filePath, strlen($backup_dir)+1);			
						$zip->addFile($filePath, basename($backup_dir).'/'.$relativePath);
					}        				
				}
			}
		}
		$zip->close();

		$out['status']=1;
		$out['msg']='';
		$out['fileurl']=html_entity_decode(wp_nonce_url(admin_url('admin.php?wt_pklist_download_temp_zip=true&file='.$backup_file_name), WF_PKLIST_PLUGIN_NAME));
		echo json_encode($out);
		exit();
	}

	/**
	*  	Download temp zip file via a nonce URL
	*	@since 4.0.6
	*/
	public function download_temp_zip_file()
	{
		if(isset($_GET['wt_pklist_download_temp_zip']))
		{
			if(self::check_write_access()) /* check nonce and role */
			{
				$file_name=(isset($_GET['file']) ? sanitize_text_field($_GET['file']) : '');
				if($file_name!="")
				{
					$file_arr=explode(".", $file_name);
					$file_ext=end($file_arr);
					if($file_ext=='zip') /* only zip files */
					{
						$backup_dir=Wf_Woocommerce_Packing_List::get_temp_dir('path');
						$file_path=$backup_dir.'/'.$file_name;
						if(file_exists($file_path)) /* check existence of file */
						{							
							header('Pragma: public');
						    header('Expires: 0');
						    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						    header('Cache-Control: private', false);
						    header('Content-Transfer-Encoding: binary');
						    header('Content-Disposition: attachment; filename="'.$file_name.'";');
						    header('Content-Type: application/zip');
						    header('Content-Length: '.filesize($file_path));

						    $chunk_size=1024 * 1024;
						    $handle=@fopen($file_path, 'rb');
						    while(!feof($handle))
						    {
						        $buffer = fread($handle, $chunk_size);
						        echo $buffer;
						        ob_flush();
						        flush();
						    }
						    fclose($handle);
						    exit();
						}
					}
				}	
			}
		}
	}

	/**
	 * Ajax call back function for deleting files in the plugin temp directory
	 *
	 * @since 4.1.3
	 * @return void
	 */
	public function delete_all_temp()
	{
		$out=array('status'=>0, 'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'));

		// Check permission
	    if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
		{
	    	echo json_encode($out);
	    	exit();
	    }

		$doc_type 	= isset($_POST['doc_type']) ? sanitize_text_field($_POST['doc_type']) : 'total_files';
	    /* recrusively delete files */
	    $this->delete_temp_files_recursively($doc_type,false);
		$total_temp_files = self::get_total_temp_files(true);
		$html = '';
		foreach($total_temp_files as $doc_key => $doc_data){
			if("total_files" !== $doc_key && false === apply_filters('wt_pklist_show_document_temp_files_'.$doc_key,false,$doc_key)){
				continue;
			}
			if($doc_data['total_file_count'] > 0){
				$html .= '<tr><td>'.esc_html($doc_data["label"]).'</td><td>'.esc_html($doc_data["pdf"]).'</td><td>'.esc_html($doc_data["html"]).'</td><td class="action"><a class="wt_pklist_temp_files_btn" data-action="delete_all_temp" data-document="'.esc_attr($doc_key).'"><span class="dashicons dashicons-trash"></span></a><a class="wt_pklist_temp_files_btn" data-action="download_all_temp" data-document="'.esc_attr($doc_key).'"><span class="dashicons dashicons-download"></span></a></td></tr>';
			}else{
				$html .='<tr><td colspan="4">'.__("No temporary file found","print-invoices-packing-slip-labels-for-woocommerce").'</td></tr>';
			}
		}
		$out['table_html'] = $html;
		$out['status']=1;
		$out['msg']=__('Successfully cleared all temp files.', 'print-invoices-packing-slip-labels-for-woocommerce');
		$out['extra_msg']=__('No files found.', 'print-invoices-packing-slip-labels-for-woocommerce');
		echo json_encode($out);
		exit();
	}

	/**
	 * Function to delete the file stored recursively
	 *
	 * @since 4.1.3
	 * @param string $doc_type
	 * @param boolean $auto_clean
	 * @return void
	 */
	public function delete_temp_files_recursively($doc_type='total_files',$auto_clean=true)
	{
		$is_auto_clear	= Wf_Woocommerce_Packing_List::get_option('wt_pklist_auto_temp_clear');
		if(true === $auto_clean && "Yes" !== $is_auto_clear){
			$this->wt_pklist_action_scheduler_for_auto_cleanup();
			return;
		}
		$backup_dir=Wf_Woocommerce_Packing_List::get_temp_dir('path');
		$wt_backup_dir=Wf_Woocommerce_Packing_List::get_temp_dir('path');
		if("total_files" !== $doc_type){
			$backup_dir=Wf_Woocommerce_Packing_List::get_temp_dir('path').'/'.$doc_type;
		}
		if(is_dir($backup_dir))
		{
		    $files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($backup_dir), RecursiveIteratorIterator::LEAVES_ONLY);
			foreach($files as $name=>$file)
			{
				if(!$file->isDir())
				{
					$file_name=$file->getFilename();
					$file_ext_arr=explode('.', $file_name);
					$file_ext=end($file_ext_arr);
					if(($file_ext==='pdf') || ($file_ext==='zip') || ($file_ext==='html')) //temp pdf files and zip files
					{
						@unlink($file);
					}
				}
			}
		}

		if("total_files" !== $doc_type){
			if(is_dir($wt_backup_dir)){
				$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($wt_backup_dir), RecursiveIteratorIterator::LEAVES_ONLY);
				foreach($files as $name=>$file)
				{
					if(!$file->isDir())
					{
						$file_name=$file->getFilename();
						$file_ext_arr=explode('.', $file_name);
						$file_ext=end($file_ext_arr);
						if(($file_ext==='zip')) //temp pdf files and zip files
						{
							@unlink($file);
						}
					}
				}
			}
		}
	}

	/**
	 * Function to define the action scheduler for deleting the file automatically
	 *
	 * @return void
	 */
	public function wt_pklist_action_scheduler_for_auto_cleanup(){
		$group = "wt_pklist_clear_temp_files_group";
		$is_auto_clear	= Wf_Woocommerce_Packing_List::get_option('wt_pklist_auto_temp_clear');
		/* interval in minutes */
		$is_auto_clear_interval	= (int) Wf_Woocommerce_Packing_List::get_option('wt_pklist_auto_temp_clear_interval');
		$clear_interval = $is_auto_clear_interval * 60;
		if("Yes" === $is_auto_clear && $is_auto_clear_interval>0) //if auto clear enabled, and interval greater than zero
		{
			if ( false === as_next_scheduled_action( 'wt_pklist_temp_file_clear' ) ) {
					as_schedule_recurring_action( time(), $clear_interval, 'wt_pklist_temp_file_clear', array(), $group );
			}
		}elseif("No" === $is_auto_clear || 0 === $is_auto_clear_interval){
			as_unschedule_all_actions('wt_pklist_temp_file_clear', array(), $group);
		}
	}

	/**
	 * Safe redirect to the page or die
	 * 
	 * @since 4.2.0
	 * @param string $url
	 * @param string $message
	 * @return void
	 */
	public static function wt_pklist_safe_redirect_or_die( $url = '', $message = '' ) {
		if ( ! empty( $url ) ) {
			wp_safe_redirect( $url );
			exit;
		} else {
			wp_die( $message );
		}
	}

	/**
	 * Get the page url to get redirected, when document access denied
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public static function get_page_url_for_denied_document_access() {
		$redirect_url   = '';
		$redirect_page 	= Wf_Woocommerce_Packing_List::get_option('wt_pklist_document_access_denied_redirect_page');
		$redirect_page  = !empty( $redirect_page ) ? $redirect_page : 'myaccount_page';
		if ( !empty( $redirect_page ) ) {
			switch ( $redirect_page ) {
				case 'myaccount_page':
				default:
					$redirect_url = wp_sanitize_redirect( wc_get_page_permalink( 'myaccount' ) );
					break;
				case 'login_page':
					$redirect_url = wp_sanitize_redirect( wp_login_url() );
					break;
			}
		}
		return apply_filters( 'wt_pklist_alter_document_access_denied_redirect_page', $redirect_url );
	}
	
	/**
	 * To add the common print button on WC order listing action column
	 *
	 * @param object $order
	 * @return void
	 */
	public function add_common_print_button_in_wc_order_listing_action_column( $order ) {
		if ( !empty( $order ) ) {
			$order_id					= (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$enabled_common_print_btn 	= Wf_Woocommerce_Packing_List::get_option('wt_pklist_common_print_button_enable');
			$show_print_button			= current_user_can('seller') ? false : true;
			$show_print_button			= apply_filters('wt_pklist_show_document_common_print_button_action_column',$show_print_button);
			
			if( "Yes" === $enabled_common_print_btn && true === $show_print_button) {
				echo '<a class="button wc-action-button wc-action-button-wf_pklist_print_document wf_pklist_print_document" href="#'.esc_attr($order_id).'" aria-label="" title=""></a>';
				echo '<div id="wf_pklist_print_document-'.esc_attr($order_id).'" class="wf-pklist-print-tooltip-order-actions">				
					<div class="wf-pklist-print-tooltip-content">
						<ul>';
						$btn_arr=array();
						$btn_arr=apply_filters('wt_print_actions', $btn_arr, $order, $order_id, 'list_page');
						echo self::generate_print_button_html($btn_arr, $order, $order_id, 'list_page'); //generate buttons
						echo '</ul>
					</div>
					<div class="wf_arrow"></div>	
				</div>';
			}
		}
	}

	/**
	 * To save the form wizard for the first installation
	 *
	 * @since 4.2.0
	 * 
	 * @return void
	 */
	public function wt_pklist_form_wizard_save(){
		$out=array(
			'status'=>false,
			'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
		);
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
			$invoice_module_id = Wf_Woocommerce_Packing_List::get_module_id('invoice');
			$general_module_fields = array(
				'woocommerce_wf_packinglist_companyname',
				'woocommerce_wf_packinglist_sender_address_line1',
				'woocommerce_wf_packinglist_sender_address_line2',
				'woocommerce_wf_packinglist_sender_city',
				'wf_country',
				'woocommerce_wf_packinglist_sender_postalcode',
				'woocommerce_wf_packinglist_sender_contact_number',
				'woocommerce_wf_packinglist_sender_vat',
				'woocommerce_wf_packinglist_logo',
			);

			$invoice_module_fields = array(
				'wt_pdf_invoice_attachment_wc_email_classes' => 'wt_pdf_invoice_attachment_wc_email_classes',
				'woocommerce_wf_invoice_as_ordernumber' => 'woocommerce_wf_invoice_as_ordernumber_pdf_fw',
				'woocommerce_wf_invoice_number_format'	=> 'woocommerce_wf_invoice_number_format_pdf_fw',
				'woocommerce_wf_invoice_number_prefix' 	=> 'woocommerce_wf_invoice_number_prefix_pdf_fw',
				'woocommerce_wf_invoice_number_postfix' => 'woocommerce_wf_invoice_number_postfix_pdf_fw',
				'woocommerce_wf_invoice_start_number' 	=> 'woocommerce_wf_invoice_start_number_pdf_fw',
				'woocommerce_wf_invoice_padding_number' => 'woocommerce_wf_invoice_padding_number_pdf_fw',
				'woocommerce_wf_Current_Invoice_number_pdf' => 'woocommerce_wf_Current_Invoice_number_pdf_fw',
			);

			foreach($general_module_fields as $g_key){
				$val = isset($_POST[$g_key]) ? sanitize_text_field($_POST[$g_key]) : '';
				Wf_Woocommerce_Packing_List::update_option($g_key,$val);
			}

			foreach($invoice_module_fields as $i_key => $i_post_key){
				
				if('wt_pdf_invoice_attachment_wc_email_classes' === $i_key){

					$invoice_gen_status = array('wc-completed','wc-processing');
					$i_val = array();
					
					if(isset($_POST['wt_pdf_invoice_attachment_wc_email_classes'])){
						$order_status_wc_class_arr = Wt_Pklist_Common::wc_order_status_email_class_mapping();
						if ( !empty( $order_status_wc_class_arr ) ) {
							$invoice_gen_status = array();
							foreach ( $order_status_wc_class_arr as $order_status => $wc_email_class ) {
								if ( in_array( $wc_email_class, array_map('sanitize_text_field', $_POST['wt_pdf_invoice_attachment_wc_email_classes']) ) ) {
									$invoice_gen_status[] = $order_status;
								}
							}
						}
						$i_val = array_map('sanitize_text_field', $_POST['wt_pdf_invoice_attachment_wc_email_classes']);
					}

					Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_generate_for_orderstatus',$invoice_gen_status,$invoice_module_id);
				}else{
					$i_val = isset($_POST[$i_post_key]) ? sanitize_text_field($_POST[$i_post_key]) : '';
				}
				Wf_Woocommerce_Packing_List::update_option($i_key, $i_val, $invoice_module_id);
			}
			$out['status'] = true;
			$out['msg'] = esc_html__('Settings Updated', 'print-invoices-packing-slip-labels-for-woocommerce');
		}
		echo json_encode($out);
		exit();
	}

	/**
	 * Function to update the plugin tax settings when woocommerce tax is changed
	 *
	 * @since 4.2.0
	 * @since 4.4.2 - set the plugin tax status to exclusive tax if the WC tax is false
	 * @return void
	 */
	public function update_plugin_settings_when_wc_update_settings(){
		$current_wc_page	= isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		$wc_price_inc_tax	= get_option('woocommerce_prices_include_tax');
		$wc_tax_enable		= ! function_exists( 'wc_tax_enabled' ) ? apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' ) : wc_tax_enabled();
		
		if ( 'wc-settings' === $current_wc_page ) {
			if ( $wc_tax_enable &&  "yes" === $wc_price_inc_tax ) {
				Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_generate_for_taxstatus', array( 'in_tax' ) );
			} else {
				Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_generate_for_taxstatus', array( 'ex_tax' ) );
			}
		}
	}

	/**
	 *  Screens to show Black Friday and Cyber Monday Banner
	 * 
	 *  @since 4.7.0
	 */
	public function wt_bfcm_banner_screens( $screen_ids ) {
		$screen_ids[] = 'toplevel_page_wf_woocommerce_packing_list';
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_invoice';
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_packinglist';
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_deliverynote';
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_shippinglabel';
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_dispatchlabel'; // Plugin settings page.
		$screen_ids[] = 'invoice-packing_page_wf_woocommerce_packing_list_premium_extension'; // Premium extension page.
		return $screen_ids;
	}

	public function get_wt_pklist_plugin_data( $from_ajax = false ) {
		$page_param 			= '';
		$wt_pklist_plugin_data 	= array(
			'main'	=> get_option('Wf_Woocommerce_Packing_List'),
		);

		if( true === $from_ajax ) {
			$base		= ( isset( $_POST[ 'wf_settings_base' ] ) ? sanitize_text_field( $_POST[ 'wf_settings_base' ] ) : 'main' );
			$page_param	= ( "main" === $base ? '' : Wf_Woocommerce_Packing_List::get_module_id( $base ) );
		} else {
			$page_param = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ): '';
		}

		$wt_pklist_plugin_data = apply_filters('wt_pklist_get_plugin_data', $wt_pklist_plugin_data, $page_param);
		return $wt_pklist_plugin_data;
	}

	/**
	 * Adds the filter to customize before rendering the pdf
	 *
	 * @since 4.6.0
	 * @param array $filters
	 * @return array
	 */
	public function pdf_before_rendering_filters( $filters, $template_type, $order ) {
		$filters[] = array( 'woocommerce_currency_symbol', array( $this, 'alter_currency_symbol' ), 10, 2 );
		return $filters;
	}

	/**
	 * Alters the currency symbol in all the documents as per the plugin settings.
	 *
	 * @param string $currency_symbol
	 * @param string $currency
	 * @return string
	 */
	public function alter_currency_symbol( $currency_symbol, $currency ) {
		
		// show currency code instead of currency symbol.
		if ( 'Yes' === Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_show_currency_code' ) ) {
			return $currency;
		}

		// use extended font library for currency symbol.
		if ( 
			'Yes' !== Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_show_currency_code' ) && 
			'Yes' === Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_additional_currency_font_support' ) &&
			false === self::check_if_mpdf_used()
		) {
			$currency_symbol = sprintf( '<span class="wt_pdf_currency_symbol">%s</span>', $currency_symbol );
			return $currency_symbol;
		}
		
		return $currency_symbol;
	}

	/**
	 * Update the plugin settings with migrated values
	 * 
	 * @since 4.6.1
	 */
	public function update_plugin_settings_with_migration( $options, $base_id ) {
		if ( '' === $base_id ) {
			if ( isset( $options['woocommerce_wf_packinglist_preview'] ) ) {
				if ( 'enabled' === $options['woocommerce_wf_packinglist_preview'] ) {
					Wf_Woocommerce_Packing_List::update_option( 'woocommerce_wf_packinglist_preview', 'No' );
				} else if ( 'disabled' === $options['woocommerce_wf_packinglist_preview'] ) {
					Wf_Woocommerce_Packing_List::update_option( 'woocommerce_wf_packinglist_preview', 'Yes' );
				}
			}
		}
	}

	/**
	 * To add the element after particular key in array
	 */
	public static function wt_add_array_element_to_position( $settings, $new_element, $after_key ) {
		$new_settings = array();
		
		foreach ( $settings as $key => $value ) {
			$new_settings[ $key ] = $value; // Add the current element to the new array.
			
			// When you reach the desired key, add the new element immediately after it.
			if ( $key === $after_key ) {
				$new_settings = array_merge( $new_settings, $new_element );
			}
		}
		
		return $new_settings;
	}
}