






<?php
/**
 * Invoice section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */




 
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Invoice
{
	public $module_id							= '';
	public static $module_id_static				= '';
	public $module_base							= 'invoice';
	public $module_title						= '';
    public $customizer							= null;
    public $is_enable_invoice					= '';
    public static $return_dummy_invoice_number	= false;  //it will return dummy invoice number if force generate is on
	public $print_btn_label						= '';
	public $download_btn_label					= '';
	public function __construct()
	{	
		
		$this->module_id		= Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		self::$module_id_static	= $this->module_id;
		$this->module_title		= __('Invoice', 'print-invoices-packing-slip-labels-for-woocommerce');
		$this->print_btn_label	= __('Print invoice', 'print-invoices-packing-slip-labels-for-woocommerce');
		$this->download_btn_label	= __('Download invoice', 'print-invoices-packing-slip-labels-for-woocommerce');

		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);
		add_filter('wf_module_single_checkbox_fields', array($this, 'single_checkbox_fields'), 10, 3);
		add_filter('wf_module_multi_checkbox_fields', array($this, 'multi_checkbox_fields'), 10, 3);
		add_filter('wf_module_save_multi_checkbox_fields',array($this, 'save_multi_checkbox_fields'), 10, 4);
		add_filter('wf_module_customizable_items',array($this,'get_customizable_items'),10,2);
		add_filter('wf_module_non_options_fields',array($this,'get_non_options_fields'),10,2);
		add_filter('wf_module_non_disable_fields',array($this,'get_non_disable_fields'),10,2);
		
		//hook to add which fiedls to convert
		add_filter('wf_module_convert_to_design_view_html_for_'.$this->module_base,array($this,'convert_to_design_view_html'),10,3);

		//hook to generate template html
		add_filter('wf_module_generate_template_html_for_'.$this->module_base,array($this,'generate_template_html'), 10, 6);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		$this->is_enable_invoice=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_invoice',$this->module_id);
		if("Yes" === $this->is_enable_invoice) /* `print_it` method also have the same checking */
		{	
			// show document details
			add_filter('wt_print_docdata_metabox',array($this, 'add_docdata_metabox'),10,3);

			// show document print/download buttons
			add_filter('wt_print_actions',array($this,'add_print_buttons'),10,4);

			add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));
			add_filter('wt_frontend_print_actions',array($this,'add_frontend_print_buttons'),10,3);
			add_filter('wt_pklist_intl_frontend_order_list_page_print_actions', array($this, 'add_frontend_order_list_page_print_buttons'), 10, 3);					
			add_filter('wt_email_print_actions',array($this,'add_email_print_buttons'),10,3);
			add_filter('wt_email_attachments',array($this,'add_email_attachments'),10,4);
			add_action('woocommerce_thankyou',array($this,'generate_invoice_number_on_order_creation'),10,1);
			add_action('woocommerce_new_order',array($this,'generate_invoice_number_on_order_pending'),10,1);
			add_action('woocommerce_order_status_changed',array($this,'generate_invoice_number_on_status_change'),10,3);
			add_filter('wt_pklist_individual_print_button_for_document_types',array($this,'add_individual_print_button_in_admin_order_listing_page'),10,1);
			add_filter('woocommerce_admin_order_actions_end',array($this,'document_print_btn_on_wc_order_listing_action_column'),10,1);
		}
		add_action('wt_print_doc',array($this,'print_it'),10,2);

		//add fields to customizer panel
		add_filter('wf_pklist_alter_customize_inputs',array($this,'alter_customize_inputs'),10,3);
		add_filter('wf_pklist_alter_customize_info_text',array($this,'alter_customize_info_text'),10,3);
		
		add_filter('wt_pklist_alter_order_template_html',array($this,'alter_final_template_html'),10,3);
		
		add_action('wt_run_necessary',array($this,'run_necessary'));

		//invoice column and value
		add_filter('manage_edit-shop_order_columns',array($this,'add_invoice_column'),11); /* Add invoice number column to order page */
		add_action('manage_shop_order_posts_custom_column',array($this,'add_invoice_column_value'),11,2); /* Add value to invoice number column in order page */
		add_action('manage_edit-shop_order_sortable_columns',array($this,'sort_invoice_column'),11);

		// WC HPOS -  invoice column in order listing page
		add_filter('manage_woocommerce_page_wc-orders_columns',array($this,'add_invoice_column'),11); /* Add invoice number column to order page */
		add_action('manage_woocommerce_page_wc-orders_custom_column',array($this,'add_invoice_column_value'),11, 2); /* Add value to invoice number column in order page */
		add_action('manage_woocommerce_page_wc-orders_sortable_columns',array($this,'sort_invoice_column'),11);

		add_filter('wt_pklist_alter_tooltip_data',array($this, 'register_tooltips'),1);

		/** 
		* @since 2.6.2 declaring multi select form fields in settings form 
		*/
		add_filter('wt_pklist_intl_alter_multi_select_fields', array($this,'alter_multi_select_fields'), 10, 2);
		
		/** 
		* @since 2.6.2 Declaring validation rule for form fields in settings form 
		*/
		add_filter('wt_pklist_intl_alter_validation_rule', array($this,'alter_validation_rule'), 10, 2);

		/** 
		* @since 2.6.2 Enable PDF preview option
		*/
		add_filter('wf_pklist_intl_customizer_enable_pdf_preview', array($this,'enable_pdf_preview'), 10, 2);

		/* @since 2.6.9 add admin menu */
		add_filter('wt_admin_menu', array($this,'add_admin_pages'),10,1);

		add_action('wt_pklist_auto_generate_invoice_number_module',array($this,'generate_auto_invoice_number'),10);

		add_action('wt_pklist_update_settings_module_wise_on_update',array($this,'invoice_settings_on_plugin_update'),10);

		add_filter( 'wt_pklist_get_plugin_data', array( $this, 'get_plugin_data' ),10,2 );
	}
	
	/**
	* 	Add admin menu
	*	@since 	2.6.9
	*/
	public function add_admin_pages($menus)
	{
		$menus[]=array(
			'submenu',
			WF_PKLIST_POST_TYPE,
			__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			$this->module_id,
			array($this,'admin_settings_page'),
			'id' => 'invoice',
		);
		return $menus;
	}

	/**
	*  	Admin settings page
	*	@since 	2.6.9
	*/
	public function admin_settings_page()
	{
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $this->module_id);
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles',WC()->plugin_url().'/assets/css/admin.css');
		wp_enqueue_media();
		if(!class_exists('Wf_Woocommerce_Packing_List_Pro_Common_Func')){
			wp_enqueue_script($this->module_id,plugin_dir_url( __FILE__ ).'assets/js/main.js',array('jquery'),WF_PKLIST_VERSION);
		}
		if(!is_plugin_active('wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php') && isset($_GET['page']) && "wf_woocommerce_packing_list_invoice" === $_GET['page']){
			wp_enqueue_script($this->module_id.'-pro-cta-banner',plugin_dir_url( __FILE__ ).'assets/js/pro-cta-banner.js',array('jquery'),WF_PKLIST_VERSION);
		}
		wp_enqueue_script($this->module_id.'-common',plugin_dir_url( __FILE__ ).'assets/js/common.js',array('jquery'),WF_PKLIST_VERSION);
		$params=array(
			'nonces' => array(
	            'main'=>wp_create_nonce($this->module_id),
	        ),
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'order_statuses' => $order_statuses,
	        'module_base' => $this->module_base,
	        'msgs'=>array(
	        	'enter_order_id'=>__('Please enter order number','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'generating'=>__('Generating','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
	        )
		);
		$common_js_params = array(
			'order_statuses' => $order_statuses,
	        'module_base' => $this->module_base,
		);
		wp_localize_script($this->module_id,$this->module_id,$params);
		wp_localize_script($this->module_id.'-common',$this->module_id.'_common_param',$common_js_params);
		do_action('wt_pklist_add_additional_scripts',$this->module_id);
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);

	    //initializing necessary modules, the argument must be current module name/folder
	    if(!is_null($this->customizer) && true === apply_filters('wt_pklist_switch_to_classic_customizer_'.$this->module_base, true, $this->module_base))
		{
			$this->customizer->init($this->module_base);
		}

		$template_type = $this->module_base;
		include_once WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
		include(plugin_dir_path( __FILE__ ).'views/invoice-admin-settings.php');
	}

	/**
	* 	Enable PDF preview
	*	@since 	2.6.2
	*/
	public function enable_pdf_preview($status, $template_type)
	{
		if($template_type === $this->module_base)
		{
			$status=true;	
		}
		return $status;
	}

	/**
	* 	Declaring validation rule for form fields in settings form.
	* 	@since 2.6.2
	* 	@since 4.0.5	Added the field `woocommerce_wf_add_invoice_in_customer_mail`.
	*   @since 4.7.0    Removed the field `woocommerce_wf_add_invoice_in_customer_mail` and added the field `wt_pdf_invoice_attachment_wc_email_classes`.
	* 	
	*/
	public function alter_validation_rule($arr, $base_id)
	{
		if($base_id === $this->module_id) {
			$arr = array(
				'woocommerce_wf_generate_for_orderstatus' => array('type' => 'text_arr'),
				'woocommerce_wf_attach_' . $this->module_base => array('type' => 'text_arr'),
				'wf_' . $this->module_base . '_contactno_email' => array('type' => 'text_arr'),
				'wf_woocommerce_invoice_show_print_button' => array('type' => 'text_arr'),
				'woocommerce_wf_Current_Invoice_number' => array('type' => 'int'),
				'woocommerce_wf_invoice_start_number' => array('type' => 'int'),
				'woocommerce_wf_invoice_padding_number' => array('type' => 'int'),
				'wf_woocommerce_invoice_show_print_button' => array('type' => 'text_arr'),
				'wt_pdf_invoice_attachment_wc_email_classes' => array('type' => 'text_arr'),
			);
		}
		return $arr;
	}

	/**
	*	Declaring multi select form fields in settings form 
	* 	@since 2.6.2
	* 	@since 4.0.5 Added the field `woocommerce_wf_add_invoice_in_customer_mail`
	* 	@since 4.7.0 Removed the field `woocommerce_wf_add_invoice_in_customer_mail` and added the field `wt_pdf_invoice_attachment_wc_email_classes`
	* 	
	*/
	public function alter_multi_select_fields($arr, $base_id)
	{
		if($base_id === $this->module_id)
		{
			$arr=array(
				'wf_'.$this->module_base.'_contactno_email'=>array(),
	        	'woocommerce_wf_generate_for_orderstatus'=>array(),
	        	'woocommerce_wf_attach_'.$this->module_base=>array(),
	        	'wf_woocommerce_invoice_show_print_button'=>array(),
	        	'wt_pdf_invoice_attachment_wc_email_classes' => array(),
	        );
		}
		return $arr;
	}

	/**
	* 	@since 2.5.8
	* 	Hook the tooltip data to main tooltip array
	*/
	public function register_tooltips($tooltip_arr)
	{
		include(plugin_dir_path( __FILE__ ).'data/data.tooltip.php');
		$tooltip_arr[$this->module_id]=$arr;
		return $tooltip_arr;
	}


	/**
	* Adding received seal filters and other options
	*	@since 	2.5.5
	*/
	public function alter_final_template_html($html,$template_type,$order)
	{
		if($template_type === $this->module_base)
		{ 
			$is_enable_received_seal=true;
			$is_enable_received_seal=apply_filters('wf_pklist_toggle_received_seal',$is_enable_received_seal,$template_type,$order);
			if(true !== $is_enable_received_seal) //hide it
			{
				$html=Wf_Woocommerce_Packing_List_CustomizerLib::addClass('wfte_received_seal',$html,Wf_Woocommerce_Packing_List_CustomizerLib::TO_HIDE_CSS);
			}
		}
		return $html;
	}

	/**
	* Adding received seal extra text
	*	@since 	2.5.5
	*/
	private static function set_received_seal_extra_text($find_replace,$template_type,$html,$order)
	{
		if(false !== strpos($html,'[wfte_received_seal_extra_text]')) //if extra text placeholder exists then only do the process
        {
        	$extra_text='';
        	$find_replace['[wfte_received_seal_extra_text]']=apply_filters('wf_pklist_received_seal_extra_text',$extra_text,$template_type,$order);
		}
		return $find_replace;
	}

	/**
	* Adding customizer info text for received seal
	*	@since 	2.5.5
	*/
	public function alter_customize_info_text($info_text,$type,$template_type)
	{
		if($template_type === $this->module_base)
		{
			if("received_seal" === $type)
			{
				$info_text=sprintf(__('You can control the visibility of the seal according to order status via filters. See filter documentation %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<a href="'.admin_url('admin.php?page=wf_woocommerce_packing_list#help#filters').'" target="_blank">', '</a>');
				if ( Wf_Woocommerce_Packing_List_Admin::check_if_mpdf_used() ) {
					$info_text .= '<span style="color:red;">'. __( 'This feature might not work in mPDF.', 'print-invoices-packing-slip-labels-for-woocommerce' ) .'</span>';
				}
			}
		}
		return $info_text;
	}


	/**
	* Adding received seal customization options to customizer
	*	@since 	2.5.5
	*/
	public function alter_customize_inputs($fields,$type,$template_type)
	{
		if($template_type === $this->module_base)
		{
			if("received_seal" === $type)
			{
				$fields=array(			
					array(
						'label'=>__('Width','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'width',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',			
					), 
					array(
						'label'=>__('Height','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'height',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Text','print-invoices-packing-slip-labels-for-woocommerce'),
						'css_prop'=>'html',
						'trgt_elm'=>$type.'_text',
						'width'=>'49%',					
					), 
					array(
						'label'=>__('Font size','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'font-size',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',
						'float'=>'right',
					),					
					array(
						'label'=>__('Border width','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'border-top-width|border-right-width|border-bottom-width|border-left-width',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',						
					),
					array(
						'label'=>__('Line height','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'line-height',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Opacity','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'select',
						'select_options'=>array(
							'1'=>1,
							'0.9'=>.9,
							'0.8'=>.8,
							'0.7'=>.7,
							'0.6'=>.6,
							'0.5'=>.5,
							'0.4'=>.4,
							'0.3'=>.3,
							'0.2'=>.2,
							'0.1'=>.1,
							'0'=>0,
						),
						'css_prop'=>'opacity',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'event_class'=>'wf_cst_change',						
					),
					array(
						'label'=>__('Border radius','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'border-top-left-radius|border-top-right-radius|border-bottom-left-radius|border-bottom-right-radius',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('From left','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'margin-left',
						'trgt_elm'=>$type,
						'unit'=>'px',
						'width'=>'49%',						
					),
					array(
						'label'=>__('From top','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'margin-top',
						'trgt_elm'=>$type,
						'width'=>'49%',
						'float'=>'right',
					),
					array(
						'label'=>__('Angle','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'rotate',
						'trgt_elm'=>$type,
						'unit'=>'deg',						
					),
					array(
						'label'=>__('Color','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'color',
						'css_prop'=>'border-top-color|border-right-color|border-bottom-color|border-left-color|color',
						'trgt_elm'=>$type,
						'event_class'=>'wf_cst_click',
					)
				);
			}
		}
		return $fields;
	}

	/**
	*	Generate invoice number on order creation, If user set status to generate invoice number
	*	@since 2.5.4
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function generate_invoice_number_on_order_creation($order_id)
	{
		if(!$order_id){
        	return;
    	}

    	// Allow code execution only once 
    	if(!Wt_Pklist_Common::get_order_meta($order_id,'_wt_thankyou_action_done',true))
    	{
    		// Get an instance of the WC_Order object
        	$order=wc_get_order($order_id);
        	$status=( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();

        	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
        	$invoice_creation = 1;

        	if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					$invoice_creation = 0;
				}
			}

			if(1 === $invoice_creation){
				$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				$force_generate=in_array('wc-'.$status,$generate_invoice_for) ? true :false;		
				if(true === $force_generate) //only generate if user set status to generate invoice
				{
					self::generate_invoice_number($order,$force_generate);
				}
			}
        	//add post meta, prevent to fire thankyou hook multiple times
			$order->add_meta_data( '_wt_thankyou_action_done', true, true );
        	$order->save();
    	}
	}

	/**
	 * @since 2.8.3
	 * Generate the invoice number when order status changes
	 */
	public function generate_invoice_number_on_status_change($order_id,$old_status,$new_status){
		if(!$order_id){
        	return;
    	}
    	$order=wc_get_order($order_id);
		$status	= ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
    	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
    	$invoice_creation = 1;

    	if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				$invoice_creation = 0;
			}
		}

		if(1 === $invoice_creation){
			$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			$force_generate=in_array('wc-'.$status,$generate_invoice_for) ? true :false;	
			if(true === $force_generate) //only generate if user set status to generate invoice
			{
				self::generate_invoice_number($order,$force_generate);
			}
		}
	}

	/**
	 * @since 4.6.1
	 * Generate the invoice number when the order status of a new order is 'pending'.
	 * This is necessary because the 'woocommerce_thankyou' hook is not triggered when the order status is 'pending'.
	 */
	public function generate_invoice_number_on_order_pending($order_id) {
		if ( ! $order_id ) {
			return; 
		}
	
		$order = wc_get_order($order_id);
		$status = $order->get_status();
	
		if ( 'pending' !== $status ) {
			return;
		}
	
		$generate_invoice_for = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus', $this->module_id);
	
		if ( in_array('wc-' . $status, $generate_invoice_for) && 'pending' === $status ) {
			$this->generate_invoice_number_on_order_creation($order_id);
		}
	}


	/**
	 *  Items needed to be converted to design view for the customizer screen
	 */
	public function convert_to_design_view_html($find_replace,$html,$template_type)
	{	
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_default_order_fields($find_replace,$template_type,$html);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html);
			$find_replace['[wfte_received_seal_extra_text]']='';
		}
		return $find_replace;
	}

	/**
	 *  Items needed to be converted to HTML for print/download
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{	
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			//Generate invoice number while printing invoice
			self::generate_invoice_number($order);

			$find_replace=$this->set_other_data($find_replace,$template_type,$html,$order);

			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_default_order_fields($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_order_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_fields($find_replace,$template_type,$html,$order);
			$find_replace=self::set_received_seal_extra_text($find_replace,$template_type,$html,$order);
		}
		
		return $find_replace;
	}

	public function run_necessary()
	{
		$this->wf_filter_email_attach_invoice_for_status();
	}

	/**
	* 	@since 2.8.1
	* 	Added the filters to edit the invoice data when refunds
	* 
	*/ 
	public function set_other_data($find_replace, $template_type, $html, $order)
	{	
		add_filter('wf_pklist_alter_item_quantiy', array($this, 'alter_quantity_column'), 1, 5);
		add_filter('wf_pklist_alter_item_total_formated', array($this, 'alter_total_price_column'), 1, 7);
		add_filter('wf_pklist_alter_item_tax_formated', array($this, 'alter_total_total_tax_column'), 1, 6);
		add_filter('wf_pklist_alter_subtotal_formated', array($this, 'alter_sub_total_row'), 1, 5);
		add_filter('wf_pklist_alter_taxitem_amount',array($this,'alter_extra_tax_row'),1,4);
		add_filter('wf_pklist_alter_total_fee',array($this,'alter_fee_row'),1,5);
		add_filter('wf_pklist_alter_shipping_method',array($this,'alter_shipping_row'),1,4);
		add_filter('wf_pklist_alter_tax_data',array($this,'alter_tax_data'),1,4);

		// Filter for deleted product rows
		add_filter('wf_pklist_alter_item_quantiy_deleted_product',array($this,'alter_quantity_column_deleted_product'),1,4);
		add_filter('wf_pklist_alter_item_total_formated_deleted_product',array($this,'alter_total_price_column_deleted_product'),1,6);

		return $find_replace; 
	}

	public function alter_tax_data($tax_items_total,$tax_items,$order,$template_type)
	{	
		$all_refunds = $order->get_refunds();
		$new_tax = 0;
		if(!empty($all_refunds)){
			// get refund tax from all line items
			foreach($order->get_items() as $item_id => $item){
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			// get refund tax from fee and shipping
			foreach($all_refunds as $refund_order){
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						// fee details
						$fee_details=$refund_order->get_items('fee');
						if(!empty($fee_details)){
				        	$fee_ord_arr = array();
				        	foreach($fee_details as $fee => $fee_value){
			                    $fee_order_id = $fee;
			                    if(!in_array($fee_order_id,$fee_ord_arr)){
			                    	$fee_taxes = $fee_value->get_taxes();
			                    	$new_tax += abs(isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0);
			                        $fee_ord_arr[] = $fee_order_id;
			                    }
			                }
				        }
				        // shipping details
				        $shipping_details=$refund_order->get_items('shipping');
				        if(!empty($shipping_details)){
				        	$shipping_ord_arr = array();
				        	foreach($shipping_details as $ship => $shipping_value){
			                    $ship_order_id = $ship;
			                    if(!in_array($ship_order_id,$shipping_ord_arr)){
			                    	$shipping_taxes = $shipping_value->get_taxes();
			                    	$new_tax += abs(isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0);
			                        $shipping_ord_arr[] = $ship_order_id;
			                    }
			                }
				        }
					}
				}
			}
		}

		if($new_tax > 0){
			$tax_items_total = (float)$tax_items_total - (float)$new_tax;
		}
		return $tax_items_total;
	}
	/**
	*	@since 2.8.1
	*	Alter total price of order item if the item is refunded
	*	
	*/
	public function alter_total_price_column($product_total_formated, $template_type, $product_total, $_product, $order_item, $order,$incl_tax)
	{	
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$item_id = $order_item->get_id();
			$new_total=(float)$order->get_total_refunded_for_item($item_id);
			$new_tax = 0;
			if(true === $incl_tax){
				$tax_items = $order->get_tax_totals();
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			$new_total += $new_tax;
			if($new_total>0)
			{	
				$old_product_formated = '<strike>'.$product_total_formated.'</strike>';
				$wc_version=WC()->version;
				$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
				$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
				$new_total = (float)$product_total - $new_total;
				$product_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$product_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$product_total_formated,$template_type,$order);
				$product_total_formated = '<span style="">'.$old_product_formated.' '.$product_total_formated.'</span>';
			}
		}
		return $product_total_formated;
	}

	public function alter_total_total_tax_column($item_tax_formated,$template_type,$item_tax,$_product,$order_item,$order){
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$item_taxes			=	$order_item->get_taxes();
			$item_tax_subtotal	=	(isset($item_taxes['total']) ? $item_taxes['total'] : array());
			$tax_items_arr		=	$order->get_items('tax');
			$order_item_id 		= 	$order_item->get_id();
			$refunded_tot_tax	= 	0;
			foreach ($tax_items_arr as $tax_item)
			{
				$refunded_tot_tax += abs((float)$order->get_tax_refunded_for_item($order_item_id,$tax_item->get_rate_id()));
			}

			if($refunded_tot_tax > 0){
				$old_item_tax_formated = '<strike>'.$item_tax_formated.'</strike>';
				$wc_version=WC()->version;
				$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
				$item_tax	= (float)$item_tax - $refunded_tot_tax;
				$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
				$item_tax_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$item_tax);
				$item_tax_formated = apply_filters('wf_pklist_alter_price_to_negative',$item_tax_formated,$template_type,$order);
				$item_tax_formated = '<span style="">'.$old_item_tax_formated.' '.$item_tax_formated.'</span>';
			}
		}
		return $item_tax_formated;
	}

	/**
	*	@since 2.8.3
	*	Alter total price of order item if the item is refunded
	*	
	*/
	public function alter_total_price_column_deleted_product($product_total_formated, $template_type, $product_total, $order_item, $order,$incl_tax)
	{	
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$item_id = $order_item->get_id();
			$new_total=(float)$order->get_total_refunded_for_item($item_id);
			$new_tax = 0;
			if($incl_tax == true){
				$tax_items = $order->get_tax_totals();
				if(is_array($tax_items) && count($tax_items)>0)
				{
					foreach($tax_items as $tax_item)
					{
						$tax_rate_id = $tax_item->rate_id;
						$new_tax += $order->get_tax_refunded_for_item($item_id,$tax_rate_id,'line_item');
					}
				}
			}
			$new_total += $new_tax;
			if($new_total>0)
			{	
				$old_product_formated = '<strike>'.$product_total_formated.'</strike>';
				$wc_version=WC()->version;
				$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
				$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
				$new_total = (float)$product_total - $new_total;
				$product_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$product_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$product_total_formated,$template_type,$order);
				$product_total_formated = '<span style="">'.$old_product_formated.' '.$product_total_formated.'</span>';
			}
		}
		return $product_total_formated;
	}

	/**
	*	@since 2.8.1
	*	Alter quantity of order item if the item is refunded
	*	
	*/
	public function alter_quantity_column($qty, $template_type, $_product, $order_item, $order)
	{
		$item_id = $order_item->get_id();
		$new_qty=$order->get_qty_refunded_for_item($item_id);
		if($new_qty<0)
		{
			$qty='<del>'.$qty.'</del> &nbsp; <ins>'.($qty+$new_qty).'</ins>';
		}
		return $qty;
	}

	/**
	*	@since 2.8.3
	*	Alter quantity of order item if the item is refunded
	*	
	*/
	public function alter_quantity_column_deleted_product($qty, $template_type, $order_item, $order)
	{
		$item_id = $order_item->get_id();
		$new_qty=$order->get_qty_refunded_for_item($item_id);
		if($new_qty<0)
		{
			$qty='<del>'.$qty.'</del> &nbsp; <ins>'.($qty+$new_qty).'</ins>';
		}
		return $qty;
	}

	/**
	*	@since 2.8.2
	*	Alter subtotal row in product table, if any refund
	*	
	*/
	public function alter_sub_total_row($sub_total_formated, $template_type, $sub_total, $order, $incl_tax){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
		$new_total = 0;
		$new_tax = 0;
		$decimal = Wf_Woocommerce_Packing_List_Admin::wf_get_decimal_price($user_currency,$order);

		$incl_tax_text='';
		if(true === $incl_tax){
			$incl_tax_text=Wf_Woocommerce_Packing_List_CustomizerLib::get_tax_incl_text($template_type, $order, 'product_price');
			$incl_tax_text=("" !== $incl_tax_text ? ' ('.$incl_tax_text.')' : $incl_tax_text);
		}
		$sub_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$sub_total);
		
		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			foreach($all_refunds as $refund_order){
				foreach ($refund_order->get_items() as $item_id => $item ) {
					
					$new_total += (float)$item->get_subtotal();
					if($incl_tax == true){
						$tax_items = $order->get_tax_totals();
						if(is_array($tax_items) && count($tax_items)>0)
						{
							foreach($tax_items as $tax_item)
							{
								$tax_rate_id = $tax_item->rate_id;
							 	$refund_tax = $item->get_taxes();
		            			$new_tax += isset( $refund_tax['total'][ $tax_rate_id ] ) ? (float) $refund_tax['total'][ $tax_rate_id ] : 0;
							}
						}
					
					}
				}
			}
			$new_total += $new_tax;
			if($new_total < 0){
				$new_total = $sub_total - abs((float)$new_total);
				$new_sub_total_formated = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_total);
				$sub_total_formated = '<span style=""><strike>'.$sub_total_formated.'</strike> '.$new_sub_total_formated.'</span>';
			}
		}
		$sub_total_formated = apply_filters('wf_pklist_alter_price_to_negative',$sub_total_formated,$template_type,$order);
		return $sub_total_formated.$incl_tax_text;
	}

	/**
	*	@since 2.8.2
	*	Alter Individual tax rows in product table, if any refund
	*	
	*/
	public function alter_extra_tax_row($tax_amount, $tax_item, $order, $template_type){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$new_tax_amount = 0;
		$all_refunds = $order->get_refunds();
		$tax_rate_id = $tax_item->rate_id;
		$shipping = 0;

		if(!empty($all_refunds)){
			foreach($all_refunds as $refund_order){
				foreach($refund_order->get_items() as $refunded_item_id => $refunded_item){
		            $refund_tax = $refunded_item->get_taxes();
		            $new_tax_amount += isset( $refund_tax['total'][ $tax_rate_id ] ) ? (float) $refund_tax['total'][ $tax_rate_id ] : 0;
		        }

		        $fee_details=$refund_order->get_items('fee');
		        if(!empty($fee_details)){
		        	$fee_ord_arr = array();
		        	foreach($fee_details as $fee => $fee_value){
	                    $fee_order_id = $fee;
	                    if(!in_array($fee_order_id,$fee_ord_arr)){
	                    	$fee_taxes = $fee_value->get_taxes();
	                    	$new_tax_amount += isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0;
	                        $fee_ord_arr[] = $fee_order_id;
	                    }
	                }
		        }
		        $shipping_details=$refund_order->get_items('shipping');
		        if(!empty($shipping_details)){
		        	$shipping_ord_arr = array();
		        	foreach($shipping_details as $ship => $shipping_value){
	                    $ship_order_id = $ship;
	                    if(!in_array($ship_order_id,$shipping_ord_arr)){
	                    	$shipping_taxes = $shipping_value->get_taxes();
	                    	$new_tax_amount += isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0;
	                        $shipping_ord_arr[] = $ship_order_id;
	                    }
	                }
		        }
		        $refund_id = $wc_version<'2.7.0' ? $refund_order->id : $refund_order->get_id();
			}

			if($new_tax_amount < 0){
				$new_tax_amount = $tax_item->amount - abs((float)$new_tax_amount);
				$new_tax_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_tax_amount);
				$tax_amount = '<span><strike>'.$tax_amount.'</strike> '.$new_tax_amount_formatted.'</span>';
			}
		}
		return $tax_amount;	
	}

	/**
	*	@since 2.8.2
	*	Alter Fee row in product table, if any refund
	*	
	*/
	public function alter_fee_row($fee_total_amount_formated,$template_type,$fee_total_amount,$user_currency,$order){
		$incl_tax_text = '';
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$tax_items = $order->get_tax_totals();

		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$new_fee_total_amount = 0;
			foreach($all_refunds as $refund_order){
				$fee_details=$refund_order->get_items('fee');
				if(!empty($fee_details)){
					$fee_ord_arr = array();
	                foreach($fee_details as $fee => $fee_value){
	                    $fee_order_id = $fee;
	                    if(!in_array($fee_order_id,$fee_ord_arr)){
	                    	$fee_line_total = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($fee_order_id,'_line_total',true) : $order->get_item_meta($fee_order_id, '_line_total', true);
	                        $new_fee_total_amount += (float)$fee_line_total;
	                        if($incl_tax){
	                        	foreach($tax_items as $tax_item)
								{	
									$tax_rate_id = $tax_item->rate_id;
									$fee_taxes = $fee_value->get_taxes();
	                        		$new_fee_total_amount += isset( $fee_taxes['total'][ $tax_rate_id ] ) ? (float) $fee_taxes['total'][ $tax_rate_id ] : 0;
								}
	                        }
	                        $fee_ord_arr[] = $fee_order_id;
	                    }
	                }
				}
			}
			if($new_fee_total_amount < 0){
				$new_fee_total_amount = (float)$fee_total_amount - abs((float)$new_fee_total_amount);
				$new_fee_total_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_fee_total_amount);
				$fee_total_amount_formated = '<span><strike>'.$fee_total_amount_formated.'</strike> '.$new_fee_total_amount_formatted.'</span>';
			}
		}
		return $fee_total_amount_formated;
	}

	/**
	*	@since 2.8.2
	*	Alter Shipping amount row in product table, if any refund
	*	
	*/
	public function alter_shipping_row($shipping, $template_type, $order, $product_table){
		$wc_version=WC()->version;
		$order_id=$wc_version<'2.7.0' ? $order->id : $order->get_id();
		$user_currency=Wt_Pklist_Common::get_order_meta($order_id,'currency',true);
		$incl_tax_text = '';
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);
		$tax_items = $order->get_tax_totals();

		$all_refunds = $order->get_refunds();
		if(!empty($all_refunds)){
			$new_shipping_amount = 0;
			foreach($all_refunds as $refund_order){
				$refund_id = $wc_version<'2.7.0' ? $refund_order->id : $refund_order->get_id();
				$new_shipping_amount += (float) Wt_Pklist_Common::get_order_meta($refund_id,'shipping_total',true);

				if($incl_tax){
					if(is_array($tax_items) && count($tax_items)>0)
					{
						foreach($tax_items as $tax_item)
						{	
							$tax_rate_id = $tax_item->rate_id;
							$shipping_details=$refund_order->get_items('shipping');
					        if(!empty($shipping_details)){
					        	$shipping_ord_arr = array();
					        	foreach($shipping_details as $ship => $shipping_value){
				                    $ship_order_id = $ship;
				                    if(!in_array($ship_order_id,$shipping_ord_arr)){
				                    	$shipping_taxes = $shipping_value->get_taxes();
				                    	$new_shipping_amount += isset( $shipping_taxes['total'][ $tax_rate_id ] ) ? (float) $shipping_taxes['total'][ $tax_rate_id ] : 0;
				                        $shipping_ord_arr[] = $ship_order_id;
				                    }
				                }
					        }	
						}
					}
				}
			}
			
			if($new_shipping_amount < 0){
				
				if (($incl_tax === false)) {
					$shipping_total_amount = (float)$order->get_shipping_total();
				}else{
					if(abs($order->get_shipping_tax()) > 0){
						$incl_tax_text=Wf_Woocommerce_Packing_List_CustomizerLib::get_tax_incl_text($template_type, $order, 'product_price');
						$incl_tax_text=($incl_tax_text!="" ? ' ('.$incl_tax_text.')' : $incl_tax_text);
					}
					$shipping_total_amount = (float)$order->get_shipping_total() + (float)$order->get_shipping_tax();
				}
				
				$new_shipping_amount = $shipping_total_amount - abs((float)$new_shipping_amount);
				$old_shipping_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$shipping_total_amount);
				$new_shipping_total_amount_formatted = Wf_Woocommerce_Packing_List_Admin::wf_display_price($user_currency,$order,$new_shipping_amount);
				$shipping = '<span><strike>'.$old_shipping_amount_formatted.'</strike> '.$new_shipping_total_amount_formatted.'</span>'.$incl_tax_text;
				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );
			}
		}
		return $shipping;
	}

    protected static function get_orderdate_timestamp($order_id)
    {
    	$order_date=get_the_date('Y-m-d h:i:s A',$order_id);
		return strtotime($order_date);
    }

    /**
	* Get invoice date
	* @since 2.5.4
	* @return mixed
	*/
    public static function get_invoice_date($order_id,$date_format,$order)
    {
    	$invoice_date=Wt_Pklist_Common::get_order_meta($order_id,'_wf_invoice_date',true);
    	if($invoice_date)
    	{
    		return (empty($invoice_date) ? '' : date_i18n($date_format,$invoice_date));
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

    public static function generate_invoice_number($order, $force_generate=true,$free_ord="") 
    {	
    	$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    $wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_number', true);
		
		if( !empty( $wf_invoice_id ) ) {
			return $wf_invoice_id;
		}

	    if((empty($wf_invoice_id)) && ("set" !== $free_ord)){
	    	$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',self::$module_id_static);
			if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					return '';
				}
			}
	    }

	    if(class_exists('Wf_Woocommerce_Packing_List_Sequential_Number'))
	    {
			$lockFolderPath = Wf_Woocommerce_Packing_List::get_temp_dir('path').'-lock';
			if ( !is_dir( $lockFolderPath ) ) {
				wp_mkdir_p( $lockFolderPath );
			}
			$lockFilePath	= $lockFolderPath.'/wt_pklist_sequence_number.lock';
			$file 			= fopen($lockFilePath,"w");
			if($file){
				if (flock($file,LOCK_EX)) {
					$invoice_id =  Wf_Woocommerce_Packing_List_Sequential_Number::generate_sequential_number($order, self::$module_id_static, array('number'=>'wf_invoice_number', 'date'=>'wf_invoice_date', 'enable'=>'woocommerce_wf_enable_invoice'), $force_generate);
					flock($file,LOCK_UN);
					return $invoice_id;
				}else{
					sleep(2);
					self::generate_invoice_number($order, $force_generate,$free_ord);
				}
			}else{
				// if fopen fails
				$invoice_id =  Wf_Woocommerce_Packing_List_Sequential_Number::generate_sequential_number($order, self::$module_id_static, array('number'=>'wf_invoice_number', 'date'=>'wf_invoice_date', 'enable'=>'woocommerce_wf_enable_invoice'), $force_generate);
				return $invoice_id;
			}
	    }else
	    {
	    	return '';
	    }
	}

	/**
	 * Function to add "Invoice" column in order listing page
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column($columns)
	{
		$columns['Invoice']=__('Invoice','print-invoices-packing-slip-labels-for-woocommerce');
        return $columns;
	}

	/**
	 * Function to add value in "Invoice" column
	 *
	 * @since    2.5.0
	 */
	public function add_invoice_column_value($column, $post_or_order_object)
	{
		$order = ( $post_or_order_object instanceof \WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		if ( ! is_object( $order ) && is_numeric( $order ) ) {
			$order = wc_get_order( absint( $order ) );
		}

		if("Invoice" === $column)
		{
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
			$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
			$force_generate=in_array('wc-'.$order_status,$generate_invoice_for) ? true :false;	
			$wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_number', true);
			echo $wf_invoice_id;
		}
	}

	public function sort_invoice_column($columns){
		$columns['Invoice'] = __('Invoice','print-invoices-packing-slip-labels-for-woocommerce');
    	return $columns;
	}

	/**
	 * @since 3.0.5
	 * [Fix] - Function to generate invoice number in ascending order by order date
	 */ 
	public function generate_auto_invoice_number()
	{
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		if(!empty($generate_invoice_for)){
			$empty_invoice_order_ids = Wf_Woocommerce_Packing_List_Admin::get_order_ids_for_invoice_number_generation($this->module_id);
			if(!empty($empty_invoice_order_ids)){
				foreach($empty_invoice_order_ids as $this_order_id){
					$order = Wt_Pklist_Common::get_order($this_order_id);
					$order_id = (int)(WC()->version < '2.7.0') ? $order->id : $order->get_id();
					$order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();
					$wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id,'wf_invoice_number',true);
				    if(empty($wf_invoice_id))
				    {
				    	$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
						$force_generate=in_array('wc-'.$order_status,$generate_invoice_for) ? true :false;
						self::generate_invoice_number($order,$force_generate);
				    }
				}
				update_option('invoice_empty_count',0);
			}
		}else{
			update_option('invoice_empty_count',0);
		}
	}

	/**
	 * removing status other than generate invoice status
	 * @since 	2.5.0
	 * @since 	2.5.3 [Bug fix] array intersect issue when order status is empty 	
	 */
	private function wf_filter_email_attach_invoice_for_status()
	{
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);
		$email_attach_invoice_for_status=$the_options['woocommerce_wf_attach_invoice'];
		$generate_for_orderstatus=$the_options['woocommerce_wf_generate_for_orderstatus'];
		$generate_for_orderstatus=!is_array($generate_for_orderstatus) ? array() : $generate_for_orderstatus;
		$email_attach_invoice_for_status=!is_array($email_attach_invoice_for_status) ? array() : $email_attach_invoice_for_status;
		$the_options['woocommerce_wf_attach_invoice']=array_intersect($email_attach_invoice_for_status,$generate_for_orderstatus);
		Wf_Woocommerce_Packing_List::update_settings($the_options,$this->module_id);
	}

	public function get_customizable_items($settings,$base_id)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($base_id === $this->module_id)
		{
			$only_pro_html='<span class="wt_customizer_pro_text" style="color:red;"> ('.__('Pro version','print-invoices-packing-slip-labels-for-woocommerce').')</span>';
			$only_pro_addon_html='<span class="wt_customizer_pro_text" style="color:red;"> ('.__('Pro Add-on','print-invoices-packing-slip-labels-for-woocommerce').')</span>';
			//these fields are the classname in template Eg: `company_logo` will point to `wfte_company_logo`
			
			$settings = array(
				'doc_title'=>__('Document title','print-invoices-packing-slip-labels-for-woocommerce'),
				'company_logo'=>__('Company Logo / Name','print-invoices-packing-slip-labels-for-woocommerce'),
				//'barcode_disabled'=>__('Barcode','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'invoice_number'=>__('Invoice Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'order_number'=>__('Order Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_date'=>__('Invoice Date','print-invoices-packing-slip-labels-for-woocommerce'),
				'order_date'=>__('Order Date','print-invoices-packing-slip-labels-for-woocommerce'),
				'product_table'=>__('Product Table','print-invoices-packing-slip-labels-for-woocommerce'),
				'from_address'=>__('From Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'billing_address'=>__('Billing Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'shipping_address'=>__('Shipping Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'email'=>__('Email Field','print-invoices-packing-slip-labels-for-woocommerce'),
				'tel'=>__('Tel Field','print-invoices-packing-slip-labels-for-woocommerce'),
				'customer_note'=>__('Customer note','print-invoices-packing-slip-labels-for-woocommerce'),
				'ssn_number'=>__('SSN number','print-invoices-packing-slip-labels-for-woocommerce'),
				'vat_number'=>__('VAT number','print-invoices-packing-slip-labels-for-woocommerce'),
				//'shipping_method'=>__('Shipping Method','print-invoices-packing-slip-labels-for-woocommerce'),
				'received_seal'=>__('Payment received stamp','print-invoices-packing-slip-labels-for-woocommerce'),
			);

			$template_type=$this->module_base;
			$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
			$settings['barcode'] = __('Barcode','print-invoices-packing-slip-labels-for-woocommerce');
			if(!$show_qrcode_placeholder){
				$settings['footer'] = __('Footer','print-invoices-packing-slip-labels-for-woocommerce');
				$settings['qrcode_disabled'] = __('QR code','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_addon_html;
			}else{
				$settings['qrcode'] = __('QR Code','print-invoices-packing-slip-labels-for-woocommerce');
				$settings['footer'] = __('Footer','print-invoices-packing-slip-labels-for-woocommerce');
			}
			
			if ( false === $is_pro_customizer ) {
				$pro_features = array(
					'tracking_number_pro_element'=>__('Tracking Number','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_subtotal_pro_element'=>__('Subtotal','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_shipping_pro_element'=>__('Shipping','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_cart_discount_pro_element'=>__('Cart Discount','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_order_discount_pro_element'=>__('Order Discount','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_total_tax_pro_element'=>__('Total Tax','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_fee_pro_element'=>__('Fee','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_coupon_pro_element'=>__('Coupon info','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_payment_method_pro_element'=>__('Payment Method','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
					'product_table_payment_total_pro_element'=>__('Total','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				);
				$settings = array_merge($settings, $pro_features);
			}
		}
		return $settings;
	}

	/*
	* These are the fields that have no customizable options, Just on/off
	* 
	*/
	public function get_non_options_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{	
			$template_type=$this->module_base;
			$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',false,$template_type);
			return array(
				'barcode',
				'qrcode',
				'footer',
				'return_policy',
			);
		}
		return $settings;
	}

	/*
	* These are the fields that are switchable
	* 
	*/
	public function get_non_disable_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			return array(
				'product_table_payment_summary'
			);
		}
		return $settings;
	}

	/**
	*	Default form fields and their values for invoice settings page
	* 	@since 4.0.5	Added the fields `woocommerce_wf_add_invoice_in_customer_mail` and `woocommerce_wf_add_invoice_in_admin_mail`.
	*	@since 4.7.0	Removed the fields `woocommerce_wf_add_invoice_in_customer_mail`,`woocommerce_wf_add_invoice_in_admin_mail` and added the field `wt_pdf_invoice_attachment_wc_email_classes`.
	* 	
	*/ 
	public function default_settings($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			$settings = array(
	        	'woocommerce_wf_generate_for_orderstatus'=>array('wc-completed','wc-processing'),
	        	'woocommerce_wf_attach_invoice'=>array(),
	        	'woocommerce_wf_packinglist_logo'=>'',
	        	'woocommerce_wf_add_invoice_in_mail'=>'No',
	        	'woocommerce_wf_packinglist_frontend_info'=>'Yes',
	        	'woocommerce_wf_invoice_number_format'=>"[number]",
				'woocommerce_wf_Current_Invoice_number'=>1,
				'woocommerce_wf_invoice_start_number'=>1,
				'woocommerce_wf_invoice_number_prefix'=>'',
				'woocommerce_wf_invoice_padding_number'=>0,
				'woocommerce_wf_invoice_number_postfix'=>'',
				'woocommerce_wf_invoice_as_ordernumber'=>"Yes",
				'woocommerce_wf_enable_invoice'=>"Yes",
				'woocommerce_wf_add_customer_note_in_invoice'=>"No", //Add customer note
				'woocommerce_wf_packinglist_variation_data'=>'Yes', //Add product variation data
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number', 'email', 'vat'),
				'woocommerce_wf_orderdate_as_invoicedate'=>"Yes",
				'woocommerce_wf_custom_pdf_name' => '[prefix][order_no]',/* Since 2.8.0 */
				'woocommerce_wf_custom_pdf_name_prefix' => 'Invoice_',/* Since 2.8.0 */
				'wf_woocommerce_invoice_free_orders' => 'Yes',
	        	'wf_woocommerce_invoice_free_line_items' => 'Yes', /* Since 2.8.0 , To display the free line items*/
	        	'wf_woocommerce_invoice_prev_install_orders' => 'No',
	        	'wt_pklist_total_tax_column_display_option' => 'amount',
	        	'wf_woocommerce_invoice_show_print_button' => array('order_listing','order_details','order_email'),
				'woocommerce_wt_use_latest_settings_invoice' => 'Yes',
				'wt_pdf_invoice_attachment_wc_email_classes' => array(),
			);
			return $settings;
		}else
		{
			return $settings;
		}
	}

	/** 
	*	@since v3.0.3 - Changed the radio button fields to checkbox
	*	This function is for getting the values for checkbox fields when they are unchecked,
	*	since the PHP will sent the $_POST the unchecked fields.
	*	@since 4.7.0 - Added the field `wt_pdf_invoice_attachment_wc_email_classes` and removed the fields `woocommerce_wf_add_invoice_in_admin_mail`.
	*/
	public function single_checkbox_fields($settings,$base_id,$tab_name){
		if($base_id === $this->module_id)
		{	
			// array of fields with their unchecked values.
			$settings['wt_invoice_general'] = array(
				'woocommerce_wf_enable_invoice'						=> "No",
				'woocommerce_wf_add_'.$this->module_base.'_in_mail'	=> "No",
				'woocommerce_wf_packinglist_frontend_info'			=> "Yes",
				'wf_woocommerce_invoice_prev_install_orders' 		=> "No",
				'wf_woocommerce_invoice_free_orders' 				=> "No",
				'wf_woocommerce_invoice_free_line_items'			=> "No",
				'woocommerce_wt_use_latest_settings_invoice' 		=> "No",
			);
		}

		return $settings;
	}

	/*
	*	@since v3.0.5 - Changed the radio button fields to multi checkbox
	*	This function is for getting the values for checkbox fields when they are unchecked,
	*	since the PHP will sent the $_POST the unchecked fields.
	*/
	public function multi_checkbox_fields($settings, $base_id,$tab_name){
		if($base_id === $this->module_id){
			$settings['wt_invoice_general'] = array(
				'wf_'.$this->module_base.'_contactno_email'		=> array(),
				'wf_woocommerce_invoice_show_print_button'		=> array(),
				'woocommerce_wf_generate_for_orderstatus' 		=> array(),
				'wt_pdf_invoice_attachment_wc_email_classes' 	=> array(),
			);
		}
		return $settings;
	}

	public function save_multi_checkbox_fields($result,$key,$fields,$base_id){
		if($base_id === $this->module_id){
			$result = (isset($fields[$key]) && !isset($_POST[$key])) ? $fields[$key] : $result;
		}
		return $result;
	}

	public function add_bulk_print_buttons($actions)
	{
		$actions['print_invoice']=__('Print Invoices','print-invoices-packing-slip-labels-for-woocommerce');
		$actions['download_invoice']=__('Download Invoices','print-invoices-packing-slip-labels-for-woocommerce');
		return $actions;
	}

	/**
	*	Adding print/download options in Order list/detail page
	*	@since 4.0.0 Show the prompt for free orders, when no invoice number for the free order
	*/
	public function add_print_buttons($item_arr, $order, $order_id, $button_location)
	{
		$invoice_number=self::generate_invoice_number($order,false);
		$generate_invoice_for =Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
		$is_show=0;
		$is_show_prompt=1;
		$order_status = ( WC()->version < '2.7.0' ) ? $order->status : $order->get_status();

		if(in_array('wc-'.$order_status, $generate_invoice_for) || !empty($invoice_number))
        {
        	$is_show_prompt=0;
        	$is_show=1;
		}else
		{
			if(empty($invoice_number))
			{
				$is_show_prompt=1;
				$is_show=1;
			}
		}

		if(empty($invoice_number))
		{
			if("No" === $free_order_enable){
				if(0 === \intval($order->get_total())){
					$is_show_prompt=2;
				}
			}
		}

		if(1 === $is_show)
		{
			//for print button
			$btn_args=array(  
				'action'=>'print_invoice',
				'tooltip'=>__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>$is_show_prompt,
				'button_location'=>$button_location,						
			);

			//for download button
			$btn_args_dw=array(
				'action'=>'download_invoice',
				'tooltip'=>__('Download Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>$is_show_prompt,
				'button_location'=>$button_location,
			);

			if($button_location=='detail_page')
			{
				$btn_args['label']		= __('Print','print-invoices-packing-slip-labels-for-woocommerce');
				$btn_args_dw['label']	= __('Download','print-invoices-packing-slip-labels-for-woocommerce');
				$invoice_number 	= Wt_Pklist_Common::get_order_meta($order_id,'wf_invoice_number',true);
				if(!empty($invoice_number)){
					$invoice_number_exists = true;
				}else{
					$invoice_number_exists = false;
				}
				$item_arr['invoice_details_actions']=array(
					'button_type'=>'aggregate',
					'button_key'=>'invoice_actions', //unique if multiple on same page
					'button_location'=>$button_location,
					'action'=>'',
					'label'=>__('Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
					'tooltip'=>__('Print/Download Invoice','print-invoices-packing-slip-labels-for-woocommerce'),
					'is_show_prompt'=>0, //always 0
					'items'=>array(
						'print_invoice' => $btn_args,
						'download_invoice' => $btn_args_dw
					),
					'exist' => $invoice_number_exists,
				);
			}else
			{
				$btn_args['label']=__('Print Invoice','print-invoices-packing-slip-labels-for-woocommerce');
				$btn_args_dw['label']=__('Download Invoice','print-invoices-packing-slip-labels-for-woocommerce');
				$item_arr[]=$btn_args;
				$item_arr[]=$btn_args_dw;			
			}
		}
		return $item_arr;
	}

	public function add_docdata_metabox($data_arr, $order, $order_id)
	{
		
		$invoice_number=self::generate_invoice_number($order, false);
		if("" !== $invoice_number)
		{
			$data_arr['wf_meta_box_invoice_number']=array(
				'label'=>__('Invoice Number','print-invoices-packing-slip-labels-for-woocommerce'),
				'value'=>$invoice_number,
			);
		}
		return $data_arr;
	}

	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	* 	@since 4.7.0 - Changed the feature to send the attachemnt as per the selected email classes instead of order status
	*
	*/
	public function add_email_attachments($attachments, $order, $order_id, $email_class_id)
	{ 	
		/**
		 * Check if the free order is enabled or not to attach the invoice document.
		 */	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);
		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $attachments;
			}
		}

		$chosen_wc_email_classes = Wf_Woocommerce_Packing_List::get_option( 'wt_pdf_invoice_attachment_wc_email_classes', $this->module_id );
		$chosen_wc_email_classes = apply_filters( 'wf_pklist_alter_'.$this->module_base.'_attachment_mail_type', $chosen_wc_email_classes, $order_id, $email_class_id, $order );
		$chosen_wc_email_classes = array_unique( $chosen_wc_email_classes );
		$generate_invoice_for = Wf_Woocommerce_Packing_List::get_option( 'woocommerce_wf_generate_for_orderstatus', $this->module_id );
		$generate_invoice_for = apply_filters( 'wf_pklist_alter_'.$this->module_base.'_attachment_order_status', $generate_invoice_for, $order_id, $email_class_id, $order );

		/**
		 * Check if the current email class and current order status are selected for attaching the invoice document.
		 * Or if the order has already an invoice number generated.
		 */
		if( !empty( $chosen_wc_email_classes ) && in_array( $email_class_id, $chosen_wc_email_classes ) ) {
			if ( ( !empty( $generate_invoice_for ) && in_array( 'wc-'.$order->get_status(), $generate_invoice_for ) ) ||
				( empty( $generate_invoice_for ) && !empty( Wt_Pklist_Common::get_order_meta( $order_id, 'wf_invoice_number', true )  ) ) ) {
				$attachments[] = $this->prepare_pdf_attachments( $order_id );
			}
		}

        return $attachments;
	}

	/**
	 * To Prepare the PDF attachments
	 *
	 * @param int|string $order_id
	 * @return string	$attachment
	 */
	public function prepare_pdf_attachments( $order_id ) {
		$attachment = '';
		if ( !empty( $order_id ) && !is_null( $this->customizer ) ) {
			$order_ids = array( $order_id );
			$pdf_name = $this->customizer->generate_pdf_name($this->module_base,$order_ids);
			$this->customizer->template_for_pdf = true;
			$html = $this->generate_order_template( $order_ids, $pdf_name );
			$attachment = $this->customizer->generate_template_pdf( $html, $this->module_base, $pdf_name, 'attach' );
		}
		return $attachment;
	}

	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function add_email_print_buttons($html,$order,$order_id)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $html;
			}
		}

		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_email',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);

			if(('Yes' === $show_on_frontend) && (in_array('order_email',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_number', true);
				$show_print_button_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
		        if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$show_print_button_for))
		        {
					$email_btn_label	= apply_filters('wt_pklist_alter_document_button_label',__($this->print_btn_label,'print-invoices-packing-slip-labels-for-woocommerce'),'print','email',$template_type);
		            Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',$email_btn_label,true); 
		        }
		    }
		}
	    return $html;
	}
	
	/**
	*	@since 2.8.0 - Added option to not generate the invoice number for free orders
	*
	*/
	public function add_frontend_print_buttons($html,$order,$order_id)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return $html;
			}
		}
		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_details',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);
			if(('Yes' === $show_on_frontend) && (in_array('order_details',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_number', true);
				$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$generate_invoice_for)){
					$print_btn_label	= apply_filters('wt_pklist_alter_document_button_label',__($this->print_btn_label,'print-invoices-packing-slip-labels-for-woocommerce'),'print','my_account_order_details',$template_type);
					$download_btn_label	= apply_filters('wt_pklist_alter_document_button_label',__($this->download_btn_label,'print-invoices-packing-slip-labels-for-woocommerce'),'download','my_account_order_details',$template_type);
					
					if( true === apply_filters('wt_pklist_show_document_button',true,'print','my_account_order_details',$template_type,$order) ) {
						Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'print_invoice',$print_btn_label);
					}
					
					if( true === apply_filters('wt_pklist_show_document_button',true,'download','my_account_order_details',$template_type,$order) ) {
						Wf_Woocommerce_Packing_List::generate_print_button_for_user($order,$order_id,'download_invoice',$download_btn_label);
					}
					
				}
			}
		}
		return $html;
	}
	
	/**
	 * @since 3.0.0 
	 * Show print invoice button on the order listing page - frontend
	 */
	public function add_frontend_order_list_page_print_buttons($wt_actions, $order, $order_id)
	{
		if($this->is_show_frontend_print_button($order))
		{
			$wt_actions[$this->module_base]=array(
				'print'		=> apply_filters('wt_pklist_alter_document_button_label',__($this->print_btn_label, 'print-invoices-packing-slip-labels-for-woocommerce' ),'print','my_account_order_listing',$this->module_base),
				'download'	=> apply_filters('wt_pklist_alter_document_button_label',__($this->download_btn_label, 'print-invoices-packing-slip-labels-for-woocommerce'),'download','my_account_order_listing',$this->module_base),
			);
		}
		return $wt_actions;
	}

	public function is_show_frontend_print_button($order)
	{	
		$free_order_enable = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_free_orders',$this->module_id);

		if("No" === $free_order_enable){
			if(0 === \intval($order->get_total())){
				return false;
			}
		}
		$template_type=$this->module_base;
		$show_print_button_pages = apply_filters('wt_pklist_show_hide_print_button_in_pages',true,'order_listing',$template_type,$order);
		
		if($show_print_button_pages){
			$show_on_frontend=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_frontend_info',$this->module_id);
			$show_print_button_arr = Wf_Woocommerce_Packing_List::get_option('wf_woocommerce_invoice_show_print_button',$this->module_id);
			if(('Yes' === $show_on_frontend) && (in_array('order_listing',$show_print_button_arr)))
			{
				$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
	    		$wf_invoice_id = Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_number', true);
				$generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus',$this->module_id);
				if("" !== trim($wf_invoice_id) || in_array('wc-'.$order->get_status(),$generate_invoice_for)){
					return true;
				}
			}
		}
	    return false;
	}

	/**
	* 	Print_window for invoice
	* 	@param $orders : order ids
	*	@param $action : (string) download/preview/print
	*	@since 2.6.2 Added compatibilty preview PDF
	*/    
    public function print_it($order_ids, $action) 
    {
    	$template_type=$this->module_base;
    	if("print_invoice" === $action || "download_invoice" === $action || "preview_invoice" === $action)
    	{   
    		if("Yes" !== $this->is_enable_invoice) /* invoice not enabled so only allow preview option */
    		{
    			if("print_invoice" === $action || "download_invoice" === $action)
    			{
    				return;	
    			}else
    			{
    				if(!Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	                {
	                	return;
	                }
    			}
    		}
    		if(!is_array($order_ids))
    		{
    			return;
    		}

	        if(!is_null($this->customizer))
	        {
				if( count( $order_ids ) > 1 ) {
					$sort_order = apply_filters( 'wt_pklist_sort_orders', 'desc', $this->module_base, $action ); // To choose the sorting of the orders when doing bulk print or download.
					if ( 'asc' ===  $sort_order ) {
						sort( $order_ids );
					}
				}

				$pdf_name=$this->customizer->generate_pdf_name($this->module_base, $order_ids);
	        	if("download_invoice" === $action || "preview_invoice" === $action)
	        	{
					if(!isset($_GET['dbg'])){
						ob_start();
					}
	        		$this->customizer->template_for_pdf=true;

	        		if("preview_invoice" === $action)
		        	{
		        		$html=$this->customizer->get_preview_pdf_html($this->module_base);
		        		$html=$this->generate_order_template_for_invoice_preview($order_ids, $pdf_name, $html,$action);
		        	}else
		        	{
		        		$html=$this->generate_order_template($order_ids, $pdf_name);	
		        	}
		        	$html = Wf_Woocommerce_Packing_List_Admin::qrcode_barcode_visibility($html,$template_type);
		        	$action=str_replace('_'.$this->module_base, '', $action);
	        		$this->customizer->generate_template_pdf($html, $this->module_base, $pdf_name, $action);
	        		if(!isset($_GET['dbg'])){
						ob_end_clean();
					}
	        	}else
	        	{
	        		if(!isset($_GET['dbg'])){
						ob_start();
					}
	        		$html=$this->generate_order_template($order_ids, $pdf_name,"",$action);
	        		$html = Wf_Woocommerce_Packing_List_Admin::qrcode_barcode_visibility($html,$template_type);
	        		if(!isset($_GET['dbg'])){
						ob_end_clean();
					}
	        		echo $html;
	        	}
	        }else
	        {
	        	_e('Customizer module is not active.', 'print-invoices-packing-slip-labels-for-woocommerce');
	        }
	        exit();
    	}
    }

	public function generate_order_template($orders,$page_title,$html="",$action=""){
		$template_type = $this->module_base;
		$number_of_orders = count($orders);
    	$order_inc = 0;
    	$out = '';

		foreach($orders as $order_id){
			$lang	= ( isset( $_GET['lang'] ) ? sanitize_text_field( $_GET['lang'] ) : get_locale() );
			$lang	= apply_filters('wt_pklist_alter_document_language_for_'.$template_type,$lang,$template_type,$order_id);
			$lang	= apply_filters('wt_pklist_alter_document_language',$lang,$template_type,$order_id);
			do_action('wt_pklist_language_switcher_for_'.$template_type,$lang,$template_type,$order_id);
    		$order_inc++;
    		$out .= $this->generate_order_template_for_single_order($order_id,$page_title,$html,$action);
    		$document_created = Wf_Woocommerce_Packing_List_Admin::created_document_count($order_id,$template_type);
    	}

		
		/**
		 * bulk download page break
		 * @since 4.5.1 - [Fix] - mPDF library shows empty pages.
		 * @since 4.5.2 - [Fix] - The second order over rides to the first page (a4) when bulk printing.
		 */
		if( 1 < $number_of_orders && ( "print_invoice" === $action || false === Wf_Woocommerce_Packing_List_Admin::check_if_mpdf_used() ) ) {
			$out	= str_replace('</body>','<div class="pagebreak"></div></body>',$out);
		}
		
    	return $out;
	}

	public function generate_order_template_for_single_order($order_id,$page_title,$html,$action){
    	$use_latest_settings_invoice	= Wf_Woocommerce_Packing_List::get_option('woocommerce_wt_use_latest_settings_invoice', $this->module_id);
    	$template_type	= $this->module_base;
    	$order			= Wt_Pklist_Common::get_order($order_id);
		/**
		 * @since 4.6.0 - Added filter to add before preparing the order package and rendering the html.
		 */
		$pdf_filters = apply_filters( 'wt_pklist_add_filters_before_rendering_pdf', array(), $this->module_base, $order );
		Wt_Pklist_Common::wt_pklist_pdf_add_filters( $pdf_filters );
		$invoice_html 	= Wt_Pklist_Common::get_order_meta($order_id, 'wf_invoice_html', true);
		$order_id_arr[] = $order_id;
		$pdf_name		= $this->customizer->generate_pdf_name($this->module_base, $order_id_arr);
		$html 			= "";
		$out 			= "";

		$upload_loc		= Wf_Woocommerce_Packing_List::get_temp_dir();
		$upload_dir		= $upload_loc['path'];
        $upload_url		= $upload_loc['url'];

		if(!empty($invoice_html)){
	        $file_loc=$upload_dir.'/'.$template_type. '/'.$invoice_html;
	        if(!file_exists($file_loc)){
	        	$new_invoice_html_set = 1;
	        }else{
	        	$new_invoice_html_set = 0;
	        	$html_file=@fopen($file_loc,'r');
  				$html=fread($html_file,filesize($file_loc));
	        }
		}else{
			$new_invoice_html_set = 1;
		}

		

		if((trim($html)=="") || ($use_latest_settings_invoice == "Yes"))
    	{
    		$html=$this->customizer->get_template_html($template_type);
    		$new_invoice_html_set = 1;

    		$style_blocks=$this->customizer->get_style_blocks($html);
	    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);

	    	$out.=$this->customizer->generate_template_html($html,$template_type,$order);

	        $out=$this->customizer->append_style_blocks($out,$style_blocks);
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
    	}else{
    		$payment_method_slug	= $order->get_payment_method();
			$paymethod_title	= $order->get_payment_method_title();
    		$paymethod_title	= __($paymethod_title, 'print-invoices-packing-slip-labels-for-woocommerce');
    		$custom_payment_key = "custom_payment_key";
    		$custom_script_regex = '/<script id="custom_payment_key"[^>]*>[\s\S]*'.$custom_payment_key.'[\s\S]*?<\/script>/';

    		$order_statuses_arr = array('wc-on-hold','wc-pending','wc-failed');
			if(!in_array('wc-'.$order->get_status(), $order_statuses_arr)){
				$updated_script = '<script id="custom_payment_key">var custom_render_block_key = "custom_payment_key";let current = document.querySelector(".wfte_product_table_payment_method_label");let nextSibling = current.nextElementSibling;console.log(nextSibling.innerHTML);nextSibling.innerHTML="'.$paymethod_title.'";let paylink_elm = document.querySelector(".wfte_payment_link");paylink_elm.classList.add("wfte_hidden"); </script>';
			}else{
				$updated_script = '<script id="custom_payment_key">var custom_render_block_key = "custom_payment_key";let current = document.querySelector(".wfte_product_table_payment_method_label");let nextSibling = current.nextElementSibling;console.log(nextSibling.innerHTML);nextSibling.innerHTML="'.$paymethod_title.'"</script>';
			}

    		if(preg_match($custom_script_regex, $html)){
		     	$html = preg_replace($custom_script_regex, $updated_script, $html);
	     	}else{
	     		$html .=$updated_script;
	     	}

    		$out .= $html;

    		$new_invoice_html_set = 1;

    	}

    	$style_regex = '/<style id="template_font_style"[^>]*>[\s\S]*?<\/style>/';
    	$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{/*position:absolute;bottom:0px;*/}</style>';

    	$footer_to_the_bottom = apply_filters('wt_pklist_footer_to_the_bottom',true,$order,$template_type);
    	if($footer_to_the_bottom){
    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;}</style>';
	    	
	    	$is_mpdf_used = Wf_Woocommerce_Packing_List_Admin::check_if_mpdf_used();
	    	if(is_rtl() && ($is_mpdf_used === true)){
	    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;right:0px;}</style>';
	    	}
    	}

    	if($action == "print_invoice"){
    		$updated_style = '<style id="template_font_style">*{/*font-family:"DeJaVu Sans", monospace;*/}.template_footer{/*position:absolute;bottom:0px;*/}</style>';
    	}

     	if(preg_match($style_regex, $out)){
 			$out = preg_replace($style_regex, $updated_style, $out);
 		}
 		
		if($new_invoice_html_set === 1){
	        if(!is_dir($upload_dir))
	        {
	            @mkdir($upload_dir, 0700);
	        }

	        //document type specific subfolder
	        $upload_dir=$upload_dir.'/'.$template_type;
	        $upload_url=$upload_url.'/'.$template_type;
	        if(!is_dir($upload_dir))
	        {
	            @mkdir($upload_dir, 0700);
	        }

	        //if directory successfully created
	        if(is_dir($upload_dir))
	        {	
	        	$file_name = $pdf_name.'.html';
	        	$file_path=$upload_dir . '/'.$pdf_name.'.html';
        		$file_url=$upload_url . '/'.$pdf_name.'.html';
	        	//$myfile = fopen($file_path, "w") or die("Unable to open file!");
	        	$fh=@fopen($file_path, "w");
	            if(is_resource($fh))
	            {
	                fwrite($fh,$out);
	                fclose($fh);
	            }
				Wt_Pklist_Common::update_order_meta($order_id,'wf_invoice_html',$file_name);
	        }
		}
		
		/**
		 * @since 4.6.0 - Remove the filters which were added before preparing the order package and rendering the html.
		 */
		Wt_Pklist_Common::wt_pklist_pdf_remove_filters( $pdf_filters );
		
		return $out;
    }

    public function generate_order_template_for_invoice_preview($orders,$page_title,$html="",$action="")
    {
    	$template_type=$this->module_base;
    	if("" === $html)
    	{
    		//taking active template html
    		$html=$this->customizer->get_template_html($template_type);
    	}
    	$style_blocks=$this->customizer->get_style_blocks($html);
    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);
    	$out='';
    	if("" !== $html)
    	{
    		$number_of_orders=count($orders);
			$order_inc=0;
			foreach($orders as $order_id)
			{
				$order_inc++;
				$order	= Wt_Pklist_Common::get_order($order_id);
				if(count($orders)>1)
				{
					self::generate_invoice_number($order,true,'set');
				}
				$out.=$this->customizer->generate_template_html($html,$template_type,$order);
				if("preview_invoice" !== $action){
					$document_created = Wf_Woocommerce_Packing_List_Admin::created_document_count($order_id,$template_type);
				}
				if($number_of_orders>1 && $order_inc<$number_of_orders)
				{
                	$out.='<p class="pagebreak"></p>';
	            }else
	            {
	                //$out.='<p class="no-page-break"></p>';
	            }
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
    	}
    	$style_regex = '/<style id="template_font_style"[^>]*>[\s\S]*?<\/style>/';
    	$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{/*position:absolute;bottom:0px;*/}</style>';

    	$footer_to_the_bottom = apply_filters('wt_pklist_footer_to_the_bottom',true,$order,$template_type);

    	if($footer_to_the_bottom){
    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;}</style>';
	    	
	    	$is_mpdf_used = Wf_Woocommerce_Packing_List_Admin::check_if_mpdf_used();
	    	if(is_rtl() && ($is_mpdf_used === true)){
	    		$updated_style = '<style id="template_font_style">*{font-family:"DeJaVu Sans", monospace;}.template_footer{position:absolute;bottom:0px;right:0px;}</style>';
	    	}
    	}

    	if($action == "print_invoice"){
    		$updated_style = '<style id="template_font_style">*{/*font-family:"DeJaVu Sans", monospace;*/}.template_footer{/*position:absolute;bottom:0px;*/}</style>';
    	}

     	if(preg_match($style_regex, $out)){
 			$out = preg_replace($style_regex, $updated_style, $out);
 		}
    	return $out;
    }

	/**
	 * Add the document type as one of the options for the individual print button access 
	 *
	 * @param array $documents
	 * @return array
	 */
	public function add_individual_print_button_in_admin_order_listing_page($documents) {
		if( !in_array( $this->module_base, $documents ) ) {
			$documents[$this->module_base] = __("Invoice","print-invoices-packing-slip-labels-for-woocommerce");
		}
		return $documents;
	}

	/**
	 * Add document print button as per the 'wt_pklist_separate_print_button_enable' value
	 *
	 * @since 4.2.0
	 * @param object $order
	 * @return void
	 */
	public function document_print_btn_on_wc_order_listing_action_column( $order ) {
		$show_print_button	= apply_filters('wt_pklist_show_document_print_button_action_column_free', true, $this->module_base, $order);

		if( !empty( $order ) && true === $show_print_button ) {
			$order_id	= ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id();
			
			if( in_array( $this->module_base, Wf_Woocommerce_Packing_List::get_option( 'wt_pklist_separate_print_button_enable' ) ) ) {
				$invoice_number		= Wt_Pklist_Common::get_order_meta( $order_id, 'wf_invoice_number', true );
				$btn_action_name 	= 'wt_pklist_print_document_'.$this->module_base;
				$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'_logo.png';
				$invoice_no_set = true;
				
				if( empty( $invoice_number ) ) {
					$invoice_no_set = false;
					$btn_action_name	= $btn_action_name.'_not_yet';
					$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'.png';
				}

				$action			= 'print_'.$this->module_base;
				$action_title 	= sprintf( '%1$s %2$s',
					__("Print","print-invoices-packing-slip-labels-for-woocommerce"),
					$this->module_title,
					);
				$print_url		= Wf_Woocommerce_Packing_List_Admin::get_print_url($order_id,$action);
				
				if ( true === $invoice_no_set ) {
					echo '<a title="'.esc_attr($action_title).'" class="button wc-action-button wc-action-button-'.esc_attr($btn_action_name).' '.esc_attr($btn_action_name).' wt_pklist_action_btn wt_pklist_admin_print_document_btn" href="'.esc_url_raw($print_url).'" aria-label="'.esc_attr($action_title).'" target="_blank" style="padding:5px;"><img src="'.esc_url($img_url).'" ></a>';
				} else {
					$is_show_prompt = 1;
					$free_order_enable = Wf_Woocommerce_Packing_List::get_option( 'wf_woocommerce_invoice_free_orders',$this->module_id );

					if ( "No" === $free_order_enable ) {
						if ( 0 === \intval ( $order->get_total() ) ){
							$is_show_prompt = 2;
						}
					}
                    $onlick = "return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers('".esc_url_raw($print_url)."',$is_show_prompt)";
                    echo '<a title="'.esc_attr($action_title).'" class="button wc-action-button wc-action-button-'.esc_attr($btn_action_name).' '.esc_attr($btn_action_name).' wt_pklist_action_btn" onclick="'.$onlick.'" aria-label="'.esc_attr($action_title).'" target="_blank" style="padding:5px;"><img src="'.esc_url($img_url).'" ></a>';
                    echo '<a class="wt_pklist_empty_number" data-template-type="'.esc_attr($this->module_base).'" data-id="'.esc_attr($order_id).'" style="display:none;"></a>';
                }		
			}
		}
	}

	/**
	 * To clean the invoice number format values upon the first installation
	 *
	 * @since 4.2.0
	 * 
	 * @return void
	 */
	public function invoice_settings_on_plugin_update(){
		$invoice_no_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_format',$this->module_id);
		if("[number]" === $invoice_no_format)
		{
			Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_invoice_number_prefix','',$this->module_id);
			Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_invoice_number_postfix','',$this->module_id);
		}
		elseif("[number][suffix]" === $invoice_no_format)
		{
			Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_invoice_number_prefix','',$this->module_id);
		}
		elseif("[prefix][number]" === $invoice_no_format)
		{
			Wf_Woocommerce_Packing_List::update_option('woocommerce_wf_invoice_number_postfix','',$this->module_id);
		}
	}

	/**
	 * Get the saved data of the invoice module
	 *
	 * @since 4.5.0
	 * @param array $plugin_data
	 * @param string $page
	 * @return array
	 */
	public function get_plugin_data( $plugin_data, $page ) {
		return ( $this->module_id === $page ) ? array( $this->module_base =>  get_option( $this->module_id ) ) : $plugin_data;
	}

	public function get_attachment_for_ubl_invoice( $order_ids ) {
		$this->customizer = new Wf_Woocommerce_Packing_List_Customizer();
		$this->module_base = 'invoice';
		$attachments = array();
		$pdf_name = $this->customizer->generate_pdf_name( $this->module_base, $order_ids );
		$this->customizer->template_for_pdf = true;
		$html = $this->generate_order_template( $order_ids, $pdf_name );
		$attachments[] = $this->customizer->generate_template_pdf( $html, $this->module_base, $pdf_name, 'attach' );
		return $attachments;
	}

	public function get_attachment_name_for_ubl_invoice( $order_ids ) {
		$this->customizer = new Wf_Woocommerce_Packing_List_Customizer();
		return $this->customizer->generate_pdf_name( $this->module_base, $order_ids );
	}
}
new Wf_Woocommerce_Packing_List_Invoice();





