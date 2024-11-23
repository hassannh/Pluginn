<?php
/**
 * Dispatch Label section of the plugin
 *
 * @link       
 * @since 2.5.0     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}

class Wf_Woocommerce_Packing_List_Dispatchlabel
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='dispatchlabel';
	public $module_title='';
    private $customizer=null;
	public function __construct()
	{
		$this->module_id=Wf_Woocommerce_Packing_List::get_module_id($this->module_base);
		self::$module_id_static=$this->module_id;
		$this->module_title=__('Dispatch label', 'print-invoices-packing-slip-labels-for-woocommerce');

		/* @since 4.0.0 add admin menu */
		add_filter('wt_admin_menu', array($this,'add_admin_pages'),10,1);
		add_filter('wf_module_default_settings',array($this,'default_settings'),10,2);
		//hook to generate template html
		add_filter('wf_module_generate_template_html_for_'.$this->module_base,array($this,'generate_template_html'),10,6);
		add_action('wt_print_doc',array($this,'print_it'),10,2);

		//hide empty fields on template
		add_filter('wf_pklist_alter_hide_empty',array($this,'hide_empty_elements'),10,6);
		
		//filter to alter settings
		add_filter('wf_pklist_alter_settings',array($this,'alter_settings'),10,2);		
		add_filter('wf_pklist_alter_option',array($this,'alter_option'),10,4);

		//initializing customizer		
		$this->customizer=Wf_Woocommerce_Packing_List::load_modules('customizer');

		add_filter('wt_print_actions',array($this,'add_print_buttons'),10,4);
		add_filter('wt_print_bulk_actions',array($this,'add_bulk_print_buttons'));

		add_filter('wf_pklist_alter_find_replace',array($this,'alter_find_replace'),10,5);

		add_filter('wt_pklist_alter_tooltip_data',array($this,'register_tooltips'),1);
		add_filter('wt_pklist_individual_print_button_for_document_types',array($this,'add_individual_print_button_in_admin_order_listing_page'),10,1);
		add_filter('woocommerce_admin_order_actions_end',array($this,'document_print_btn_on_wc_order_listing_action_column'),10,1);
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
	* 	Add admin menu
	*	@since 	4.0.0
	*/
	public function add_admin_pages($menus)
	{
		$menus[]=array(
			'submenu',
			WF_PKLIST_POST_TYPE,
			__('Dispatch label','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Dispatch label','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			$this->module_id,
			array($this,'admin_settings_page')
		);
		return $menus;
	}

	public function alter_find_replace($find_replace,$template_type,$order,$box_packing,$order_package)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			$is_footer=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_packinglist_footer_dl',$this->module_id);
			if("Yes" !== $is_footer)
			{
				$find_replace['wfte_footer']='wfte_hidden';
			}
		}
		return $find_replace;
	}
	public function alter_option($vl,$settings,$option_name,$base_id)
	{
        $is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($base_id === $this->module_id && !$is_pro_customizer)
		{
			if('wf_'.$this->module_base.'_contactno_email' === $option_name && is_array($vl))
			{
				if("Yes" === $settings['woocommerce_wf_add_customer_note_in_'.$this->module_base])
				{
					if(false === array_search('cus_note',$vl))
					{
						$vl[]='cus_note';
					}
				}else
				{
					if(false !== ($key=array_search('cus_note',$vl)))
					{
						unset($vl[$key]);
					}
				}
			}
		}
		return $vl;
	}
	public function alter_settings($settings,$base_id)
	{
        $is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($base_id === $this->module_id && !$is_pro_customizer)
		{
			$vl=$settings['wf_'.$this->module_base.'_contactno_email'];
			$vl = is_array($vl) ? $vl : array();
			if("Yes" === $settings['woocommerce_wf_add_customer_note_in_'.$this->module_base])
			{
				if(false === array_search('cus_note',$vl))
				{
					$vl[]='cus_note';
				}
			}else
			{
				if(false !== ($key=array_search('cus_note',$vl)))
				{
					unset($vl[$key]);
				}
			}
			$settings['wf_'.$this->module_base.'_contactno_email']=$vl;
		}
		return $settings;
	}

	public function hide_empty_elements($hide_on_empty_fields,$template_type)
	{
		if($template_type === $this->module_base)
		{
			$hide_on_empty_fields[]='wfte_dispatch_date';
			$hide_on_empty_fields[]='wfte_return_address';
		}
		return $hide_on_empty_fields;
	}

	public function admin_settings_page()
	{
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles',WC()->plugin_url().'/assets/css/admin.css');
		wp_enqueue_media();
		do_action('wt_pklist_customizer_enable',$this->module_id,$this->module_base);
		$template_type = $this->module_base;
		include_once WF_PKLIST_PLUGIN_PATH.'/admin/views/premium_extension_listing.php';
		include(plugin_dir_path( __FILE__ ).'views/admin-settings.php');
	}


	/**
	 *  Items needed to be converted to HTML for print/download
	 */
	public function generate_template_html($find_replace,$html,$template_type,$order,$box_packing=null,$order_package=null)
	{
		$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
		if($template_type === $this->module_base && !$is_pro_customizer)
		{
			//Generate invoice number while printing Dispatch label
			if(Wf_Woocommerce_Packing_List_Public::module_exists('invoice'))
			{
				Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order);	
			}			

			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_billing_address($find_replace,$template_type,$order);		
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_shipping_address($find_replace,$template_type,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_default_order_fields($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_product_table($find_replace,$template_type,$html,$order);
			$find_replace=Wf_Woocommerce_Packing_List_CustomizerLib::set_extra_charge_fields($find_replace,$template_type,$html,$order);		
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
				'woocommerce_wf_add_customer_note_in_dispatchlabel'=>'No',
				'woocommerce_wf_packinglist_footer_dl'=>'No',
				'woocommerce_wf_packinglist_variation_data'=>'Yes', //Add product variation data
				'wf_'.$this->module_base.'_contactno_email'=>array('contact_number','email'),
			);
		}else
		{
			return $settings;
		}
	}
	public function add_bulk_print_buttons($actions)
	{
		$actions['print_dispatchlabel']=__('Print Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce');
		return $actions;
	}
	
	public function add_print_buttons($item_arr, $order, $order_id, $button_location)
	{
		$is_show_prompt=1;
		if(Wf_Woocommerce_Packing_List_Public::module_exists('invoice'))
		{
			$invoice_number=Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,false);
			if(!empty($invoice_number))
	        {
	        	$is_show_prompt=0;
			}
		}else
		{
			$invoice_number='';
			$is_show_prompt=0;
		}

		if("detail_page" === $button_location)
		{
			$item_arr['dispatchlabel_details_actions']=array(
				'button_type'=>'aggregate',
				'button_key'=>'dispatchlabel_actions', //unique if multiple on same page
				'button_location'=>$button_location,
				'action'=>'',
				'label'=>__('Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'tooltip'=>__('Print/Download Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>0, //always 0
				'items'=>array(
					'print_dispatchlabel' => array(  
						'action'=>'print_dispatchlabel',
						'label'=>__('Print','print-invoices-packing-slip-labels-for-woocommerce'),
						'tooltip'=>__('Print Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce'),
						'is_show_prompt'=>$is_show_prompt,
						'button_location'=>$button_location,						
					),
				),
				'exist' => Wf_Woocommerce_Packing_List_Admin::check_doc_already_created($order,$order_id,'dispatchlabel'),
			);
		}else
		{
			$item_arr[]=array(
				'action'=>'print_dispatchlabel',
				'label'=>__('Print Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'tooltip'=>__('Print Dispatch Label','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_show_prompt'=>$is_show_prompt,
				'button_location'=>$button_location,
			);
		}
		return $item_arr;
	}
	
	/* 
	* Print_window for invoice
	* @param $orders : order ids
	*/    
    public function print_it($order_ids,$action) 
    {
    	$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$this->module_base,false,$this->module_base);
        if(!$is_pro_customizer)
        {
        	if("print_dispatchlabel" === $action)
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
		        	$this->customizer->template_for_pdf=false;	        	
		        	$html=$this->generate_order_template($order_ids,$pdf_name);
					echo $html;
		        }
		        exit();
	    	}
        }
    }
    public function generate_order_template($orders,$page_title)
    {
    	$template_type=$this->module_base;
    	//taking active template html
    	$html=$this->customizer->get_template_html($template_type);
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
				$order=( WC()->version < '2.7.0' ) ? new WC_Order($order_id) : new wf_order($order_id);

				/**
				 * @since 4.6.0 - Added filter to add before preparing the order package and rendering the html.
				 */
				$pdf_filters = apply_filters( 'wt_pklist_add_filters_before_rendering_pdf', array(), $this->module_base, $order );
				Wt_Pklist_Common::wt_pklist_pdf_add_filters( $pdf_filters );
				
				$out.=$this->customizer->generate_template_html($html,$template_type,$order);
				$document_created = Wf_Woocommerce_Packing_List_Admin::created_document_count($order_id,$template_type);
				if($number_of_orders>1 && $order_inc<$number_of_orders)
				{
                	$out.='<p class="pagebreak"></p>';
	            }else
	            {
	                //$out.='<p class="no-page-break"></p>';
	            }

				/**
				 * @since 4.6.0 - Remove the filters which were added before preparing the order package and rendering the html.
				 */
				Wt_Pklist_Common::wt_pklist_pdf_remove_filters( $pdf_filters );
			}
			$out=$this->customizer->append_style_blocks($out,$style_blocks);
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
			$documents[$this->module_base] = __("Dispatch label","print-invoices-packing-slip-labels-for-woocommerce");
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
				$btn_action_name	= 'wt_pklist_print_document_'.$this->module_base.'_not_yet';
				$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'.png';
				
				if( Wf_Woocommerce_Packing_List_Public::module_exists('invoice') )
				{
					$invoice_number		= Wt_Pklist_Common::get_order_meta( $order_id, 'wf_invoice_number', true );
					if( !empty( $invoice_number ) ) {
						$btn_action_name	= 'wt_pklist_print_document_'.$this->module_base;
						$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'_logo.png';
					}
				} else {
					$order_docs			= Wt_Pklist_Common::get_order_meta( $order_id, '_created_document', true );
					$order_docs_old		= Wt_Pklist_Common::get_order_meta( $order_id, '_created_document_old', true );
					
					if( ( !empty( $order_docs ) && in_array( $this->module_base, $order_docs ) ) || ( !empty( $order_docs_old ) && in_array( $this->module_base, $order_docs_old ) ) ) {
						$btn_action_name	= 'wt_pklist_print_document_'.$this->module_base;
						$img_url 			= WF_PKLIST_PLUGIN_URL . 'admin/images/'.$this->module_base.'_logo.png';
					}
				}
				
				$action			= 'print_'.$this->module_base;
				$action_title 	= sprintf( '%1$s %2$s',
					__("Print","print-invoices-packing-slip-labels-for-woocommerce"),
					$this->module_title,
					);
				$print_url		= Wf_Woocommerce_Packing_List_Admin::get_print_url($order_id,$action);
				echo '<a title="'.esc_attr($action_title).'" class="button wc-action-button wc-action-button-'.esc_attr($btn_action_name).' '.esc_attr($btn_action_name).' wt_pklist_action_btn wt_pklist_admin_print_document_btn" href="'.esc_url_raw($print_url).'" aria-label="'.esc_attr($action_title).'" target="_blank" style="padding:5px;"><img src="'.esc_url($img_url).'" ></a>';
			}
		}
	}
}
new Wf_Woocommerce_Packing_List_Dispatchlabel();