<?php
/**
 * Request a Feature Module
 */
namespace WtPdf\Admin\Modules;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly  
}

if ( !class_exists( '\\WtPdf\\Admin\\Modules' ) ) {

    class Wt_Pdf_Request_Feature {

        public $module_base = 'request_feature';
        public $module_id = '';
        public static $module_id_static = '';
        public $module_version = '';

        private static $instance = null;
        private $end_point = 'https://feedback.webtoffee.com/wp-json/feature-suggestion/v1';

        public function __construct() {

            if ( $this->load_this_module()) {
                $this->module_id = \Wf_Woocommerce_Packing_List::get_module_id( $this->module_base );
                self::$module_id_static = $this->module_id;
                $this->module_version = WF_PKLIST_VERSION;

                // Enqueue styles
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );


                add_action( 'wt_pdf_plugin_settings_after_wrap', array( $this, 'add_request_button' ) );
            }

            /**
             *  Ajax callback to send the suggestion
             * 
             *  @since 4.7.0
             */
            add_action( 'wp_ajax_wt_pdf_request_feature_action_func', array( $this, 'send_suggestion' ) );

        }

        /**
         *  Get Instance
         * 
         *  @since 2.1.0
         */
        public static function get_instance()
        {
            if(is_null(self::$instance))
            {
                self::$instance = new \WtPdf\Admin\Modules\Wt_Pdf_Request_Feature();
            }
            return self::$instance;
        }

        public function load_this_module() {
            if ( isset( $_GET['page'] ) && 'wf_woocommerce_packing_list' === $_GET['page'] ) {
                return true;
            }
            return false;
        }

        public function enqueue_styles_and_scripts() {
            wp_enqueue_style( $this->module_id.'-css', plugin_dir_url( __FILE__ ) . 'assets/css/wt-pdf-request-feature.css', array(), $this->module_version , 'all' );
            $params = array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wt_pdf_request_feature_nonce' ),
                'enter_message' => esc_html__( 'Please enter a message', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'email_message' => esc_html__( 'We need your email address to contact you back.', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'sending' => esc_html__( 'Sending...', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'unable_to_submit' => esc_html__( 'Unable to submit. Please try again later.', 'print-invoices-packing-slip-labels-for-woocommerce' ),
                'success_msg' => esc_html__( 'Thank you for your valuable suggestion.', 'print-invoices-packing-slip-labels-for-woocommerce' ),
            );
            wp_enqueue_script( $this->module_id.'-js', plugin_dir_url( __FILE__ ) . 'assets/js/wt-pdf-request-feature.js', array( 'jquery' ), $this->module_version, false );
            wp_localize_script( $this->module_id.'-js', 'wt_pdf_request_feature_js_params', $params );
        }

        public function add_request_button() {
            ?>
            <div class="wt_pdf_request_feature_section">
                <p><i><?php echo esc_html__("Looking for a feature we don't have?","print-invoices-packing-slip-labels-for-woocommerce"); ?> <a class="wt-pdf-request-feature-button" data-wt-pdf-popup="wt_pdf_request_feature_popup"><?php echo esc_html__( "Request it here" ,"print-invoices-packing-slip-labels-for-woocommerce" ); ?></a></i></p>
                <div class="wt_pdf_request_feature_popup wf_pklist_popup" style="width:95%; max-width:722px;">
                    <div class="wf_pklist_popup_hd">
                        <div class="wf_pklist_popup_title">
                            <p class="main_title"><?php echo esc_html__( "Missing a feature?", "print-invoices-packing-slip-labels-for-woocommerce" ); ?></p>
                            <p class="sub_title"><?php echo esc_html__( "Drop a message to let us know!", "print-invoices-packing-slip-labels-for-woocommerce" ); ?></p>
                        </div>
                        <div class="wf_pklist_popup_close">X</div>
                    </div>
                    <div class="wf_pklist_popup_content">
                        <form class="wt_pdf_request_feature_form">
                            <?php wp_nonce_field(WF_PKLIST_PLUGIN_NAME); ?>
                            <input type="hidden" name="action" value="wt_pdf_request_feature_action_func">
                            <label class="form_label"><?php echo esc_html__( "What would you like to add as a new feature?", "print-invoices-packing-slip-labels-for-woocommerce" ); ?></label>
                            <span class="form_label_caption"><?php echo esc_html__( "More the details you share, the better.", "print-invoices-packing-slip-labels-for-woocommerce" ); ?></span>
                            <textarea name="wt_pdf_request_feature_msg" placeholder="<?php esc_attr_e( 'I would like...', 'print-invoices-packing-slip-labels-for-woocommerce' );?>"></textarea>
                            <div class="wt_pdf_request_feature_checkbox_container">
                                <input type="checkbox" name="wt_pdf_request_feature_customer_email_enable" id="wt_pdf_request_feature_customer_email_enable" value="yes"> <label for="wt_pdf_request_feature_customer_email_enable"><?php echo esc_html__( "Webtoffee can contact me about this feedback", "print-invoices-packing-slip-labels-for-woocommerce" ); ?></label>
                            </div>
                            <!-- Email field -->
                            <div class="wt_pdf_request_feature_email_container">
                                <label class="form_label"><?php esc_html_e( 'Enter your email address.', 'print-invoices-packing-slip-labels-for-woocommerce' ) ?></label>
                                <input type="email" name="wt_pdf_request_feature_email" class="wt_pdf_request_feature_input" placeholder="<?php esc_attr_e( 'Enter email address', 'print-invoices-packing-slip-labels-for-woocommerce' );?>">
                            </div>

                            <!-- Buttons -->
                            <div class="wt_pdf_request_feature_btn_box">
                                <button type="submit" name="wt_pdf_request_feature_sbmt_btn" class="button-primary"><?php esc_html_e('Send feature request', 'print-invoices-packing-slip-labels-for-woocommerce') ?></button>
                                <button type="button" name="wt_pdf_request_feature_cancel_btn" class="button-secondary"><?php esc_html_e('Cancel', 'print-invoices-packing-slip-labels-for-woocommerce') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }


        /**
         *  Send ajax form data to WebToffee server
         *  Hooked into: wp_ajax_wt_pdf_request_feature_action_func
         * 
         *  @since 4.7.0
         */
        public function send_suggestion() {
            $out = array(
                'status' => true,
                'msg' => __('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
            );
            $nonce = ( isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '' );

            if( "" !== $nonce && wp_verify_nonce( $nonce, WF_PKLIST_PLUGIN_NAME ) ) 
            {
                $er_msg = '';
                $msg = isset( $_POST['wt_pdf_request_feature_msg'] ) ? sanitize_textarea_field( $_POST['wt_pdf_request_feature_msg'] ) : '';
                $take_email = isset( $_POST['wt_pdf_request_feature_customer_email_enable'] ) ? sanitize_text_field( $_POST['wt_pdf_request_feature_customer_email_enable'] ) : 'no';
                $email = isset( $_POST['wt_pdf_request_feature_email'] ) ? sanitize_email( $_POST['wt_pdf_request_feature_email'] ) : '';
                

                if( '' === $msg ) {
                    $er_msg = esc_html__('Please enter your message.', 'print-invoices-packing-slip-labels-for-woocommerce');
                }

                if( '' === $er_msg && 'yes' === $take_email && '' === $email ) {
                    $er_msg = esc_html__('We need your email address to contact you back.', 'print-invoices-packing-slip-labels-for-woocommerce' );
                }

                //no error
                if( '' === $er_msg ) {

                    $data = array(
                        'msg' => $msg, //message from user
                        'user_email' => $email, //user email, if given
                        'plugin_version' => WF_PKLIST_VERSION,
                        'plugin' => "invoice",
                    );
                    
                    // Write an action/hook here in webtoffe to recieve the data
                    $resp = wp_remote_retrieve_body( wp_remote_post( $this->end_point, array(
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => false,
                            'body' => $data,
                            'cookies' => array(),
                        )
                    ));

                    if( !is_wp_error( $resp ) ) {
                        $out['status'] = true;
                        $out['msg'] = __( 'Success', 'print-invoices-packing-slip-labels-for-woocommerce' );
                    }

                } else {
                    $out['msg'] = $er_msg;
                }
            }

            echo json_encode($out);
            exit();
        }
    }
    new Wt_Pdf_Request_Feature();

}