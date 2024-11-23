<?php
if (!defined('ABSPATH')) {
	exit;
}
if ( isset( $template_type ) && !empty( $template_type ) ) {
?>
<style type="text/css">
/* popup - Customizer promotion popup */
.wt_pklist_customizer_promotion_header{ float: left; width: 100%; position: relative; }
.wt_pklist_customizer_promotion_header .wf_pklist_popup_close{ position: absolute; right: 0; padding: 0 15px;}
.wt_pklist_customizer_promotion_content{width: 70%; margin: auto;margin-top: 30px;}
.wt_pklist_customizer_promotion_content_title p{ font-style: normal; font-weight: 600; font-size: 15px; line-height: 140.69%; text-align: center; color: #000000; }
.wt_pklist_customizer_promotion_content_title p span{color: #3171FB;}
.wt_pklist_customizer_promotion_content_list{ display: flex; flex-direction: column; text-align: left; font-style: normal; font-weight: 400; font-size: 13px; line-height: 160.7%; color: #000000; }
.wt_pklist_customizer_promotion_content_list_row{ display: flex; flex-direction: row; justify-content: space-evenly; margin: auto;}

.wt_pklist_customizer_promotion_footer{ width: 100%; display: flex; justify-content: center; margin: 20px 0 35px; }
.wt_pklist_customizer_promotion_footer a { background: #1DA5F8; font-style: normal; font-weight: 600; font-size: 14px; line-height: 140.69%; color: #FFFFFF; padding:11px 35px; border-radius: 6px; text-decoration: none; }
<?php 
if ( $template_type === 'packinglist' ) {
?>
.wt_pklist_customizer_promotion_content_list_row{width: 65%;}
.wt_pklist_customizer_promotion_content_list_row p{width: 100%;margin: 0.5em;}
<?php
} else{
?>
.wt_pklist_customizer_promotion_content_list_row{width: 80%;}
.wt_pklist_customizer_promotion_content_list_row p{width: 40%; margin: 0.5em;}
<?php
}
?>
</style>
<!-- Customizer promotion popup for invoice, packing slip and shipping label -->
<?php
	$customizer_promotion_content = array(
		'invoice' => array(
			'title' 	=> sprintf( '%1$s <span> %2$s </span>', __( 'Get advanced customization options for', 'print-invoices-packing-slip-labels-for-woocommerce' ), __( 'invoices, packing slips, and credit notes.', 'print-invoices-packing-slip-labels-for-woocommerce' ) ),
			'features' 	=> array(
				__('Add additional fields to invoices', 'print-invoices-packing-slip-labels-for-woocommerce'),
				__('Multiple pre-built templates','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Customize using Code Editor','print-invoices-packing-slip-labels-for-woocommerce'),
				__('More customization options', 'print-invoices-packing-slip-labels-for-woocommerce')
			),
			'link'		=> 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_customizesection&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
		),

		'packinglist' => array(
			'title' 	=> __( 'Upgrade for more customization on packing slip', 'print-invoices-packing-slip-labels-for-woocommerce' ),
			'features' 	=> array(
				__('Advanced customization option for packing slip templates', 'print-invoices-packing-slip-labels-for-woocommerce'),
				__('Show/hide fields like billing address, weight, product image etc','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Add additional data such as product meta, order meta, product attributes etc','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Group and sort product table based on different conditions', 'print-invoices-packing-slip-labels-for-woocommerce'),
				__('Many more customization options', 'print-invoices-packing-slip-labels-for-woocommerce')
			),
			'link'		=> 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_packingslip_menu&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
		),

		'shippinglabel' => array(
			'title' 	=> sprintf( '%1$s <span> %2$s </span>', __( 'Get advanced customization options for', 'print-invoices-packing-slip-labels-for-woocommerce' ), __( 'shipping labels, dispatch labels, and delivery notes.', 'print-invoices-packing-slip-labels-for-woocommerce' ) ),
			'features' 	=> array(
				__('Add additional fields to shipping labels', 'print-invoices-packing-slip-labels-for-woocommerce'),
				__('Multiple pre-built templates','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Customize using Code Editor','print-invoices-packing-slip-labels-for-woocommerce'),
				__('More customization options', 'print-invoices-packing-slip-labels-for-woocommerce'),
			),
			'link'		=> 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_customizesection&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION,
		),
	);
	$is_pro_customizer = apply_filters('wt_pklist_pro_customizer_'.$template_type,false,$template_type);
	if ( false === $is_pro_customizer && isset( $customizer_promotion_content[$template_type] ) ) {

	?>
	<div class="wt_pklist_customizer_promotion wf_pklist_popup">
    <div class="wt_pklist_customizer_promotion_bg" style=" position: absolute; top: 0; left: 0;">
        <svg width="114" height="98" viewBox="0 0 114 98" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 1.74084V94.1024C8.06275 96.546 16.6093 97.8618 25.4613 97.8618C74.2113 97.8618 113.727 58.0806 113.727 9.00331C113.727 5.67966 113.541 2.40301 113.188 -0.822388H2.54613C1.14152 -0.822388 0 0.326792 0 1.74084Z" fill="#E9F5FF" fill-opacity="0.67"></path>
        </svg>
    </div>
    <div class="wt_pklist_customizer_promotion_bg" style=" position: absolute; right: 0; bottom: 0;">
        <svg width="98" height="82" viewBox="0 0 98 82" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M73.1859 0C32.7672 0 0 32.6498 0 72.9237C0 75.9971 0.194744 79.0267 0.565385 82H94.2308C96.3101 82 98 80.3162 98 78.2443V4.30031C90.2542 1.51481 81.8991 0 73.1859 0Z" fill="#E9F5FF" fill-opacity="0.67"></path>
        </svg>
    </div>
		<div class="wt_pklist_customizer_promotion_header">
			<div class="wf_pklist_popup_close">
			<svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1 1L10 10" stroke="black"/>
				<path d="M10 1L1 10" stroke="black"/>
			</svg>
			</div>
		</div>
		<div class="wt_pklist_customizer_promotion_content">
			<div class="wt_pklist_customizer_promotion_content_image">
				<img src="<?php echo esc_url( WF_PKLIST_PLUGIN_URL.'assets/images/promotion_crown.png' ); ?>" alt="promotion_crown">
			</div>
			<div class="wt_pklist_customizer_promotion_content_title">
				<p>
					<?php 
						echo wp_kses_post( $customizer_promotion_content[$template_type]['title'] );
					?>
				</p>
			</div>
		</div>
		<div class="wt_pklist_customizer_promotion_content_list" style="width: 100%;">
			<?php
                if ( 'packinglist' === $template_type ) {
                    $feature_list = array_chunk( $customizer_promotion_content[$template_type]['features'], 1);
                } else {
                    $feature_list = array_chunk( $customizer_promotion_content[$template_type]['features'], 2);
                }
				
				foreach( $feature_list as $feature_chunk ){
			?>
				<div class="wt_pklist_customizer_promotion_content_list_row">
					<?php foreach ( $feature_chunk as $point ) { ?>
						<p><svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 4L5 7L10 2" stroke="#6ABE45" stroke-width="3"/></svg> <?php echo esc_html($point); ?></p>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<div class="wt_pklist_customizer_promotion_footer">
				<a href="<?php echo esc_url($customizer_promotion_content[$template_type]['link']) ?>" class="wt_pklist_customizer_promotion_premium_btn" target="_blank">
				<img src="<?php echo esc_url( WF_PKLIST_PLUGIN_URL.'admin/images/other_solutions/promote_crown.png' ); ?>" style="width: 14px;height: 13px;margin-right: 7px;"><?php  echo __( 'Upgrade To Premium', 'print-invoices-packing-slip-labels-for-woocommerce' ); ?>
			</a>
		</div>
		<?php
		/**
		 * @since 4.7.0 - Add offer for Black Friday Cyber Monday 2024
		 */
		if( Wt_Pklist_Common::is_bfcm_season() ) {
			?>
		<div class="wt_pklist_customizer_promotion_footer_bfcm_banner" style="position:relative;">
				<img src="<?php echo esc_url( WF_PKLIST_PLUGIN_URL.'admin/modules/banner/assets/images/bfcm-packingslip-customizer.svg' ); ?>" style="vertical-align:bottom;">
			</a>
		</div>
		<?php
			}
		?>
	</div>
	<?php
		} // end of is_pro_customizer false and popup available for template type.
    }// end of isset of template_type.
	?>