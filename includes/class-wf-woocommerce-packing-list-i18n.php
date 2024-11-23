<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.5.0
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/includes
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.5.0
	 * @since    4.4.1 - Improved the loading text domain for translation
	 */
	public function load_plugin_textdomain() {
		$text_domain	= 'print-invoices-packing-slip-labels-for-woocommerce';
		if ( function_exists( 'determine_locale' ) ) { // WP5.0+
			$locale = determine_locale();
		} else {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		}
		
		$locale = apply_filters( 'plugin_locale', $locale, $text_domain );
		unload_textdomain( $text_domain );
		$dir    = trailingslashit( WP_LANG_DIR );

		/**
		 * Frontend/global Locale. Looks in:
		 *
		 * if text domain is print-invoices-packing-slip-labels-for-woocommerce then
		 * 
		 * 		- WP_LANG_DIR/print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce-LOCALE.mo
		 * 	 	- WP_LANG_DIR/plugins/print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce-LOCALE.mo
		 * 		- WP_LANG_DIR/plugins/print-invoices-packing-slip-labels-for-woocommerce-LOCALE.mo
		 * 		- WP_LANG_DIR/print-invoices-packing-slip-labels-for-woocommerce-LOCALE.mo
		 *
		 * WP_LANG_DIR defaults to wp-content/languages
		 */
		
		// load mo file from language folder of the plugin
		$custom_mo_file = WF_PKLIST_PLUGIN_PATH . '/languages/' . $text_domain .'-'. $locale .'.mo';
		$custom_mo_file = apply_filters( 'wt_pklist_alter_language_file_location', $custom_mo_file, $locale, $text_domain );
		load_textdomain( $text_domain, $custom_mo_file ); // Custom mo file location
		
		// load mo files from the Wordpress language file locations
		load_textdomain( $text_domain, $dir . $text_domain . '/' . $text_domain .'-'. $locale .'.mo' ); 
		load_textdomain( $text_domain, $dir . 'plugins/' . $text_domain . '/' . $text_domain .'-'. $locale .'.mo' );
		load_textdomain( $text_domain, $dir . 'plugins/' . $text_domain .'-'. $locale .'.mo' );
		load_textdomain( $text_domain, $dir . $text_domain .'-'. $locale .'.mo' );
		
		// load mo files from default location
		load_plugin_textdomain($text_domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/');
	}
}
