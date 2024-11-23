<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
$settings_button_title=isset($settings_button_title) ? $settings_button_title : __('Update Settings', 'print-invoices-packing-slip-labels-for-woocommerce');
$enable_btn=(int) (!isset($enable_save_btn) ? 1 : $enable_save_btn);
?>
<div style="clear: both;"></div>
<span id="end_wf_setting_form" class="end_wf_setting_form"></span>
<div class="wf-plugin-toolbar bottom" id="wf-plugin-toolbar">
	<div class="wt_pklist_update_settings_btn_div">
		<?php
		if(1 === $enable_btn)
		{
		?>
			<input type="submit" name="update_admin_settings_form" value="<?php echo esc_html($settings_button_title); ?>" class="button button-primary wt_pklist_update_settings_btn wt_pklist_update_settings_btn_invoice"/>
			<span class="spinner" style="margin-top:11px"></span>
		<?php
		}
		?>
	</div>
    <div class="left">
    </div>
    <div class="right">
    </div>
</div>