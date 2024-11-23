<?php
/**
 * Packinglist section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Shippinglabel
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='shippinglabel';
	public $module_title='';
    private $customizer=null;
	public function __construct()
	{
		$this->module_id=Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		self::$module_id_static=$this->module_id;
		$this->module_title=__("Shipping label","print-invoices-packing-slip-labels-for-woocommerce");

		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);

		/**
		*	@since 2.6.9
		*	Hooks to customizer right panel
		*/
		add_filter('wf_module_customizable_items',array($this,'get_customizable_items'),10,2);
		add_filter('wf_module_non_options_fields',array($this,'get_non_options_fields'),10,2);
		add_filter('wf_module_non_disable_fields',array($this,'get_non_disable_fields'),10,2);
		add_filter('wf_pklist_alter_customize_inputs',array($this,'alter_customize_inputs'),10,3);
		//hook to add which fiedls to convert
		add_filter('wf_module_convert_to_design_view_html_for_'.$this->module_base,array($this,'convert_to_design_view_html'),10,3);

		//hook to generate template html
		add_filter('wf_module_generate_template_html_for_'.$this->module_base,array($this,'generate_template_html'),10,6);
		
		//hide empty fields on template
		add_filter('wf_pklist_alter_hide_empty',array($this,'hide_empty_elements'),10,6);

		add_action('wt_print_doc',array($this,'print_it'),10,2);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		add_filter('wt_print_actions',array($this,'add_print_buttons'),10,4);
		add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));
		
		add_filter('wt_pklist_alter_tooltip_data',array($this,'register_tooltips'),1);

		/* @since 2.6.9 add admin menu */
		add_filter('wt_admin_menu', array($this,'add_admin_pages'),10,1);
		add_filter('wt_pklist_individual_print_button_for_document_types',array($this,'add_individual_print_button_in_admin_order_listing_page'),10,1);
		add_filter( 'woocommerce_admin_order_actions_end', array( $this, 'document_print_btn_on_wc_order_listing_action_column' ), 10, 1 );

		add_filter( 'wt_pklist_hide_shipping_address_for_local_pickup', array( $this, 'hide_shipping_address_for_local_pickup' ),10,3);
        add_filter( 'wt_pklist_use_billing_address_as_shipping_address', array($this, 'use_billing_address_as_shipping_address'), 10, 3);
	}

	/**
	 *	@since 2.6.9
	 *  Items needed to be converted to design view
	 */
	public function convert_to_design_view_html($find_replace,$html,$template_type)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_logo($find_replace,$template_type);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_from_address($find_replace,$template_type);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type);	
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_default_order_fields($find_replace,$template_type,$html);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html);
			$find_replace=$this->extra_fields_dummy_data($find_replace,$html,$template_type);
			$find_replace = $this->toggle_qrcode($find_replace);
		}
		return $find_replace;
	}

	/**
	 *	@since 2.6.9
	 *  Dummy data for extra fields on template
	 */
	private function extra_fields_dummy_data($find_replace,$html,$template_type)
	{
		$find_replace['[wfte_weight]']='10 Kg';
		$find_replace['[wfte_ship_date]']=date('d-m-Y');
		$find_replace['[wfte_additional_data]']='';
		return $find_replace;
	}

	/**
	 *	@since 2.6.9
	 *  Alter customizer inputs
	 */
	public function alter_customize_inputs($fields,$type,$template_type)
	{
		if($template_type === $this->module_base)
		{
			if("from_address" === $type || "shipping_address" === $type)
			{
				$fields=array(
					array(
						'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
						'css_prop'=>'html',
						'trgt_elm'=>$type.'_label',
					),
					array(
						'label'=>__('Title font size','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'font-size',
						'trgt_elm'=>$type.'_label',
						'width'=>'49%',
					),
					array(
						'label'=>__('Address font size','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'font-size',
						'trgt_elm'=>$type.'_val',
						'width'=>'49%',
						'float'=>'right',
					),
				);
			}
			elseif("tel" === $type || "weight" === $type || "ship_date" === $type)
			{
				$fields=array(
					array(
						'label'=>__('Title','print-invoices-packing-slip-labels-for-woocommerce'),
						'css_prop'=>'html',
						'trgt_elm'=>$type.'_label',
					),
					array(
						'label'=>__('Title font size','print-invoices-packing-slip-labels-for-woocommerce'),
						'type'=>'text_inputgrp',
						'css_prop'=>'font-size',
						'trgt_elm'=>$type,
					)
				);
			}
		}
		return $fields;
	}

	/**
	 *	@since 2.6.9
	 *  Which items need enable in right customization panel
	 */
	public function get_customizable_items($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			$only_pro_html='<span class="wt_customizer_pro_text" style="color:red;"> ('.__('Pro version','print-invoices-packing-slip-labels-for-woocommerce').')</span>';
			//these fields are the classname in template Eg: `company_logo` will point to `wfte_company_logo`
			$settings = array(
				'company_logo_pro_element'=>__('Company Logo','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'from_address'=>__('From Address','print-invoices-packing-slip-labels-for-woocommerce'),
				'shipping_address'=>__('To Address','print-invoices-packing-slip-labels-for-woocommerce'),				
				'order_number'=>__('Order Number','print-invoices-packing-slip-labels-for-woocommerce'),	
				'order_date'=>__('Order Date','print-invoices-packing-slip-labels-for-woocommerce'),				
				'weight'=>__('Weight','print-invoices-packing-slip-labels-for-woocommerce'),	
				'shipping_method'=>__('Shipping Method','print-invoices-packing-slip-labels-for-woocommerce'),	
				'email'=>__('Email Field','print-invoices-packing-slip-labels-for-woocommerce'),
				'tel'=>__('Tel Field','print-invoices-packing-slip-labels-for-woocommerce'),
				'vat_number'=>__('VAT number','print-invoices-packing-slip-labels-for-woocommerce'),
				'ssn_number'=>__('SSN number','print-invoices-packing-slip-labels-for-woocommerce'),
				'customer_note'=>__('Customer note','print-invoices-packing-slip-labels-for-woocommerce'),
				'footer' => __('Footer','print-invoices-packing-slip-labels-for-woocommerce'),
				'barcode_pro_element' => __('Barcode','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'tracking_number_pro_element' => __('Tracking Number','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
				'package_no_pro_element' => __('Package Number','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
                'box_name_pro_element' => __('Box name','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
                'total_no_of_items_pro_element' => __('No of Items','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
                'fragile_pro_element'=>__('Fragile','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
                'thiswayup_pro_element'=>__('This way up','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
                'keepdry_pro_element'=>__('Keep dry','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html,
			);
			$settings['return_policy_pro_element'] = __('Return Policy','print-invoices-packing-slip-labels-for-woocommerce').$only_pro_html;
			return $settings;
		}
		return $settings;
	}

	/**
	*	@since 2.6.9
	* 	These are the fields that have no customizable options, Just on/off
	* 
	*/
	public function get_non_options_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			return array(
				'footer',
				'barcode',
				'return_policy',
			);
		}
		return $settings;
	}

	/**
	*	@since 2.6.9
	* 	These are the fields that are switchable
	* 
	*/
	public function get_non_disable_fields($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			return array(
				'from_address',
				'shipping_address',
			);
		}
		return $settings;
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
			__('Shipping label','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Shipping label','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			$this->module_id,
			array($this, 'admin_settings_page')
		);
		return $menus;
	}

	/**
	*  	Admin settings page
	*	@since 	2.6.9
	*/
	public function admin_settings_page()
	{
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles',WC()->plugin_url().'/assets/css/admin.css');
		wp_enqueue_media();
		if(!is_plugin_active('wt-woocommerce-shippinglabel-addon/wt-woocommerce-shippinglabel-addon.php') && isset($_GET['page']) && "wf_woocommerce_packing_list_shippinglabel" === $_GET['page']){
			wp_enqueue_script($this->module_id.'-pro-cta-banner',plugin_dir_url( __FILE__ ).'assets/js/pro-cta-banner.js',array('jquery'),WF_PKLIST_VERSION);
		}
		$params=array(
			'nonces' => array(
	            'main'=>wp_create_nonce($this->module_id),
	        ),
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'msgs'=>array(
	        	'enter_order_id'=>__('Please enter order number','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'generating'=>__('Generating','print-invoices-packing-slip-labels-for-woocommerce'),
	        	'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
	        )
		);
		wp_localize_script($this->module_id,$this->module_id,$params);
		include_once WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
		$the_options=Wf_Woocommerce_Packing_List::get_settings($this->module_id);

	    //initializing necessary modules, the argument must be current module name/folder
	    if(!is_null($this->customizer) && true === apply_filters('wt_pklist_switch_to_classic_customizer_'.$this->module_base, true, $this->module_base))
		{
			$this->customizer->init($this->module_base);
		}

		$template_type = $this->module_base;
		include(plugin_dir_path( __FILE__ ).'views/admin-settings.php');
	}

	private function toggle_qrcode($find_replace)
	{
		$template_type=$this->module_base;
		$show_qrcode_placeholder = false;
		$show_qrcode_placeholder = apply_filters('wt_pklist_show_qrcode_placeholder_in_template',$show_qrcode_placeholder,$template_type);
		if(false === $show_qrcode_placeholder)
		{
			$find_replace['wfte_qrcode']='wfte_hidden';
		}
		return $find_replace;
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

	public function hide_empty_elements($hide_on_empty_fields,$template_type)
	{
		if($template_type === $this->module_base)
		{
			$hide_on_empty_fields[]='wfte_qrcode';
			$hide_on_empty_fields[]='wfte_box_name';
			$hide_on_empty_fields[]='wfte_ship_date';
			$hide_on_empty_fields[]='wfte_weight';
			$hide_on_empty_fields[]='wfte_barcode';
		}
		return $hide_on_empty_fields;
	}

	/**
	 *  Items needed to be converted to HTML for print
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);	
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_default_order_fields($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_other_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_order_data($find_replace,$template_type,$html,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_fields($find_replace,$template_type,$html,$order);
		}
		return $find_replace;
	}

	public function default_settings($settings,$base_id)
	{
		if($base_id === $this->module_id)
		{
			return array(
				'woocommerce_wf_packinglist_label_size'=>2, //full page
				'woocommerce_wf_enable_multiple_shipping_label'=>'Yes',
				'woocommerce_wf_packinglist_footer_sl'=>'Yes',
				'wf_shipping_label_column_number'=>1,
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number','email'),
			);
		}else
		{
			return $settings;
		}
	}

	public function add_bulk_print_buttons($actions)
	{
		$actions['print_shippinglabel']=__('Print Shipping Label','print-invoices-packing-slip-labels-for-woocommerce');
		return $actions;
	}
	
	/**
	* @since 4.0.0	Adding print/download options in Order list/detail page
	*
	*/
	public function add_print_buttons($item_arr, $order, $order_id, $button_location)
	{
		if("detail_page" === $button_location)
		{
			$data_ar=array(
				'button_type'=>'aggregate',
				'button_key'=>'shippinglabel_actions', //unique if multiple on same page
				'button_location'=>$button_location,
				'action'=>'',
				'label'=>__('Shipping Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'tooltip'=>__('Print Shipping Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>0, //always 0
				'items'=>array(),
				'exist' => Wf_Woocommerce_Packing_List_Admin::check_doc_already_created($order,$order_id,'shippinglabel'),
			);
			$data_ar['items']['print_shippinglabel']=array(  
				'action'=>'print_shippinglabel',
				'label'=>__('Print','print-invoices-packing-slip-labels-for-woocommerce'),
				'tooltip'=>__('Print Shipping Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>0,
				'button_location'=>$button_location,						
			);
			$item_arr['shippinglabel_details_actions']=$data_ar;

		}else
		{
			$item_arr[]=array(
				'action'=>'print_shippinglabel',
				'label'=>__('Print Shipping Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'tooltip'=>__('Print Shipping Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>0,
				'button_location'=>$button_location,
			);
		}
		return $item_arr;
	}
	
	/* 
	* Print_window for shippinglabel
	* @param $orders : order ids
	*/    
    public function print_it($order_ids,$action) 
    {
    	$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
        if(!$is_pro_customizer)
        {
        	if("print_shippinglabel" === $action)
	    	{   
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
					
		        	$pdf_name=$this->customizer->generate_pdf_name($this->module_base,$order_ids);
		        	
		        	//RTL enabled
		        	if("Yes" === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_rtl_support'))
		        	{
		        		$this->customizer->custom_css.='';
		        	}
		        	$html=$this->generate_order_template($order_ids,$pdf_name);
		        	echo $html;
		        }
		        exit();
	    	}
        }
    }
    public function generate_order_template($orders,$page_title)
    {
    	/* if(false === Wf_Woocommerce_Packing_List::is_from_address_available()) 
    	{
    		wp_die(__("Please add shipping from address in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'), "", array());
        } */

    	$template_type=$this->module_base;
    	//taking active template html
    	$html=$this->customizer->get_template_html($template_type);
    	$style_blocks=$this->customizer->get_style_blocks($html);
    	$html=$this->customizer->remove_style_blocks($html,$style_blocks);
    	$out='<style type="text/css">
    	.wfte_main{ margin:5px;}
    	div{ page-break-inside:avoid;}
    	</style>';
    	$out_arr=array();
    	if("" !== $html)
    	{	
    		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
    		$multilabel_on_page = apply_filters('wt_pklist_enable_multi_shippinglabel_on_page',false,$this->module_base);
    		if(!$is_pro_customizer && !$multilabel_on_page){
    			$is_single_page_print = "No";
    		}else{
    			$is_single_page_print = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_enable_multiple_shipping_label',$this->module_id);
    		}
    		$label_column_number=(int)Wf_Woocommerce_Packing_List::get_option('wf_shipping_label_column_number',$this->module_id);
			if($label_column_number<=0)
			{
                $label_column_number=4;
            }

            //box packing
    		if (!class_exists('Wf_Woocommerce_Packing_List_Box_packing_Basic')) {
		        include_once WF_PKLIST_PLUGIN_PATH.'includes/class-wf-woocommerce-packing-list-box_packing.php';
		    }
	        $box_packing=new Wf_Woocommerce_Packing_List_Box_packing_Basic();
	        $order_pack_inc=0;
	        if("Yes" === $is_single_page_print) //when paper size is not fit to handle labels, then shrink it or keep dimension, Default: shrink
			{
				$keep_label_dimension=false;
				$keep_label_dimension=apply_filters('wf_pklist_label_keep_dimension',$keep_label_dimension,$template_type);
			}
	        foreach ($orders as $order_id)
	        {
	        	$order = ( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);

				/**
				 * @since 4.6.0 - Added filter to add before preparing the order package and rendering the html.
				 */
				$pdf_filters = apply_filters( 'wt_pklist_add_filters_before_rendering_pdf', array(), $this->module_base, $order );
				Wt_Pklist_Common::wt_pklist_pdf_add_filters( $pdf_filters );

				$order_packages=null;
				$order_packages=$box_packing->wf_pklist_create_order_single_package($order);
				$number_of_order_package=count($order_packages);
				if(!empty($order_packages)) 
				{
					foreach ($order_packages as $order_package_id => $order_package)
					{
						if("Yes" === $is_single_page_print)
						{
							if(0 === ($order_pack_inc%$label_column_number))
							{
								if($order_pack_inc>0) //not starting of loop
								{
									$out.='</div>'; 
								}
								$flex_wrap=$keep_label_dimension ? 'wrap' : 'nowrap';
								$out.='<div style="align-items:start; display:flex; flex-direction:row; flex-wrap:'.$flex_wrap.'; align-content:flex-start; align-items:stretch;">'; //comment this line to give preference to label size
							}
						}
						$order_pack_inc++;
						$order=( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);						
						if("No" === $is_single_page_print)
						{
							$out_arr[]=$this->customizer->generate_template_html($html,$template_type,$order,$box_packing,$order_package);
						}else
						{
							$out.=$this->customizer->generate_template_html($html,$template_type,$order,$box_packing,$order_package);	
						}						
					}
					$document_created = Wf_Woocommerce_Packing_List_Admin::created_document_count($order_id,$template_type);
				}else
				{
					$no_item_error_message = __("Unable to print shipping label. Please check the items in the order.",'print-invoices-packing-slip-labels-for-woocommerce');
                    if( 'No' === Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_preview') && 'print_shippinglabel' === $action ) {
                        header('Content-Type: text/plain');
                        // Sanitize the error message to avoid potential issues.
                        $no_item_error_message = htmlspecialchars($no_item_error_message, ENT_QUOTES, 'UTF-8');
                        // Return the error message.
                        echo $no_item_error_message;
                        // Make sure to stop further execution.
                        exit();
                    } else {
                        wp_die($no_item_error_message, "", array());
                    }
				}

				/**
				 * @since 4.6.0 - Remove the filters which were added before preparing the order package and rendering the html.
				 */
				Wt_Pklist_Common::wt_pklist_pdf_remove_filters( $pdf_filters );
			}
			if("Yes" === $is_single_page_print)
			{
				if($order_pack_inc>0) //items exists
				{
					$out.='</div>';
				}
			}else
			{
				$out=implode('<p class="pagebreak"></p>',$out_arr).'<p class="no-page-break">';
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
			//adding header and footer
			$out=$this->customizer->append_header_and_footer_html($out,$template_type,$page_title);
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
			$documents[$this->module_base] = __("Shipping label","print-invoices-packing-slip-labels-for-woocommerce");
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
				$btn_action_name 	= 'wt_pklist_print_document_'.$this->module_base.'_not_yet';
				$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'.png';
				$order_docs			= Wt_Pklist_Common::get_order_meta( $order_id, '_created_document', true );
				$order_docs_old		= Wt_Pklist_Common::get_order_meta( $order_id, '_created_document_old', true );
				
				if( ( !empty( $order_docs ) && in_array( $this->module_base, $order_docs ) ) || ( !empty( $order_docs_old ) && in_array( $this->module_base, $order_docs_old ) ) ) {
					$btn_action_name	= 'wt_pklist_print_document_'.$this->module_base;
					$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'_logo.png';
				}

				$action 		= 'print_'.$this->module_base;
				$action_title 	= sprintf( '%1$s %2$s',
					__("Print","print-invoices-packing-slip-labels-for-woocommerce"),
					$this->module_title
					);
				$print_url		= Wf_Woocommerce_Packing_List_Admin::get_print_url($order_id,$action);
				echo '<a title="'.esc_attr($action_title).'" class="button wc-action-button wc-action-button-'.esc_attr($btn_action_name).' '.esc_attr($btn_action_name).' wt_pklist_action_btn wt_pklist_admin_print_document_btn" href="'.esc_url_raw($print_url).'" aria-label="'.esc_attr($action_title).'" target="_blank" style="padding:5px;"><img src="'.esc_url($img_url).'"></a>';
			}
		}
	}

	public function hide_shipping_address_for_local_pickup( $show_shipping_address_for_local_pickup, $template_type, $order ) {
        return $this->module_base === $template_type ? false : $show_shipping_address_for_local_pickup;
    }

    public function use_billing_address_as_shipping_address( $show_shipping_address_for_local_pickup, $template_type, $order ) {
        return $this->module_base === $template_type ? true : $show_shipping_address_for_local_pickup;
    }
}
new Wf_Woocommerce_Packing_List_Shippinglabel();