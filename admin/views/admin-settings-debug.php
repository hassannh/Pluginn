<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<style>
	.wt_pklist_debug_table{
		width: 40%;
		margin-left: 2em;
	}
	.wt_pklist_debug_table tr th:first-child{width: 50%;}
	.wt_pklist_reset_settings{background-color: #FFF4F4 !important; color: #A02222 !important;border:1px solid #A02222 !important;}
	.wt_pklist_reset_settings:hover{background-color: #A02222 !important; color: #FFF4F4 !important;border:1px solid #A02222 !important;}
	.wt_pklist_imp_exp_settings{background-color: #F5F7FA !important; color: #3157A6 !important;border:1px solid #3157A6 !important;}
	.wt_pklist_imp_exp_settings:hover{background-color: #3157A6 !important; color: #FFF !important;border:1px solid #3157A6 !important;}
	.wt_debug_status_div{float: left;width: 100%;}
	.wt_debug_error{color: #721c24;background-color: #f8d7da;border-color: #f5c6cb;position: relative;padding: 0.75rem 1.25rem;margin-bottom: 1rem;border: 1px solid transparent;border-radius: 0.25rem;float: left;width: 60%;}
	.wt_debug_success{color: #155724;background-color: #d4edda;border-color: #c3e6cb;position: relative;padding: 0.75rem 1.25rem;margin-bottom: 1rem;border: 1px solid transparent;border-radius: 0.25rem;float: left;width: 60%;}
	.wt_pklist_import_settings_popup table,.wt_pklist_reset_settings_popup table{margin-top:10px;}
	.wt_pklist_import_settings_popup td{padding: 5px;}
	.wt_pklist_reset_settings_popup td{padding: 5px 0px; }
</style>
<div class="wf-tab-content" data-id="<?php echo esc_attr($target_id); ?>">
	<?php
		if("POST" === $_SERVER['REQUEST_METHOD'] && isset($_POST['wt_pklist_settings_import_confirm_text']) && isset($_POST['wt_status']) && isset($_POST['wt_status_message'])){
			if(!empty($_POST['wt_status_message'])){
				$debug_msg_class = (0 === $_POST['wt_status']) ? 'wt_debug_success' : 'wt_debug_error';
				echo '<div class="wt_debug_status_div">
						<span class="'.esc_attr($debug_msg_class).'">'.esc_html($_POST['wt_status_message']).'</span>
					</div>';
				?>
				<?php
			}
		}

		if("POST" === $_SERVER['REQUEST_METHOD'] && isset($_POST['wt_pklist_settings_reset_confirm_text']) && isset($_POST['wt_reset_status'])){
			if(!empty($_POST['wt_reset_status'])){
				$reset_msg_class = (1 === $_POST['wt_reset_status']) ? 'wt_debug_success' : 'wt_debug_error';
				$reset_msg = (1 === $_POST['wt_reset_status'])  ? __("Reset successfully","print-invoices-packing-slip-labels-for-woocommerce") : $_POST['wt_status_message'];
				echo '<div class="wt_debug_status_div">
						<span class="'.esc_attr($reset_msg_class).'">'.esc_html($reset_msg).'</span>
					</div>';
				?>
				<?php
			}
		}
	?>
	<h3><?php _e('Debug','print-invoices-packing-slip-labels-for-woocommerce');?></h3>
	<p><span class="dashicons dashicons-warning" style="color:#ef2424;"></span> <?php _e('Caution: Settings here are only for advanced users.','print-invoices-packing-slip-labels-for-woocommerce');?></p>
	<form method="post">
		<?php
	    // Set nonce:
	    if(function_exists('wp_nonce_field'))
	    {
	        wp_nonce_field(WF_PKLIST_PLUGIN_NAME);
	    }
	    ?>
		<table class="wf-form-table wt_pklist_debug_table">
			<?php
	        $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
	        if($wt_pklist_common_modules===false)
	        {
	            $wt_pklist_common_modules=array();
	        }
	        ?>
	        <tr valign="top">
	            <th scope="row">Common modules</th>
	            <td>
	                <?php
	                foreach($wt_pklist_common_modules as $k=>$v)
	                {
	                    if("" !== $k){
	                    	echo '<input type="checkbox" name="wt_pklist_common_modules['.$k.']" value="1" '.($v==1 ? 'checked' : '').' /> ';
		                    echo $k;
		                    echo '<br />';
	                    }
	                }
	                ?>
	            </td>
	        </tr>
	        <?php
	        $wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
	        if($wt_pklist_admin_modules===false)
	        {
	            $wt_pklist_admin_modules=array();
	        }
	        ?>
	        <tr valign="top">
	            <th scope="row">Admin modules</th>
	            <td>
	                <?php
	                foreach($wt_pklist_admin_modules as $k=>$v)
	                {
	                    if("" !== $k){
	                    	echo '<input type="checkbox" name="wt_pklist_admin_modules['.$k.']" value="1" '.($v==1 ? 'checked' : '').' /> ';
		                    echo $k;
		                    echo '<br />';
	                    }
	                }
	                ?>
	            </td>
	        </tr>

	        <tr valign="top">
	            <th scope="row">&nbsp;</th>
	            <td>
	                <input type="submit" name="wt_pklist_admin_modules_btn" value="Save" class="button-primary">
	            </td>
	        </tr>	
		</table>
	</form>
	
	<hr>
	<form id="wt_pklist_export_settings_form">
		<input type="hidden" name="_wtpdf_debug_settings_export_nonce" id="wtpdf_debug_settings_export_nonce_id" value="<?php echo esc_attr( wp_create_nonce( WF_PKLIST_PLUGIN_NAME . '_debug_export_form' ) ); ?>">		
		<table class="wf-form-table wt_pklist_debug_table">
			<tr valign="top">
				<th scope="row"><?php _e("Export settings (JSON)","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
				<td>
					<input type="button" class="wt_pklist_imp_exp_settings wt_pklist_export_settings button-primary" value="<?php _e("Export","print-invoices-packing-slip-labels-for-woocommerce"); ?>">
				</td>
			</tr>
		</table>
	</form>

	<form id="wt_pklist_import_settings_form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="_wtpdf_debug_settings_import_nonce" id="_wtpdf_debug_settings_import_nonce_id" value="<?php echo esc_attr( wp_create_nonce( WF_PKLIST_PLUGIN_NAME . '_debug_import_form' ) ); ?>">		
		<table class="wf-form-table wt_pklist_debug_table" style="width:60%;">
			<tr valign="top">
				<th scope="row" style="width: 24%;"><?php _e("Import settings (JSON)","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
				<td>
					<input type="file" id="wt_pklist_import_setting_file" name="wt_pklist_import_setting_file" accept="application/json" style="float: left;margin:0;">
					<br><br>
					<input type="submit" name="wt_pklist_import_settings" class="wt_pklist_imp_exp_settings wt_pklist_import_settings button-primary" value="<?php _e("Import","print-invoices-packing-slip-labels-for-woocommerce"); ?>" data-popup-id="wt_pklist_import_settings_popup" data-popup-alert="<?php _e("Please select json file","print-invoices-packing-slip-labels-for-woocommerce"); ?>">
					<?php
					if(false !== get_option('wt_pklist_import_date',true)){
						if(!empty(get_option('wt_pklist_import_date'))){
							?>
					<br><br><span style="font-size: 12px;font-style: italic;"><?php _e("Last imported on:","print-invoices-packing-slip-labels-for-woocommerce"); ?> <?php echo date('Y/d/M h:i:s A',get_option('wt_pklist_import_date')); ?></span>
							<?php
							}
						}
					?>
				</td>
			</tr>
		</table>
		<div class="wt_pklist_import_settings_popup wf_pklist_popup" style="width:40%;text-align:left;">
			<div style="float:left;padding:20px;">
			<div class="wt_pklist_import_settings_popup_main wf_pklist_popup_body">
				<div class="message" style="float:left; box-sizing:border-box; width:100%; padding:0px 5px; margin-bottom:15px;">
					<span class="dashicons dashicons-warning" style="color:#ef2424;"></span> <?php _e('Importing the file will delete all the settings and replace with data from chosen file',"print-invoices-packing-slip-labels-for-woocommerce"); ?>
					<br>
				</div>
				<div id="wt_pklist_settings_import_confirm_text_div" style="float: left;box-sizing: border-box;width: 100%;padding: 0px 5px;margin-bottom: 5px;">
					<table>
						<tr>
							<th>
								<label><?php _e("Templates","print-invoices-packing-slip-labels-for-woocommerce"); ?></label>
							</th>
						</tr>
						<tr>
							<td>
								<input type="radio" id="template_import_append" name="template_import" value="append" class="template_import" checked><label for="template_import_append"><?php _e('Combine with the existing templates','print-invoices-packing-slip-labels-for-woocommerce'); ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<input type="radio" id="template_import_override" name="template_import" value="override" class="template_import"><label for="template_import_override"><?php _e('Replace the existing templates','print-invoices-packing-slip-labels-for-woocommerce'); ?></label>
							</td>
						</tr>
					</table>
					<br>
					<?php printf(__('To proceed with the import, please type %1$s in the field below',"print-invoices-packing-slip-labels-for-woocommerce"),'`confirm`'); ?>
					<input type="text" id="wt_pklist_settings_import_confirm_text" name="wt_pklist_settings_import_confirm_text" style="margin-top: 12px;" autocomplete="off">
					<span id="wt_pklist_import_settings_popup_error"></span>
				</div>
			</div>
			<div class="wf_pklist_popup_footer" style="float:left;">
				<button type="button" name="" class="button-secondary wf_pklist_popup_cancel" style="color: #3157A6;border-color: #3157A6;">
					<?php _e("Cancel","print-invoices-packing-slip-labels-for-woocommerce"); ?>
				</button>
				<button type="button" name="" class="button-primary wt_pklist_import_settings_popup_yes" style="background: #3157A6;">
					<?php _e("Import","print-invoices-packing-slip-labels-for-woocommerce"); ?>
				</button>	
			</div>
			</div>
		</div>
	</form>

	<form id="wt_pklist_reset_settings_form" method="post">
		<input type="hidden" name="_wtpdf_debug_settings_reset_nonce" id="wtpdf_debug_settings_reset_nonce_id" value="<?php echo esc_attr( wp_create_nonce( WF_PKLIST_PLUGIN_NAME . '_debug_reset_form' ) ); ?>">	
		<table class="wf-form-table wt_pklist_debug_table">
			<tr valign="top">
				<th scope="row"><?php _e("Reset all settings to default","print-invoices-packing-slip-labels-for-woocommerce"); ?></th>
				<td>
					<input type="submit" name="wt_pklist_reset_settings" class="wt_pklist_reset_settings button-primary" value="<?php _e("Reset","print-invoices-packing-slip-labels-for-woocommerce"); ?>" data-popup-id="wt_pklist_reset_settings_popup">
					<?php
					if(false !== get_option('wt_pklist_reset_date',true)){
						if(!empty(get_option('wt_pklist_reset_date'))){
							$utc_timestamp_converted = date( 'Y-m-d h:i:s', get_option('wt_pklist_reset_date'));
							?>
					<br><br><span style="font-size: 12px;font-style: italic;"><?php _e("Last updated on:","print-invoices-packing-slip-labels-for-woocommerce"); ?> <?php echo get_date_from_gmt( $utc_timestamp_converted, 'Y-m-d h:i:s' ); ?></span>
							<?php
							}
						}
					?>
				</td>
			</tr>
		</table>
		<div class="wt_pklist_reset_settings_popup wf_pklist_popup" style="width:40%;text-align:left;">
			<div style="float:left;padding:20px;">
			<div class="wt_pklist_reset_settings_popup_main wf_pklist_popup_body">
				<div class="message" style="float:left; box-sizing:border-box; width:100%; padding:0px 5px;">
					<span class="dashicons dashicons-warning" style="color:#ef2424;"></span> <?php _e('Reset will delete all the settings, saved templates and reset to the default settings',"print-invoices-packing-slip-labels-for-woocommerce"); ?>
					<br>
				</div>
				<div id="wt_pklist_settings_reset_confirm_text_div" style="float: left;box-sizing: border-box;width: 100%;padding: 0px 5px;margin-bottom: 5px;">
					<table style="margin-bottom: 10px;">
						<tr>
							<td>
								<input type="checkbox" id="dont_reset_template" name="dont_reset_template" value="1" class="dont_reset_template"><label for="dont_reset_template"><?php _e('Do not reset the templates','print-invoices-packing-slip-labels-for-woocommerce'); ?></label>
							</td>
						</tr>
					</table>
					<?php printf(__('To proceed with the reset, please type %1$s in the field below',"print-invoices-packing-slip-labels-for-woocommerce"),'`confirm`'); ?>
					<br>
					<input type="text" id="wt_pklist_settings_reset_confirm_text" name="wt_pklist_settings_reset_confirm_text" style="margin-top: 12px;" autocomplete="off">
					<span id="wt_pklist_reset_settings_popup_error"></span>
				</div>
			</div>
			<div class="wf_pklist_popup_footer" style="float:left;">
				<button type="button" name="" class="button-secondary wf_pklist_popup_cancel" style="color: #3157A6;border-color: #3157A6;">
					<?php _e("Cancel","print-invoices-packing-slip-labels-for-woocommerce"); ?>
				</button>
				<button type="button" name="" class="button-primary wt_pklist_reset_settings_popup_yes" style="background: #3157A6;">
					<?php _e("Reset","print-invoices-packing-slip-labels-for-woocommerce"); ?>
				</button>	
			</div>
			</div>
		</div>
	</form>
<?php
//advanced settings form fields for module
do_action('wt_pklist_module_settings_debug');
?>
</div>