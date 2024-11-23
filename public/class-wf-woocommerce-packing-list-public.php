<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/public
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_Public {

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
		'invoice',
		'packinglist',
		'deliverynote',
		'shippinglabel',
		'dispatchlabel',
	);
	public static $modules_default_state=array(
		'invoice'=>1,
		'packinglist'=>1,
		'deliverynote'=>1,
		'shippinglabel'=>1,
		'dispatchlabel'=>1,
	);

	public static $modules_label=array(
		'invoice'=>'Invoice',
		'packinglist'=>'Packing slip',
		'deliverynote'=>'Delivery note',
		'shippinglabel'=>'Shipping label',
		'dispatchlabel'=>'Dispatch label',
	);

	public static $existing_modules=array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.5.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_public_modules();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wf_Woocommerce_Packing_List_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wf_Woocommerce_Packing_List_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wf_Woocommerce_Packing_List_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wf_Woocommerce_Packing_List_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'_public', plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-public.js', array( 'jquery' ), $this->version, false );
		$user_id = get_current_user_id();
		$dont_show_again = false;
		if(0 !== $user_id){
			if(1 == get_user_meta($user_id, 'wt_pklist_doc_create_dont_show_popup',true) || "1" === get_user_meta($user_id, 'wt_pklist_doc_create_dont_show_popup',true)){
				$dont_show_again = true;
			}
		}
		$params=array(
			'show_document_preview' => Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_packinglist_preview' ),
			'document_access_type'	=> Wf_Woocommerce_Packing_List::get_option('wt_pklist_print_button_access_for'),
			'is_user_logged_in'	=> is_user_logged_in(),
			'msgs'=>array(
				'invoice_number_prompt_free_order' => __("‘Generate invoice for free orders’ is disabled in Invoice settings > Advanced. You are attempting to generate invoice for this free order. Proceed?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'creditnote_number_prompt' => __("Refund in this order seems not having credit number yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_no_from_addr' => __("Please fill the `from address` in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_title_prompt' => __("Invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt' => __("number has not been generated yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'pop_dont_show_again' => $dont_show_again,	
				'request_error' => __('Request error.','print-invoices-packing-slip-labels-for-woocommerce'),
				'error_loading_data' => __('Error loading data.','print-invoices-packing-slip-labels-for-woocommerce'),
				'min_value_error' => __( 'minimum value should be', 'print-invoices-packing-slip-labels-for-woocommerce'),
				'generating_document_text' => __( 'Generating document...', 'print-invoices-packing-slip-labels-for-woocommerce' ),
				'new_tab_open_error' => __( 'Failed to open new tab. Please check your browser settings.', 'print-invoices-packing-slip-labels-for-woocommerce' ),
			)
		);
		wp_localize_script($this->plugin_name.'_public', 'wf_pklist_params_public', $params);
	}

	/**
	 *Registers modules: public+admin	 
	 */
	public function common_modules()
	{ 
		$wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		$common_modules_set = true;
		if(false === $wt_pklist_common_modules || !is_array( $wt_pklist_common_modules ))
		{	
			$common_modules_set = false;
			$wt_pklist_common_modules=self::$modules_default_state;
		}
		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_pklist_common_modules[$module]))
			{
				$is_active=$wt_pklist_common_modules[$module]; //checking module status
			}else
			{
				$wt_pklist_common_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";
			if(file_exists($module_file) && (1 === $is_active || "1" === $is_active))
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_pklist_common_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_pklist_common_modules as $k=>$m)
		{
			if(in_array($k,self::$modules))
			{
				$out[$k]=$m;
			}
		}
		if(false === $common_modules_set){
			update_option('wt_pklist_common_modules',$out);
		}
	}
	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}

	public static function get_document_module_labels()
	{
		$labels=apply_filters('wf_pklist_alter_document_module_labels',self::$modules_label);
		return $labels;
	}

	public static function load_public_modules() {
		$public_modules = array(
			'ubl'
		);
		$public_modules = apply_filters( 'wf_pklist_public_modules', $public_modules );

		foreach ( $public_modules as $module ) {
			$module_file = plugin_dir_path(__FILE__) . "modules/{$module}/class-wt-pklist-pdf-{$module}.php";

			if ( file_exists( $module_file ) ) {
				require_once $module_file;
			}
		}
	}
}
