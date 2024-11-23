<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(!class_exists('Wf_Woocommerce_Packing_List_Basic_Common_Func'))
{

class Wf_Woocommerce_Packing_List_Basic_Common_Func {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action( 'admin_enqueue_scripts',array($this,'enqueue_scripts') );
    }

    public function enqueue_scripts() 
    {
        wp_enqueue_script( $this->plugin_name.'-basic-common', plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin-basic-common.js', array( 'jquery','wp-color-picker','jquery-tiptip'), $this->version, false );

        $order_meta_autocomplete = Wf_Woocommerce_Packing_List_Admin::order_meta_dropdown_list();
        $wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
        $is_rtl = is_rtl() ? 'rtl' : 'ltr';
        $params=array(
            'nonces' => array(
                    'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
             ),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'no_image'=>$wf_admin_img_path,
            'print_action_url'=>admin_url('?print_packinglist=true'),
            'order_meta_autocomplete' => json_encode($order_meta_autocomplete),
            'is_rtl' => $is_rtl,
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
                'invoice_number_prompt_no_from_addr' => __("Please fill the `from address` in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'),
                'fitler_code_copied' => __("Code Copied","print-invoices-packing-slip-labels-for-woocommerce"),
                'close'=>__("Close",'print-invoices-packing-slip-labels-for-woocommerce'),
                'save'=>__("Save",'print-invoices-packing-slip-labels-for-woocommerce'),
                'enter_mandatory_fields'=>__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce'),
                'buy_pro_prompt_order_meta' => __('You can add more than 1 order meta in','print-invoices-packing-slip-labels-for-woocommerce'),
                'buy_pro_prompt_edit_order_meta' => __('Edit','print-invoices-packing-slip-labels-for-woocommerce'),
                'buy_pro_prompt_edit_order_meta_desc' => __('You can edit an existing item by using its key.','print-invoices-packing-slip-labels-for-woocommerce'),
            )
        );
        wp_localize_script($this->plugin_name.'-basic-common', 'wf_pklist_params_basic', $params);
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
                            $module_base=(isset($_POST['wt_pklist_settings_base']) ? sanitize_text_field($_POST['wt_pklist_settings_base']) : 'main');
                            $module_id=("main" === $module_base ? '' : Wf_Woocommerce_Packing_List::get_module_id($module_base));
                            $add_only=(isset($_POST['add_only']) ? true : false);
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

                                $dc_slug=Wf_Woocommerce_Packing_List_Admin::sanitize_css_class_name($new_meta_key_display); /* This is for Dynamic customizer */

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

}
// end of class
}