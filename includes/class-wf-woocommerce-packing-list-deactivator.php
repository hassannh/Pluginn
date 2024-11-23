<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.5.0
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.5.0
	 */
	public static function deactivate()
	{
		do_action("wt_pklist_deactivate");
        
		// delete the schedule of deleting the pdf/html files stored
		as_unschedule_all_actions('wt_pklist_temp_file_clear', array(), "wt_pklist_clear_temp_files_group");
		
        // delete the schedule of getting the empty invoice count
        as_unschedule_all_actions('update_empty_invoice_number_count', array(), "wt_pklist_get_invoice_number_count_auto_generation");
        
        // delete the schedule of generating invoice number for high number of orders
        as_unschedule_all_actions('wt_pklist_schedule_auto_generate_invoice_number', array(), "wt_pklist_invoice_number_auto_generation");

		if(isset($_GET['delete_all_settings'])){
			if(1 === $_GET['delete_all_settings'] || "1" === $_GET['delete_all_settings']){
				self::delete_all_plugin_settings();
			}
		}

		// delete the schedule of saving the default templates
        as_unschedule_all_actions('wt_pklist_save_default_templates', array(), "wt_pklist_save_default_templates_group");
	}

	/**
	 * Delete all the settings of the plugin when deactivating the plugin, if the checkbox `delete_all_settings` is checked
	 *
	 * @return void
	 */
	public static function delete_all_plugin_settings(){
		$options = Wf_Woocommerce_Packing_List_Admin::get_all_option_of_this_plugin();
		if(is_array($options)){
			$deactivate_options = array(
				// preview template option for all the document types
				'wf_pklist_options_migrated',
				'wf_woocommerce_packing_list_invoice_preview_pdf_html',
				'wf_woocommerce_packing_list_packinglist_preview_pdf_html',
				'wf_woocommerce_packing_list_deliverynote_preview_pdf_html',
				'wf_woocommerce_packing_list_shippinglabel_preview_pdf_html',
				'wf_woocommerce_packing_list_dispatchlabel_preview_pdf_html',
				'wf_woocommerce_packing_list_proformainvoice_preview_pdf_html',
				'wf_woocommerce_packing_list_picklist_preview_pdf_html',
				'wt_pklist_import_date',
				'wt_pklist_reset_date',
			);
			$options  = array_merge($options, $deactivate_options);

			// delete all the options from options table
			foreach($options as $option){
				delete_option($option);
			}
		}
		
		// delete the template table
        global $wpdb;
        $table_name=$wpdb->prefix.Wf_Woocommerce_Packing_List::$template_data_tb;
        $wpdb->query("DROP TABLE $table_name");
    }

}
