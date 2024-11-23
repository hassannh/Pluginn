<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.5.0
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.5.0
	 * @access   protected
	 * @var      Wf_Woocommerce_Packing_List_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.5.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.5.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	public static $base_version;
	private static $stored_options=array();

	public static $no_image="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";

	public static $template_data_tb='wfpklist_template_data';

	public static $default_additional_checkout_data_fields=array(
        'ssn' => 'SSN',
        'vat' => 'VAT'
    );

    public static $default_additional_data_fields=array(
        'contact_number' => 'Contact Number',
        'email' => 'Email',
        'ssn' => 'SSN',
        'vat' => 'VAT',
        'vat_number' => 'VAT',
        'eu_vat_number' => 'VAT',
        'cus_note' => 'Customer Note',
        'aelia_vat' => 'VAT',
    );

    /**
    *	@since 2.7.0
    * 	default fields without _billing_ prefix 
    */
    public static $default_fields_no_prefix=array(
    	'ssn'
    );

    public static $default_fields_no_prefix_vat = array(
    	'vat', 'vat_number', 'eu_vat_number'
    );

    public static $wf_packinglist_brand_color='080808';
    public static $loaded_modules=array();
	public $plugin_admin = null;
	public $plugin_public = null;
	public $plugin_common = null;
	public $basic_common_func = null;
	public $plugin_update = null;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.5.0
	 */
	public function __construct()
	{
		if( defined( 'WF_PKLIST_VERSION' ) ) 
		{
			$this->version = WF_PKLIST_VERSION;
			self::$base_version = WF_PKLIST_VERSION;
		}else 
		{
			$this->version = '4.7.1';
			self::$base_version = '4.7.1';
		}
		if(defined('WF_PKLIST_PLUGIN_NAME'))
		{
			$this->plugin_name=WF_PKLIST_PLUGIN_NAME;
		}else
		{
			$this->plugin_name='wf-woocommerce-packing-list';
		}
		$this->load_dependencies();
		$this->set_locale();
		$this->pre_load_hooks();
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wf_Woocommerce_Packing_List_Loader. Orchestrates the hooks of the plugin.
	 * - Wf_Woocommerce_Packing_List_i18n. Defines internationalization functionality.
	 * - Wf_Woocommerce_Packing_List_Admin. Defines all hooks for the admin area.
	 * - Wf_Woocommerce_Packing_List_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.5.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-loader.php';


		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wf-woocommerce-packing-list-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sequential-number.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-order-func.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wf-woocommerce-packing-list-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-update-install.php';

		/**
		 * The class responsible for defining all actions that occur in common for public and admin -facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-wt-pklist-common.php';

		/**
		 * Includes review request class file
		 */ 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-review_request.php';

		/**
		 * Details of pro addons list
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-pro-addons.php';

		/**
		 * Includes review request class file
		 */ 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/admin/class-wf-woocommerce-packing-list-admin_notices.php';

		/**
		 * Includes the Black Friday and Cyber Monday CTA banners for 2024
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/modules/banner/class-wt-bfcm-twenty-twenty-four.php';

		/**
		 * Includes request a feature class file
		 */ 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/modules/wt-pdf-request-feature/class-wt-pdf-request-feature.php';

		include plugin_dir_path( dirname( __FILE__ ) )."admin/views/_form_field_generator_new.php";

		$this->loader = new Wf_Woocommerce_Packing_List_Loader();
		$this->plugin_admin = new Wf_Woocommerce_Packing_List_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->plugin_public = new Wf_Woocommerce_Packing_List_Public( $this->get_plugin_name(), $this->get_version() );
		$this->plugin_common = new Wt_Pklist_Common($this->get_plugin_name(), $this->get_version());
		$this->plugin_update = new Wf_Woocommerce_Packing_List_Update_Install();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wf_Woocommerce_Packing_List_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.5.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wf_Woocommerce_Packing_List_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	*	@since 2.5.0 Some necessary functions
	*	@since 2.6.6 Added language swicthing
	*/
	private function pre_load_hooks()
	{
		$this->loader->add_action('init',$this,'run_necessary',1); //run some necessary function copied from old plugin
		
		$this->loader->add_filter('locale', $this, 'switch_locale', 1);
	}

	public function define_admin_basic_common_func_hooks(){

		$invoice_page_js_arr = array(
			'wf_woocommerce_packing_list_invoice',
			'wf_woocommerce_packing_list_packinglist',
			'wf_woocommerce_packing_list_creditnote'
		);

		$sdd_page_js_arr = array(
			'wf_woocommerce_packing_list_shippinglabel',
			'wf_woocommerce_packing_list_dispatchlabel',
			'wf_woocommerce_packing_list_deliverynote'
		);

		$pkl_page_js_arr = array(
			'wf_woocommerce_packing_list_picklist',
		);

		$pi_page_js_arr = array(
			'wf_woocommerce_packing_list_proformainvoice',
		);

		if(isset($_GET['page']) && in_array($_GET['page'],$invoice_page_js_arr) && !class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-basic-func.php';
			$this->basic_common_func = new Wf_Woocommerce_Packing_List_Basic_Common_Func( $this->get_plugin_name(), $this->get_version() );
		}

		if(isset($_GET['page']) && in_array($_GET['page'],$sdd_page_js_arr) && !class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func_SDD')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-basic-func.php';
			$this->basic_common_func = new Wf_Woocommerce_Packing_List_Basic_Common_Func( $this->get_plugin_name(), $this->get_version() );
		}

		if(isset($_GET['page']) && in_array($_GET['page'],$pkl_page_js_arr) && !class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func_PKL')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-basic-func.php';
			$this->basic_common_func = new Wf_Woocommerce_Packing_List_Basic_Common_Func( $this->get_plugin_name(), $this->get_version() );
		}

		if(isset($_GET['page']) && in_array($_GET['page'],$pi_page_js_arr) && !class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func_PI')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-woocommerce-packing-list-basic-func.php';
			$this->basic_common_func = new Wf_Woocommerce_Packing_List_Basic_Common_Func( $this->get_plugin_name(), $this->get_version() );
		}
	}

	/**
	*	@since 2.6.6 Swicth language on printing screen
	*/
	public function switch_locale($locale)
	{
		if(isset($_GET['print_packinglist'])) 
        {
        	$lang	= ( isset( $_GET['lang'] ) ? sanitize_text_field( $_GET['lang'] ) : '' );
            $lang_list=Wf_Woocommerce_Packing_List_Admin::get_language_list();
            if("" !== $lang && isset($lang_list[$lang])) /* valid language code */
            {
            	$locale=$lang;	         	
            }
        }
        return $locale;
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 */
	private function define_admin_hooks() 
	{	
		$this->loader->add_action('admin_enqueue_scripts',$this->plugin_admin, 'enqueue_styles' );
		$this->loader->add_action('admin_enqueue_scripts',$this->plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action('plugins_loaded', $this, 'define_admin_basic_common_func_hooks');
		//ajax hook for saving the moudles by toggling the button
		$this->loader->add_action('wp_ajax_wf_document_module_enable_disable',$this->plugin_admin,'document_module_enable_disable');

		//ajax hook for saving settings, Includes plugin main settings and settings from module
		$this->loader->add_action('wp_ajax_wf_save_settings', $this->plugin_admin, 'save_settings');

		/* load address from woo */
		$this->loader->add_action('wp_ajax_wf_pklist_load_address_from_woo',$this->plugin_admin,'load_address_from_woo');
		
		$this->loader->add_action('admin_menu', $this->plugin_admin, 'admin_menu',11); /* Adding admin menu */
		$this->loader->add_action('add_meta_boxes',$this->plugin_admin, 'add_meta_boxes',11); /* Add print option metabox in order page */		
		
		/**
		*	@since 2.6.7
		*	saving hook for debug tab 
		*/
		$this->loader->add_action('admin_init', $this->plugin_admin, 'debug_save');

		// Add plugin settings link: 
		$this->loader->add_filter('plugin_action_links_'.plugin_basename(WF_PKLIST_PLUGIN_FILENAME),$this->plugin_admin,'plugin_action_links');

		//print action button and dropdown items
		$this->loader->add_action('woocommerce_admin_order_actions_end',$this->plugin_admin, 'add_common_print_button_in_wc_order_listing_action_column',9,1);

		$this->loader->add_filter('bulk_actions-edit-shop_order',$this->plugin_admin,'alter_bulk_action',10); /* Add print buttons to order bulk actions */
		$this->loader->add_filter('bulk_actions-woocommerce_page_wc-orders',$this->plugin_admin,'alter_bulk_action',10); /* Add print buttons to order bulk actions */
				
		//frontend print action buttons
		$this->loader->add_action('woocommerce_order_details_after_order_table',$this->plugin_admin,'add_fontend_print_actions',10); /* Add print action buttons in user dashboard orders page */		
		
		$this->loader->add_filter('woocommerce_my_account_my_orders_actions', $this->plugin_admin, 'add_order_list_page_print_actions', 10, 2); /* Add print action buttons in user dashboard orders page */	
		//email print action buttons
		$this->loader->add_action('woocommerce_email_after_order_table',$this->plugin_admin,'add_email_print_actions',10); /* Add print action buttons in order */		
				
		//email attachment
		$this->loader->add_filter('woocommerce_email_attachments',$this->plugin_admin,'add_email_attachments',10,3); /* Add pdf attachments to order email */		
		
		$this->loader->add_filter('woocommerce_checkout_fields',$this->plugin_admin,'add_checkout_fields'); /* Add additional checkout fields */		

		$this->loader->add_action('init',$this->plugin_admin,'print_window',11); /* to print the invoice and packinglist */
		
		$this->plugin_update->do_update_things();
		$this->plugin_admin->admin_modules();
		$this->plugin_public->common_modules();
		
		
		$this->loader->add_action('plugins_loaded', $this->plugin_admin, 'register_tooltips', 11);

		/*Compatible function and filter with multicurrency and currency switcher plugin*/
		$this->loader->add_filter('wt_pklist_change_price_format',$this->plugin_admin,'wf_display_price',10,3);
		$this->loader->add_filter('wt_pklist_convert_currency',$this->plugin_admin,'wf_convert_to_user_currency',10,3);

		$this->loader->add_filter('woocommerce_shop_order_search_fields',$this->plugin_admin,'wf_search_order_by_invoice_number',10,1); /* Search the order by invoice number */

		$this->loader->add_action( 'woocommerce_after_account_orders', $this->plugin_admin,'action_after_account_orders_js',10);
		$this->loader->add_filter('woocommerce_debug_tools', $this->plugin_admin,'wt_pklist_delete_all_invoice_numbers_tool',10,1);
		$this->loader->add_action('admin_init',$this->plugin_admin,'wt_pklist_action_scheduler_for_invoice_number_count');
		$this->loader->add_action('update_empty_invoice_number_count', $this->plugin_admin, 'wt_get_empty_invoice_number_count');
		$this->loader->add_action('admin_init',$this->plugin_admin,'wt_pklist_action_scheduler_for_invoice_number');
		$this->loader->add_action('wt_pklist_schedule_auto_generate_invoice_number', $this->plugin_admin, 'action_for_auto_generate_invoice_number');

		$this->loader->add_action( 'wp_ajax_wf_pklist_advanced_fields_basic', $this->plugin_admin, 'advanced_settings');
		$this->loader->add_action('admin_footer', $this->plugin_admin, 'wt_pklist_popup_on_order_edit_page');

		$this->loader->add_action('wp_ajax_wt_pklist_cta_banner_dismiss',$this->plugin_admin,'wt_pklist_cta_banner_dismiss');
		$this->loader->add_action('wp_ajax_wt_pklist_settings_json',$this->plugin_admin,'wt_pklist_settings_json');
		$this->loader->add_action('admin_init', $this->plugin_admin, 'wt_pklist_import_settings');
		$this->loader->add_action('admin_init', $this->plugin_admin, 'wt_pklist_reset_settings');

		//ajax hook for downloading temp files
		$this->loader->add_action('wp_ajax_wt_pklist_download_all_temp', $this->plugin_admin, 'download_all_temp');
		$this->loader->add_action('admin_init', $this->plugin_admin, 'download_temp_zip_file', 11);

		// ajax hook for deleting the pdf/html file stored
		$this->loader->add_action('wp_ajax_wt_pklist_delete_all_temp', $this->plugin_admin, 'delete_all_temp');

		// hooks to make the action scheduler to delete the file stored in particular time interval automatically
		$this->loader->add_action('admin_init',$this->plugin_admin,'wt_pklist_action_scheduler_for_auto_cleanup');
		$this->loader->add_action('wt_pklist_temp_file_clear', $this->plugin_admin, 'delete_temp_files_recursively',10,1);
		
		//ajax hook for saving settings from the form wizard
		$this->loader->add_action('wp_ajax_wt_pklist_form_wizard_save', $this->plugin_admin, 'wt_pklist_form_wizard_save');
		$this->loader->add_action('woocommerce_settings_saved',$this->plugin_admin,'update_plugin_settings_when_wc_update_settings');
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $this, 'save_order_language_code' );
		$this->loader->add_action( 'woocommerce_store_api_checkout_order_processed', $this, 'save_order_language_code' ); // Checkout block

		
		/**
		 *  Set screens to show promotional banner 
		 * 
		 *  @since 4.2.1
		 */
		$this->loader->add_filter( "wt_bfcm_banner_screens", $this->plugin_admin, "wt_bfcm_banner_screens" );

		/**
		 * Update the version migrated values when updating the settings.
		 *
		 * @since 4.6.1
		 */
		$this->loader->add_filter( 'wt_pklist_add_filters_before_rendering_pdf', $this->plugin_admin, 'pdf_before_rendering_filters', 10, 3 );
		$this->loader->add_action( 'wf_pklist_intl_after_setting_update', $this->plugin_admin, 'update_plugin_settings_with_migration', 10, 2 );
	}

	private function define_common_hooks() {
		$this->plugin_common= Wt_Pklist_Common::get_instance( $this->get_plugin_name(), $this->get_version() );
		$this->plugin_common->load_common_modules();
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->loader->add_action( 'wp_enqueue_scripts',$this->plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts',$this->plugin_public, 'enqueue_scripts' );
	}


	/**
	 * Some modules are not start by default. So need to initialize via code
	 *
	 * @since    2.5.0
	 */
	public static function load_modules($module_id)
	{
		if(Wf_Woocommerce_Packing_List_Public::module_exists($module_id) || Wf_Woocommerce_Packing_List_Admin::module_exists($module_id))
		{
			if(!isset(self::$loaded_modules[$module_id]))
			{
				$module_class='Wf_Woocommerce_Packing_List_'.ucfirst($module_id);
				self::$loaded_modules[$module_id]=new $module_class;
			}
			return self::$loaded_modules[$module_id];
		}
		else
		{
			return null;
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.5.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.5.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.5.0
	 * @return    Wf_Woocommerce_Packing_List_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.5.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * run some necessary function copied from old plugin
	 * @since     2.5.0
	 */
	public function run_necessary()
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wf-legacy.php';	
		do_action('wt_run_necessary');
	}

	/**
	 * Generate tab head for settings page.
	 * method will translate the string to current language
	 * @since     2.5.0
	 */
	public static function generate_settings_tabhead($title_arr,$type="plugin")
	{
		$out_arr=apply_filters("wf_pklist_".$type."_settings_tabhead",$title_arr);
		foreach($out_arr as $k=>$v)
		{			
			if(is_array($v))
			{
				$v=(isset($v[2]) ? $v[2] : '').$v[0].' '.(isset($v[1]) ? $v[1] : '');
			}
		?>
			<a class="nav-tab" href="#<?php echo $k;?>"><?php echo $v; ?></a>
		<?php
		}
	}

	public static function wf_encode($data) 
	{
        return rtrim(strtr(base64_encode($data),'+/','-_'),'=');
    }

    public static function wf_decode($data)
    {
        return base64_decode(str_pad(strtr($data,'-_','+/'),strlen($data)%4,'=',STR_PAD_RIGHT));
    }

	/**
	 * Get the print button in order email and order details page in my account
	 *
	 * @param object $order
	 * @param int|string $order_id
	 * @param string $action
	 * @param string $label
	 * @param boolean $email_button
	 * @return void
	 */
    public static function generate_print_button_for_user($order,$order_id,$action,$label,$email_button=false)
    {
		if( !empty( $order ) ) {
			$document_link = add_query_arg( array(
				'attaching_pdf'		=> 1,
				'print_packinglist' => 'true',
				'email'     		=> self::wf_encode( WC()->version < '2.7.0' ? $order->billing_email : $order->get_billing_email() ),
				'post'				=> self::wf_encode($order_id),
				'type'				=> $action,
				'user_print'		=> 1,
				'_wpnonce'			=> wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
				'lang'				=> get_locale(),
				'access_key'    	=> $order->get_order_key(),
				'button_location'	=> true === $email_button ? 'email' : 'order_details',
			), site_url() );
			$invoice_url	= esc_url_raw($document_link);
			$style			= '';
			$button_class = '';
			if( $email_button ) {
				$button_class = 'wt_pklist_email_btn';
				$template_type = ( false !== strpos( $action, 'print_' ) ) ? str_replace('print_','',$action) : '';
				$style_arr = array(
					'background'		=> '#0085ba',
					'border-color'		=> '#0073aa',
					'box-shadow'		=> '0 1px 0 #006799',
					'color'				=> '#fff',
					'text-decoration'	=> 'none',
					'padding'			=> '10px',
					'border-radius'		=> '10px',
				);
				$style_arr	= apply_filters( 'wt_pklist_alter_style_for_email_button', $style_arr, $template_type );
				if( !empty( $style_arr ) ) {
					foreach ($style_arr as $style_key => $style_value) {
						$style .= $style_key.':'.$style_value.';';
					}
				}
			} else {
				$template_type = str_replace(array('print_', 'download_'), '', $action);
				if ( !empty( $template_type ) ) {
					if ( false !== strpos( $action, 'print_' ) ) {
						$button_class = 'wt_pklist_'.$template_type.'_print';
					} else {
						$button_class = 'wt_pklist_'.$template_type.'_download';
					}
				}
			}
			$button	= '<a class="button button-primary '.esc_attr( $button_class ).'" style="'.esc_attr($style).'" target="_blank" href="'.$invoice_url.'">'.wp_kses_post($label).'</a><br><br>';
			echo $button;
		}
    }

	/**
	 * Get the document print link for the action button in my account page - frontend
	 *
	 * @param object $order
	 * @param int|string $order_id
	 * @param string $template_type
	 * @param string $action
	 * @param boolean $email_button
	 * @param boolean $sent_to_admin
	 * @return string
	 */
    public static function generate_print_url_for_user($order, $order_id, $template_type, $action, $email_button=false, $sent_to_admin=false)
    {
		if( !empty( $order ) ) {
			$document_link = add_query_arg( array(
				'attaching_pdf'		=> 1,
				'print_packinglist' => 'true',
				'email'				=> self::wf_encode( WC()->version < '2.7.0' ? $order->billing_email : $order->get_billing_email() ),
				'post'				=> self::wf_encode($order_id),
				'type'				=> $action.'_'.$template_type,
				'user_print'		=> 1,
				'_wpnonce'			=> wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
				'lang'				=> get_locale(),
				'access_key'    	=> $order->get_order_key(),
			), site_url() );
			return esc_url_raw($document_link);
		}
		return '';
    }

	/**
	 * Get default settings
	 * @since     2.5.0
	 */
	public static function default_settings( $base_id='' ) {
		$wc_price_inc_tax 	= get_option('woocommerce_prices_include_tax');
		$wc_tax_enable		= ! function_exists( 'wc_tax_enabled' ) ? apply_filters( 'wc_tax_enabled', get_option( 'woocommerce_calc_taxes' ) === 'yes' ) : wc_tax_enabled();

		if ( $wc_tax_enable &&  "yes" === $wc_price_inc_tax ) {
			$wt_tax = array('in_tax');
		} else {
			$wt_tax = array('ex_tax');
		}
		
		$settings	= array(
			'woocommerce_wf_packinglist_companyname'			=> '',
			'woocommerce_wf_packinglist_logo'					=> '',
			'woocommerce_wf_packinglist_footer'					=> '',
			'woocommerce_wf_packinglist_sender_name'			=> '',
			'woocommerce_wf_packinglist_sender_address_line1'	=> '',
			'woocommerce_wf_packinglist_sender_address_line2'	=> '',
			'woocommerce_wf_packinglist_sender_city'			=> '',
			'wf_country'										=> '',
			'woocommerce_wf_packinglist_sender_postalcode'		=> '',
			'woocommerce_wf_packinglist_sender_contact_number'	=> '',
			'woocommerce_wf_packinglist_sender_vat'				=> '',
			'woocommerce_wf_state_code_disable'					=> 'no',
			'woocommerce_wf_packinglist_preview'				=> 'No',
			'woocommerce_wf_packinglist_package_type'			=> 'single_packing', //just keeping to avoid errors
			'woocommerce_wf_packinglist_boxes'					=> array(),
			'woocommerce_wf_add_rtl_support'					=> 'Yes',
			'active_pdf_library'								=> 'dompdf',
			'woocommerce_wf_generate_for_taxstatus'				=> $wt_tax,
			'wf_additional_data_fields'							=> array(),
			'wt_pklist_auto_temp_clear'							=> 'No',
			'wt_pklist_auto_temp_clear_interval'				=> '1440', //one day
			'wt_pklist_print_button_access_for'					=> 'logged_in',
			'wt_pklist_common_print_button_enable'				=> 'Yes',
			'wt_pklist_separate_print_button_enable'			=> array('invoice','packinglist'),
			'wt_pklist_show_currency_code'						=> 'No',
			'wt_pklist_additional_currency_font_support'		=> 'No',
		);
		
		$base_id	= ( "" === $base_id ) ? "main" : $base_id; // for the pro addons use
		$settings	= apply_filters( 'wf_module_default_settings', $settings, $base_id );
		return $settings;
	}

	/**
	 * Get active PDF libraries
	 * @since     2.6.6
	 */
	public static function get_pdf_libraries()
	{
		/* load available PDF libs */
        $pdf_libs=array(
            'dompdf'=>array(
                'file'=>plugin_dir_path(__FILE__).'class-dompdf.php', //library main file
                'class'=>'Wt_Pklist_Dompdf', //class name
                'title'=>'Dompdf', //This is for settings section
            )
        );

        return apply_filters('wt_pklist_alter_pdf_libraries', $pdf_libs);
	}

	/**
	 * Reset to default settings
	 * @since     2.5.0
	 */
	public static function reset_to_default($option_name,$base_id='')
	{
		$settings=self::default_settings($base_id);
		return (isset($settings[$option_name]) ? $settings[$option_name] : '');
	}

	/**
	 * Get current settings.
	 * @since     2.5.0
	 */
	public static function get_settings($base_id='')
	{
		$settings=self::default_settings($base_id);
		$option_name=($base_id=="" ? WF_PKLIST_SETTINGS_FIELD : $base_id);
		$option_id=($base_id=="" ? 'main' : $base_id); //to store in the stored option variable
		self::$stored_options[$option_id]=get_option($option_name);
		if(!empty(self::$stored_options[$option_id])) 
		{
			$settings=wp_parse_args(self::$stored_options[$option_id],$settings);
		}
		//stripping escape slashes
		$settings=self::arr_stripslashes($settings);
		$settings=apply_filters('wf_pklist_alter_settings',$settings,$base_id);
		return $settings;
	}

	public static function get_single_checkbox_fields($base_id='',$tab_name=''){
		$settings=self::single_checkbox_fields($base_id,$tab_name);
		return $settings;
	}

	public static function single_checkbox_fields($base_id='',$tab_name=''){
		$settings['wt_main_general'] = array(
			'woocommerce_wf_packinglist_preview' => 'No',
			'woocommerce_wf_state_code_disable' => "no",
			'woocommerce_wf_add_rtl_support' => "No",
			'wt_pklist_common_print_button_enable' => 'No',
			'wt_pklist_show_currency_code' => 'No',
			'wt_pklist_additional_currency_font_support' => 'No',
		);

		$base_id = ("" === $base_id) ? "main" : $base_id; // for the pro addons use
		$settings=apply_filters('wf_module_single_checkbox_fields',$settings,$base_id,$tab_name);
		$settings = (isset($settings[$tab_name]) && '' !== $tab_name) ? $settings[$tab_name] : array();
		return $settings;
	}
	
	/**
	 * @since 3.0.5
	 * Function to load default values for multi checkboxes when they are unchecked.
	 * PHP will send the checkbox values when they are unchecked.
	 */
	public static function get_multi_checkbox_fields($base_id='',$tab_name=''){
		$settings = array();
		$base_id = ("" === $base_id) ? "main" : $base_id; // for the pro addons use
		$settings['wt_main_general'] = array(
			'wt_pklist_separate_print_button_enable' => array()
		);
		$settings = apply_filters('wf_module_multi_checkbox_fields',$settings,$base_id,$tab_name);
		$settings = (isset($settings[$tab_name]) && '' !== $tab_name) ? $settings[$tab_name] : array();
		return $settings;
	}

	/**
	 * Delete current settings.
	 * @since     2.5.0
	 */
	public static function delete_settings($base_id='')
	{
		$option_name=("" === $base_id ? WF_PKLIST_SETTINGS_FIELD : $base_id);
		$option_id=("" === $base_id ? 'main' : $base_id); //to store in the stored option variable
		delete_option($option_name);
		unset(self::$stored_options[$option_id]);
	}

	protected static function arr_stripslashes($arr)
	{
		if(is_array($arr) || is_object($arr))
		{
			foreach($arr as &$arrv)
			{
				$arrv=self::arr_stripslashes($arrv);
			}
			return $arr;
		}else
		{
			return stripslashes($arr);
		}
	}

	/**
	 * Update current settings.
	 * @arg $base_id module id
	 * @since     2.5.0
	 */
	public static function update_settings($the_options,$base_id='')
	{
		if("" !== $base_id && "main" !== $base_id) //main is reserved so do not allow modules named main
		{
			self::$stored_options[$base_id]=$the_options;
			update_option($base_id,$the_options);
		}
		if("" === $base_id)
		{
			self::$stored_options['main']=$the_options;
			update_option(WF_PKLIST_SETTINGS_FIELD,$the_options);
		}
	}

	/**
	 * Update option value,
	 * @since     2.5.0
	 * @return mixed
	 */
	public static function update_option($option_name,$value,$base='')
	{
		$the_options=self::get_settings($base);
		$the_options[$option_name]=$value;
		self::update_settings($the_options,$base);
	}

	/**
	 * Get option value, move the option to common option field if it was individual
	 * @since     2.5.0
	 * @return mixed
	 */
	public static function get_option($option_name,$base='',$the_options=null)
	{
		if(is_null($the_options))
		{
			$the_options=self::get_settings($base);
		}
		$vl=isset($the_options[$option_name]) ? $the_options[$option_name] : false;
		$vl=apply_filters('wf_pklist_alter_option',$vl,$the_options,$option_name,$base);
		return $vl;
	}

	public static function get_module_id($module_base)
	{
		return WF_PKLIST_POST_TYPE.'_'.$module_base;
	}

	public static function get_module_base($module_id)
	{
		$module_base = str_replace(WF_PKLIST_POST_TYPE.'_','',$module_id);
		return $module_base;
	}
	
	/**
	* 	@since 2.6.3
	*	Get upload dir, Path
	*	@return array|string
	*/
	public static function get_temp_dir($out='')
	{
		$upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_url = $upload['baseurl'];
        //plugin subfolder
        $upload_dir = $upload_dir.'/'.WF_PKLIST_PLUGIN_NAME;
        $upload_url = $upload_url.'/'.WF_PKLIST_PLUGIN_NAME;
        if("path" === $out)
        {
        	return $upload_dir;
        }elseif("url" === $out)
        {
        	return $upload_url;	
        }else
        {
        	return array(
        		'path'=>$upload_dir,
        		'url'=>$upload_url,
        	);
        }
	}

    public static function is_from_address_available()
    {
        if(("" === self::get_option('woocommerce_wf_packinglist_sender_city') || 
        	"" === self::get_option('wf_country') || 
        	"" === self::get_option('woocommerce_wf_packinglist_sender_postalcode'))) 
        {
            return false;
        } else
        {
            return true;
        }
    }

    /**
    *	@since 2.7.0
    *	To display meta key along with meta label. Default fields without _billing_ prefix will be added 
    */
    public static function get_display_key($meta_key)
    {
    	/* default fields without _billing_ prefix */
        if(in_array($meta_key, self::$default_fields_no_prefix))
        {
        	$meta_key_display="_billing_".$meta_key;
        }
        elseif(('cus_note' === $meta_key) || in_array($meta_key, self::$default_fields_no_prefix_vat)) /* customer note is not a meta item */
        {
        	$meta_key_display="";
        }
        elseif("aelia_vat" === $meta_key) /* customer note is not a meta item */
        {
        	$meta_key_display="vat_number";
        }
        else
        {
        	$meta_key_display=$meta_key;
        }
        return ("" !== $meta_key_display ? "(".$meta_key_display.")" : "");
    }

	/**
	 * Add the setting fields to the plugin settings form
	 *
	 * @since 4.2.0 - Remove the tax settings and sync with WC tax settings
	 * @param array $settings
	 * @param string $target_id
	 * @param string $template_type
	 * @param string $base_id
	 * @return array
	 */
    public static function add_fields_to_settings( $settings, $target_id = "", $template_type ="", $base_id ="" ) {
    	$settings = apply_filters('wt_pklist_add_fields_to_settings',$settings,$target_id,$template_type,$base_id);
		if (isset( $settings['advanced_option']['woocommerce_wf_generate_for_taxstatus'] ) ) {
			unset( $settings['advanced_option']['woocommerce_wf_generate_for_taxstatus'] );
		}
    	return $settings;
    }

	public function save_order_language_code($order_id){
		if(!empty($order_id)){
			$current_language = get_locale();
			Wt_Pklist_Common::update_order_meta($order_id,'wt_pklist_order_language',$current_language);
		}
	}
}