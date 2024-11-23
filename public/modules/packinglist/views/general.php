<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id);?>">
	<div class="wf-tab-content-inner">
		<form method="post" class="wf_settings_form">
			<input type="hidden" value="<?php echo esc_attr($this->module_base); ?>" class="wf_settings_base" />
			<input type="hidden" value="wf_save_settings" class="wf_settings_action" />
			<input type="hidden" value="wt_packinglist_general" name="wt_tab_name" class="wt_tab_name" />
				<p><?php _e('Configure the general settings required for the packing slip.','print-invoices-packing-slip-labels-for-woocommerce');?></p>
				<?php
					// Set nonce:
					if (function_exists('wp_nonce_field'))
					{
						wp_nonce_field('wf-update-packinglist-'.WF_PKLIST_POST_TYPE);
					}
					?>
					<table class="wf-form-table" style="width: 100%;">
						<?php
							$settings_arr['packingslip_general_general'] = array(
								'woocommerce_wf_attach_image_packinglist' => array(
										'type' => 'wt_radio',
										'label' => __("Include product image","print-invoices-packing-slip-labels-for-woocommerce"),
										'id' => '',
										'class' => 'woocommerce_wf_attach_image_packinglist',
										'name' => 'woocommerce_wf_attach_image_packinglist',
										'value' => '',
										'radio_fields' => array(
												'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
												'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
											),
										'col' => 3,
										'tooltip' => true,
										'alignment' => 'horizontal_with_label',
									),
								'woocommerce_wf_add_customer_note_in_packinglist' => array(
										'type' => 'wt_radio',
										'label' => __("Add customer note","print-invoices-packing-slip-labels-for-woocommerce"),
										'id' => '',
										'class' => 'woocommerce_wf_add_customer_note_in_packinglist',
										'name' => 'woocommerce_wf_add_customer_note_in_packinglist',
										'value' => '',
										'radio_fields' => array(
												'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
												'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
											),
										'col' => 3,
										'tooltip' => true,
										'alignment' => 'horizontal_with_label',
									),
								'woocommerce_wf_packinglist_footer_pk' => array(
										'type' => 'wt_radio',
										'label' => __("Add footer","print-invoices-packing-slip-labels-for-woocommerce"),
										'id' => '',
										'class' => 'woocommerce_wf_packinglist_footer_pk',
										'name' => 'woocommerce_wf_packinglist_footer_pk',
										'value' => '',
										'radio_fields' => array(
												'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
												'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
											),
										'col' => 3,
										'tooltip' => true,
										'alignment' => 'horizontal_with_label',
									),
							);
							
							$settings_arr = Wf_Woocommerce_Packing_List::add_fields_to_settings($settings_arr,$target_id,$this->module_base,$this->module_id);
							if(class_exists('WT_Form_Field_Builder_PRO_Documents')){
								$Form_builder = new WT_Form_Field_Builder_PRO_Documents();
							}else{
								$Form_builder = new WT_Form_Field_Builder();
							}

							$h_no = 1;
							foreach($settings_arr as $settings){
								foreach($settings as $k => $this_setting){
									if(isset($this_setting['type']) && "wt_sub_head" === $this_setting['type']){
										$settings[$k]['heading_number'] = $h_no;
										$h_no++;
									}
								}
								$Form_builder->generate_form_fields($settings, $this->module_id);
							}
						?>
					</table>
					<?php if(false === $pro_installed){
						?>
						<div style="clear: both;"></div>
						<span id="end_wf_setting_form" class="end_wf_setting_form"></span>
						<input type="submit" name="update_admin_settings_form" value="<?php echo __('Update Settings', 'print-invoices-packing-slip-labels-for-woocommerce'); ?>" class="button button-primary wt_pklist_update_settings_btn"/>
						<span class="spinner" style="margin-top:11px"></span>
						<?php
					}else{
						include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
					}
					?>
					<?php 
					//settings form fields for module
					do_action('wf_pklist_document_settings_form');?>   
		</form>
		<?php
			if(false === $pro_installed){
				$sidebar_pro_link = 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_sidebar&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION;
				$packinglist_pro_feature_list = array(
					__("Automatically attach packing slip with order email","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Built-in templates to personalize packing slip","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Add a print packing slip button to My account page","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Group products by category","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Sort order items in the product table","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Show variation data for variable products","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Multiple display formats for bundled products","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Add custom footers to packing slips","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Add additional order & product meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Add additional product attributes","print-invoices-packing-slip-labels-for-woocommerce"),
					__("Generate invoices and credits notes","print-invoices-packing-slip-labels-for-woocommerce"),
				);
			?>
			<div class="wt_pro_plugin_promotion">
				<div class="wt_pro_addon_tile_doc" style="<?php echo is_rtl() ? 'left:0;' : 'right:0;'; ?>">
					<div class="wt_pro_addon_widget_doc">
					<?php
						/**
						 * @since 4.7.0 - Add offer for Black Friday Cyber Monday 2024
						 */
						if( Wt_Pklist_Common::is_bfcm_season() ) {
						?>
					<div class="bfcm_doc_settings">
						<img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/modules/banner/assets/images/bfcm-doc-settings-coupon.svg'); ?>">
					</div>
						<?php
						}
					?>
						<div class="wt_pro_addon_widget_wrapper_doc">
							<p><?php _e('Get advanced features for your','print-invoices-packing-slip-labels-for-woocommerce'); ?></p>
							<div class="wt_pro_addon_widget_wrapper_doc_logo_title">
								<div class="wt_pro_addon_widget_wrapper_doc_logo_title_col_1">
									<img src="<?php echo esc_url(WF_PKLIST_PLUGIN_URL . 'admin/images/wt_ipc_logo.png'); ?>">
								</div>
								<div class="wt_pro_addon_widget_wrapper_doc_logo_title_col_2">
									<h4><?php echo __("Invoices, Packing slips and Credit notes","print-invoices-packing-slip-labels-for-woocommerce"); ?></h4>
								</div>
							</div>
						</div>
						<div class="wt_pro_addon_features_list_doc">
							<ul>
								<?php
									foreach($packinglist_pro_feature_list as $p_feature){
										?>
										<li><?php echo esc_html_e($p_feature); ?></li>
										<?php
									}
								?>
							</ul>
						</div>
						<div class="wt_pro_show_more_less_doc">
							<a class="wt_pro_addon_show_more_doc"><p><? echo __("Show More","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
							<a class="wt_pro_addon_show_less_doc"><p><? echo __("Show Less","print-invoices-packing-slip-labels-for-woocommerce"); ?></p></a>
						</div>
						<a class="wt_pro_addon_premium_link_div_doc" href="<?php echo esc_url($sidebar_pro_link); ?>" target="_blank">
							<?php _e("View add-on","print-invoices-packing-slip-labels-for-woocommerce"); ?> <span class="dashicons dashicons-arrow-right-alt"></span>
						</a>
					</div>
				</div>
			</div>
		<?php
		}
		?>
	</div>
</div>
<?php do_action('wf_pklist_document_out_settings_form');?> 