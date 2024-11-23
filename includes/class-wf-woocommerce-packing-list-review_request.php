<?php
/**
 * Review request
 *  
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wf_Woocommerce_Packing_List_Review_Request
{
	/**
	* config options 
	*/
	private $plugin_title="WooCommerce PDF Invoices, Packing Slips, Delivery Notes & Shipping Labels";
	private $review_url="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/#new-post";
	private $plugin_prefix="wt_pklist"; /* must be unique name */
	private $activation_hook="wt_pklist_activate"; /* hook for activation, to store activated date */
	private $deactivation_hook="wt_pklist_deactivate"; /* hook for deactivation, to delete activated date */
	private $days_to_show_banner=10; /* when did the banner to show */
	private $remind_days=15; /* remind interval in days */
	private $webtoffee_logo_url=WF_PKLIST_PLUGIN_URL.'assets/images/webtoffee-logo_small.png';

	
	private $start_date=0; /* banner to show count start date. plugin installed date, remind me later added date */
	private $current_banner_state=2; /* 1: active, 2: waiting to show(first after installation), 3: closed by user/not interested to review, 4: user done the review, 5:remind me later */
	private $banner_state_option_name=''; /* WP option name to save banner state */
	private $start_date_option_name=''; /* WP option name to save start date */
	private $banner_css_class=''; /* CSS class name for Banner HTML element. */
	private $banner_message=''; /* Banner message. */
	private $later_btn_text=''; /* Remind me later button text */
	private $never_btn_text=''; /* Never review button text. */
	private $review_btn_text=''; /* Review now button text. */
	private $ajax_action_name=''; /* Name of ajax action to save banner state. */
	private $allowed_action_type_arr=array(
		'later', /* remind me later */
		'never', /* never */
		'review', /* review now */
		'closed', /* not interested */
	);
	private $doc_created_count = 0;

	public function __construct()
	{
		//Set config vars
		$this->set_vars();

		add_action($this->activation_hook, array($this, 'on_activate'));
		add_action($this->deactivation_hook, array($this, 'on_deactivate'));

		if($this->check_condition()) /* checks the banner is active now */
		{
			$this->banner_message=sprintf(__('Hey, we at %1$s WebToffee %2$s would like to thank you for using %3$s %4$s %5$s. %6$s Less than a minute of your time will motivate us to keep doing what we do. We would really appreciate if you could take a moment to drop a quick review that motivate us to keep going.', 'print-invoices-packing-slip-labels-for-woocommerce'), '<b>', '</b>','<b>', $this->plugin_title, '</b>', '<br />');

			/* button texts */
			$this->later_btn_text=__("Remind me later", 'print-invoices-packing-slip-labels-for-woocommerce');
			$this->never_btn_text=__("Not interested", 'print-invoices-packing-slip-labels-for-woocommerce');
			$this->review_btn_text=__("Review now", 'print-invoices-packing-slip-labels-for-woocommerce');

			if(1000 <= $this->doc_created_count){
				$this->webtoffee_logo_url = "";
				$round_count = floor($this->doc_created_count/100) * 100;
				$this->banner_message=sprintf('<b>%1$s</b> %2$s <b style="color:#FF6636;font-size: 14px;">%3$s +</b> %4$s <b style="color:#FF6636;font-size: 14px;">%5$s</b>. %6$s',
					__("Knock, Knock!",'print-invoices-packing-slip-labels-for-woocommerce'),
					__("You`ve just created","print-invoices-packing-slip-labels-for-woocommerce"),
					$round_count,
					__("commercial documents using","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Woocommerce PDF invoices, packing slips, delivery notes, and shipping labels","print-invoices-packing-slip-labels-for-woocommerce"),
					__("That’s awesome! We’d love it if you take a moment to share what you think and help spread the word.","print-invoices-packing-slip-labels-for-woocommerce")
					);
				$this->review_btn_text=__("Rate us now", 'print-invoices-packing-slip-labels-for-woocommerce');
			}

			add_action('admin_notices', array($this, 'show_banner')); /* show banner */
			add_action('admin_print_footer_scripts', array($this, 'add_banner_scripts')); /* add banner scripts */
			add_action('wp_ajax_'.$this->ajax_action_name, array($this, 'process_user_action')); /* process banner user action */
		}

		
		/**
		 * To show banner after bulk print in Print Invoices & Packing List for WooCommerce plugin
		 * @since 4.6.1
		 */
		if ( $this->check_condition_for_banner_after_bulk_print_ipc() ) {
			add_action('admin_notices', array($this, 'show_banner_after_bulk_print_ipc')); /* show banner */
			add_action('admin_print_footer_scripts', array($this, 'add_scripts_for_banner_after_bulk_print_ipc')); /* add banner scripts */
			add_action('wp_ajax_close_banner_after_bulk_print_ipc', array($this, 'process_user_action_for_banner_after_bulk_print_ipc')); /* process banner user action */
		}
	}

	/**
	*	Set config vars
	*/
	public function set_vars()
	{
		$this->ajax_action_name=$this->plugin_prefix.'_process_user_review_action';
		$this->banner_state_option_name=$this->plugin_prefix."_review_request"; 
		$this->start_date_option_name=$this->plugin_prefix."_start_date";
		$this->banner_css_class=$this->plugin_prefix."_review_request";

		$this->start_date=absint(get_option($this->start_date_option_name));
		$banner_state=absint(get_option($this->banner_state_option_name));
		$this->current_banner_state=($banner_state==0 ? $this->current_banner_state : $banner_state);	
		$this->doc_created_count = (int)get_option('wt_created_document_count',true);	
	}

	/**
	*	Actions on plugin activation
	*	Saves activation date
	*/
	public function on_activate()
	{
		if($this->start_date==0)
		{
			$this->reset_start_date();
		}
	}

	/**
	*	Actions on plugin deactivation
	*	Removes activation date
	*/
	public function on_deactivate()
	{
		delete_option($this->start_date_option_name);
	}

	/**
	*	Reset the start date. 
	*/
	private function reset_start_date()
	{
		update_option($this->start_date_option_name, time());
	}

	/**
	*	Update the banner state 
	*/
	private function update_banner_state($val)
	{
		update_option($this->banner_state_option_name, $val);
	}
	
	/**
	*	Prints the banner 
	*/
	public function show_banner()
	{
		$this->update_banner_state(1); /* update banner active state */
		if(1000 <= $this->doc_created_count){
			?>
			<div class="<?php echo esc_attr($this->banner_css_class); ?> value_based_notice notice is-dismissible">
				<p style="width: 85%;">
		            <?php echo wp_kses_post($this->banner_message); ?>
		        </p>
		        <div style="margin-top: 1em;">
		        	<p>
		                <a class="button" data-type="review" style="background: #FFE500;border-color: #ccc;;color: #000;"><?php echo esc_html($this->review_btn_text); ?></a>
		                <a class="button button-secondary" style="color:#333; border-color:#ccc; background:#efefef;" data-type="later"><?php echo esc_html($this->later_btn_text); ?></a>
		            </p>
		        </div>
				<div style="position: absolute;top: 1.8em;<?php if(is_rtl()){echo "left: 0;";}else{echo "right: 0;"; } ?>">
		        	<img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL.'admin/images/value_based_review_img.png')?>" style="height: 85px;<?php if(is_rtl()){echo "transform: scaleX(-1);";} ?>">
		        </div>
			</div>
			<?php
		}else{
			?>
			<div class="<?php echo esc_attr($this->banner_css_class); ?> notice-info notice is-dismissible">
	            <?php
	            if ("" !== $this->webtoffee_logo_url) {
	            ?>
	                <h3 style="margin: 10px 0;"><?php echo wp_kses_post($this->plugin_title); ?></h3>
	            <?php
	            }
	            ?>
	            <p>
	                <?php echo wp_kses_post($this->banner_message); ?>
	            </p>
	            <p>
	                <a class="button button-secondary" style="color:#333; border-color:#ccc; background:#efefef;" data-type="later"><?php echo wp_kses_post($this->later_btn_text); ?></a>
	                <a class="button button-primary" data-type="review"><?php echo wp_kses_post($this->review_btn_text); ?></a>
	            </p>
	            <div class="wt-cli-review-footer" style="position: relative;">
	                <span class="wt-cli-footer-icon" style="position: absolute;bottom: 10px;<?php echo is_rtl() ? 'left: 0;' :'right:0;'; ?>"><img src="<?php echo esc_url($this->webtoffee_logo_url); ?>" style="max-width:100px;"></span>
	            </div>
	        </div>
			<?php
		}
	}

	/**
	*	Ajax hook to process user action on the banner
	*/
	public function process_user_action()
	{
		check_ajax_referer($this->plugin_prefix);
		if(isset($_POST['wt_review_action_type']))
		{
			$action_type=sanitize_text_field($_POST['wt_review_action_type']);
			
			/* current action is in allowed action list */
			if(in_array($action_type, $this->allowed_action_type_arr))
			{
				if("never" === $action_type || "closed" === $action_type)
				{
					$new_banner_state=3;
					$this->reset_start_date();
					if(1000 <= $this->doc_created_count){
						$new_banner_state=33; 
					}
				}
				elseif("review" === $action_type)
				{
					$new_banner_state=4;
				}else
				{
					/* reset start date to current date */
					$this->reset_start_date();
					$new_banner_state=5; /* remind me later */
				}
				$this->update_banner_state($new_banner_state);
			}
		}
		exit();
	}

	/**
	*	Add banner JS to admin footer
	*/
	public function add_banner_scripts()
	{
		$ajax_url=admin_url('admin-ajax.php');
		$nonce=wp_create_nonce($this->plugin_prefix);
		?>
		<script type="text/javascript">
		    (function($){
		        "use strict";

		        /* prepare data object */
	            var data_obj={
	            	_wpnonce: '<?php echo esc_html($nonce);?>',
            		action: '<?php echo esc_html($this->ajax_action_name);?>',
            		wt_review_action_type: ''
	            };

		        $(document).on('click', '.<?php echo esc_attr($this->banner_css_class);?> a.button', function(e)
		        {
		            e.preventDefault();
		            var elm=$(this);
		            var btn_type=elm.attr('data-type');
		            if(btn_type=='review')
		            {
		            	window.open('<?php echo esc_url($this->review_url);?>');
		            }
		            elm.parents('.<?php echo esc_attr($this->banner_css_class);?>').hide();

		            data_obj['wt_review_action_type']=btn_type;
		            $.ajax({
		            	url:'<?php echo esc_url($ajax_url);?>',
		            	data:data_obj,
		            	type: 'POST'
		            });

		        }).on('click', '.<?php echo esc_attr($this->banner_css_class);?> .notice-dismiss', function(e)
		        {
	                e.preventDefault();
		            data_obj['wt_review_action_type']='closed';
		            $.ajax({
		            	url:'<?php echo esc_url($ajax_url);?>',
		            	data:data_obj,
		            	type: 'POST',
		            });

		        });

		    })(jQuery)
		</script>
		<?php
	}

	/**
	*	Checks the condition to show the banner
	*/
	private function check_condition()
	{
		if(1 === $this->current_banner_state || "1" === $this->current_banner_state) /* currently showing then return true */
		{
			return true;
		}
		
		if(2 === $this->current_banner_state || "2" === $this->current_banner_state || 5 === $this->current_banner_state || "5" === $this->current_banner_state) /* only waiting/remind later state */
		{
			if(0 === $this->start_date || "0" === $this->start_date) /* unable to get activated date */
			{
				/* set current date as activation date*/
				$this->reset_start_date();
				return false;
			}

			$days=(2 === $this->current_banner_state || "2" === $this->current_banner_state ? $this->days_to_show_banner : $this->remind_days);
			$date_to_check=$this->start_date+(86400*$days);
			// $date_to_check = $this->start_date+(60); // for testing
			if($date_to_check<=time()) /* time reached to show the banner */
			{
				return true;
			}else
			{
				return false;
			}
		}

		if((3 === $this->current_banner_state || "3" === $this->current_banner_state) && 1000 <= $this->doc_created_count){
			$date_to_check=$this->start_date+(86400*15);
			// $date_to_check = $this->start_date+(60); // for testing
			if($date_to_check<=time()) /* time reached to show the banner */
			{
				return true;
			}
		}

		return false;
	}


	public function check_condition_for_banner_after_bulk_print_ipc() {
		$bulk_printed = get_option('wt_pklist_banner_after_bulk_print_ipc');
		/**
		 * 1 - banner shown
		 * 0 - banner not shown
		 */
		if ( 1 === absint( $bulk_printed ) && !is_plugin_active( 'wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php' ) ) {
			return true;
		}
		return false;
	}

	public function show_banner_after_bulk_print_ipc() {
		$banner_html = '<div class="banner_after_bulk_print_ipc notice">
			<div style="width:75%;text-align:left;">
				<p>
					<span><svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.59664 13.1544H5.28993C5.05644 13.1544 4.86711 13.3437 4.86711 13.5772C4.86711 13.8107 5.05644 14 5.28993 14H7.59664C7.83013 14 8.01946 13.8107 8.01946 13.5772C8.01946 13.3437 7.83013 13.1544 7.59664 13.1544ZM6.44329 1.67342C6.67678 1.67342 6.86611 1.48409 6.86611 1.2506V0.422819C6.86611 0.189329 6.67678 0 6.44329 0C6.2098 0 6.02047 0.189329 6.02047 0.422819V1.2506C6.02047 1.48409 6.2098 1.67342 6.44329 1.67342ZM12.4638 5.54785H11.636C11.4025 5.54785 11.2132 5.73718 11.2132 5.97067C11.2132 6.20416 11.4025 6.39349 11.636 6.39349H12.4638C12.6972 6.39349 12.8866 6.20416 12.8866 5.97067C12.8866 5.73718 12.6977 5.54785 12.4638 5.54785ZM1.25013 5.54785H0.422819C0.189329 5.54785 0 5.73718 0 5.97067C0 6.20416 0.189329 6.39349 0.422819 6.39349H1.2506C1.48409 6.39349 1.67342 6.20416 1.67342 5.97067C1.67342 5.73718 1.48362 5.54785 1.25013 5.54785ZM2.80658 2.93201C2.88926 3.0147 2.99732 3.05604 3.10584 3.05604C3.21436 3.05604 3.32242 3.0147 3.4051 2.93201C3.57047 2.76711 3.57047 2.49933 3.4051 2.33443L2.81926 1.74859C2.65389 1.58322 2.38611 1.58322 2.22121 1.74859C2.05584 1.91349 2.05584 2.18128 2.22121 2.34617L2.80658 2.93201ZM10.0673 1.74859L9.48195 2.33396C9.31658 2.49886 9.31658 2.76664 9.48195 2.93154C9.56463 3.01423 9.67268 3.05557 9.78121 3.05557C9.88973 3.05557 9.99779 3.01423 10.0805 2.93154L10.6658 2.34617C10.8312 2.18128 10.8312 1.91349 10.6658 1.74859C10.5005 1.58369 10.2322 1.58369 10.0673 1.74859ZM6.44329 2.97946C4.31745 2.97617 2.58812 4.70503 2.58812 6.83463C2.58812 7.85973 2.99027 8.79228 3.64799 9.48054C4.08443 9.93765 4.3555 10.5272 4.3555 11.1591V11.5195C4.3555 11.7788 4.56597 11.9893 4.8253 11.9893H6.44329H8.06128C8.3206 11.9893 8.53107 11.7788 8.53107 11.5195V11.1591C8.53107 10.5272 8.80168 9.93765 9.23859 9.48054C9.89584 8.79228 10.2985 7.8602 10.2985 6.83463C10.2985 4.70503 8.56913 2.9757 6.44329 2.97946ZM6.0651 5.32658C5.51356 5.46376 5.07007 5.9096 4.93476 6.46255C4.88732 6.65564 4.71443 6.7853 4.52463 6.7853C4.49128 6.7853 4.45698 6.78107 4.42362 6.77309C4.19671 6.71765 4.05812 6.48886 4.11356 6.26195C4.32591 5.39423 4.99537 4.72148 5.86074 4.50584C6.08718 4.4504 6.31738 4.58711 6.37329 4.81403C6.42966 5.04094 6.29154 5.2702 6.0651 5.32658Z" fill="#5454A5"/>
					</svg></span>
					<span style="color:#5454A5;font-size:15px;font-style: normal;font-weight: 500;">'. __( "Did you know?", "print-invoices-packing-slip-labels-for-woocommerce" ).'</span>
					<span>'. __( "You can now bulk export PDF invoices, packing slips, and credit notes of WooCommerce orders with custom filters.", "print-invoices-packing-slip-labels-for-woocommerce" ).'</span>
				</p>
			</div>
			<div style="width:20%;text-align:left;">
				<button class="button banner_close_button wt_pklist_banner_dismiss" data-href-link="https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_report&utm_medium=pdf_basic&utm_campaign=PDF_invoice">'. __( "Checkout", "print-invoices-packing-slip-labels-for-woocommerce" ) .'  <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.4243 5.42426C11.6586 5.18995 11.6586 4.81005 11.4243 4.57574L7.60589 0.757359C7.37157 0.523044 6.99167 0.523044 6.75736 0.757359C6.52304 0.991674 6.52304 1.37157 6.75736 1.60589L10.1515 5L6.75736 8.39411C6.52305 8.62843 6.52305 9.00833 6.75736 9.24264C6.99167 9.47696 7.37157 9.47695 7.60589 9.24264L11.4243 5.42426ZM8.06978e-08 5.6L11 5.6L11 4.4L-8.06978e-08 4.4L8.06978e-08 5.6Z" fill="white"/></svg></button>
			</div>
			<div style="width:5%; text-align:right;">
				<svg class="wt_pklist_banner_dismiss" width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M9.5 1L1 9.5" stroke="#505050" stroke-width="1.5"/>
					<path d="M1 1L9.5 9.5" stroke="#505050" stroke-width="1.5"/>
				</svg>
			</div>
		</div>';
		echo wp_kses( $banner_html, array(
			'div' => array(
				'class' => array(),
				'style' => array()
			),
			'svg' => array(
				'width' => array(),
				'height' => array(),
				'viewBox' => array(),
				'fill' => array(),
				'xmlns' => array(),
				'class' => array(),
			),
			'path' => array(
				'd' => array(),
				'fill' => array(),
				'stroke' => array(),
				'stroke-width' => array()
			),
			'p' => array(),
			'span' => array(
				'class' => array(),
				'style' => array()
			),
			'button' => array(
				'class' => array(),
				'data-href-link' => array()
			),
		) );
	}

	public function add_scripts_for_banner_after_bulk_print_ipc() {
		$ajax_url=admin_url('admin-ajax.php');
		$nonce=wp_create_nonce($this->plugin_prefix);
		?>
		<script type="text/javascript">
		    (function($){
		        "use strict";
				$( '.banner_after_bulk_print_ipc .wt_pklist_banner_dismiss' ).on( 'click', function(e) {
					e.preventDefault();
					if ( $(this).hasClass( 'banner_close_button') ) {
						var data_link = $(this).attr('data-href-link');
						window.open(data_link, '_blank');
					}

					var data_obj={
						_wpnonce: '<?php echo esc_attr($nonce);?>',
						action: 'close_banner_after_bulk_print_ipc',
					};
					
					$.ajax({
						url:'<?php echo esc_url($ajax_url);?>',
						data:data_obj,
						type: 'POST'
					});
					$('.banner_after_bulk_print_ipc').remove();
				});
			})(jQuery);
		</script>
		<?php
	}

	public function process_user_action_for_banner_after_bulk_print_ipc() {
		check_ajax_referer($this->plugin_prefix);
		update_option('wt_pklist_banner_after_bulk_print_ipc', 0);
		exit();
	}
}
new Wf_Woocommerce_Packing_List_Review_Request();