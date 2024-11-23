<?php
namespace Wtpdf\Banners;
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Wt_Bfcm_Twenty_Twenty_Four
 *
 * This class is responsible for displaying and handling the Black Friday and Cyber Monday CTA banners for 2024.
 */
if ( !class_exists( '\\Wtpdf\\Banners\\Wt_Bfcm_Twenty_Twenty_Four' ) ) {

    class Wt_Bfcm_Twenty_Twenty_Four {
        
        private $banner_id = 'wt-bfcm-twenty-twenty-four';
        private static $banner_state_option_name = "wt_bfcm_twenty_twenty_four_banner_state"; // Banner state, 1: Show, 2: Closed by user, 3: Clicked the grab button, 4: Expired
        private $banner_state = 1;
        private static $show_banner = null;
        private static $ajax_action_name = "wt_bcfm_twenty_twenty_four_banner_state";
        private static $promotion_link = "https://www.webtoffee.com/plugins/?utm_source=BFCM_accounting&utm_medium=invoice&utm_campaign=BFCM-Accounting";
        private static $banner_version = '';
        
        public function __construct() {
            self::$banner_version = WF_PKLIST_VERSION; // Plugin version

            $this->banner_state = get_option( self::$banner_state_option_name ); // Current state of the banner
			$this->banner_state = absint( false === $this->banner_state ? 1 : $this->banner_state );

            // Enqueue styles
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );

            // Add banner
            add_action( 'admin_notices', array( $this, 'show_banner' ), 999 );

            // Ajax hook to save banner state
			add_action( 'wp_ajax_' . self::$ajax_action_name, array( $this, 'update_banner_state' ) ); 
        }

        /**
         * To add the banner styles
         *
         * @return void
         */
        public function enqueue_styles_and_scripts() {
            wp_enqueue_style( $this->banner_id.'-css', plugin_dir_url( __FILE__ ) . 'assets/css/wt-bfcm-twenty-twenty-four.css', array(), self::$banner_version, 'all' );
            $params = array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wt_bfcm_twenty_twenty_four_banner_nonce' ),
                'action' => self::$ajax_action_name,
                'cta_link' => self::$promotion_link,
            );
            wp_enqueue_script( $this->banner_id.'-js', plugin_dir_url( __FILE__ ) . 'assets/js/wt-bfcm-twenty-twenty-four.js', array( 'jquery' ), self::$banner_version, false );
            wp_localize_script( $this->banner_id.'-js', 'wt_bfcm_twenty_twenty_four_banner_js_params', $params );
        }

        public function show_banner () {
            if ( $this->is_show_banner() ) {
                ?>
                    <div class="wt-bfcm-banner-2024 notice is-dismissible">
                        <div class="wt-bfcm-banner-body">
                            <div class="wt-bfcm-banner-body-img-section">
                                <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/black-friday-2024.svg'; ?>" alt="Black Friday Cyber Monday 2024">
                            </div>
                            <div class="wt-bfcm-banner-body-info">
                                <div class="never-miss-this-deal">
                                    <p><?php echo esc_html__( 'Never Miss This Deal', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?></p>
                                </div>
                                <div class="info">
                                    <p><?php 
                                        echo sprintf(
                                                __( 'Your Last Chance to Avail %1$s on WebToffee Plugins. Grab the deal before it`s gone!', 'print-invoices-packing-slip-labels-for-woocommerce' ), 
                                                '<span>30% '.__("OFF","print-invoices-packing-slip-labels-for-woocommerce").'</span>'
                                            );
                                    ?></p>
                                </div>
                                <div class="info-button">
                                    <a href="<?php echo self::$promotion_link; ?>" class="bfcm_cta_button" target="_blank"><?php echo esc_html__( 'View plugins', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?> <span class="dashicons dashicons-arrow-right-alt"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
            }
        }

        public function is_show_banner () {
            
            $start_date = new \DateTime( '25-NOV-2024, 12:00 AM', new \DateTimeZone( 'Asia/Kolkata' ) ); // Start date.
            $current_date = new \DateTime( 'now', new \DateTimeZone( 'Asia/Kolkata' ) ); // Current date.
            $end_date = new \DateTime( '02-DEC-2024, 11:59 PM', new \DateTimeZone( 'Asia/Kolkata' ) ); // End date.

            /**
             * check if the current date is less than the start date then wait for the start date.
             */
            if ( $current_date < $start_date ) {
                self::$show_banner = false;
                return self::$show_banner;
            }

            /**
    		 * 	check if the current date is greater than the end date, then set the banner state as expired.
    		 */
            if ( $current_date >= $end_date ) {
                update_option( self::$banner_state_option_name, 4 ); // Set as expired.
    			self::$show_banner = false;
    			return self::$show_banner;
            }

            /**
             *  Already checked.
             */
            if ( ! is_null( self::$show_banner ) ) {
    			return self::$show_banner;
    		}

            /**
    		 * 	Check current banner state
    		 */
    		if ( 1 !== $this->banner_state ) {
    			self::$show_banner = false;
    			return self::$show_banner;
    		}

            /**
    		 * 	Check screens
    		 */
    		$screen    = get_current_screen();
            $screen_id = $screen ? $screen->id : '';
            
            /**
             *  Pages to show this black friday and cyber monday banner for 2024.
             * 	
             * 	@param 	string[] 	Default screen ids
             */
            $screens_to_show = (array) apply_filters( 'wt_bfcm_banner_screens', array() );
            self::$show_banner = in_array( $screen_id, $screens_to_show );
            return self::$show_banner;
        }

        /**
    	 * 	Update banner state ajax hook
    	 * 
    	 */
    	public function update_banner_state() {
    		check_ajax_referer( 'wt_bfcm_twenty_twenty_four_banner_nonce' );
    		if ( isset( $_POST['wt_bfcm_twenty_twenty_four_banner_action_type'] ) ) {
	            
	            $action_type = absint( sanitize_text_field( $_POST['wt_bfcm_twenty_twenty_four_banner_action_type'] ) );
	            // Current action is allowed?
	            if ( in_array( $action_type, array( 2, 3 ) ) ) {
	                update_option( self::$banner_state_option_name, $action_type );
	            }
	        }
	        exit();
    	}

    }

new \Wtpdf\Banners\Wt_Bfcm_Twenty_Twenty_Four();
}