<?php
if (!defined('ABSPATH')) {
	exit;
}
Class WT_Form_Field_Builder{

	public function generate_form_fields($settings,$base_id=""){
		$html = "";
		if(!empty($settings)){
			$h_no = 1;
			foreach($settings as $this_setting){
				if(isset($this_setting['type'])){
					$row_full_length = array('wt_hr_line','wt_sub_head','wt_plaintext','invoice_number_format');
					$row_full_length = apply_filters( 'wt_pklist_add_full_length_elements_in_settings', $row_full_length, $base_id );
					if(in_array($this_setting['type'],$row_full_length)){
						$html .= $this->{$this_setting["type"]}($this_setting,$base_id);
					}else{
						extract($this->verify_the_fields($this_setting));
						if(trim($tr_id) != ""){
							$tr_id = 'id="'.$tr_id.'"';
						}else{
							$tr_id = "";
						}
						$html .= '<tr valign="top" '.$tr_id.' '.$form_toggler_child.'>'.$this->display_label($this_setting,$base_id).$this->{$this_setting["type"]}($this_setting,$base_id).'
				                </tr>';
					}
				}
			}
		}
		echo $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display the label of the setting field
	 */
	public function display_label($args,$base_id){
		extract($this->verify_the_fields($args));
		$html ="";
		$label_style = "";
		if($tooltip){
        	$html = Wf_Woocommerce_Packing_List_Admin::set_tooltip($name,$base_id);
        }

        if(is_array($label)){
        	$label_style = isset( $label["style"] ) ? $label["style"] : "";
        	$label = ( $label["text"] ) ? $label["text"] : "";
        }
        $mandatory_star = ($mandatory ? '<span class="wt_pklist_required_field">*</span>' : '');
        return sprintf('<th scope="row"><label for="" style="%1$s">%2$s</label></th>',esc_attr($label_style),wp_kses_post($label).$mandatory_star.$html);
	}

	/**
	 * @since 4.0.0
	 * Function to display toggle checkbox
	 */
	public function wt_toggle_checkbox($args,$base_id=""){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;
    	$checkbox_label = "";
		if(is_array($checkbox_fields)){
			if(isset($checkbox_fields[$value])){
				$checkbox_label = $checkbox_fields[$value];
			}
		}
    	$html = sprintf('<td>
    			<div class="wf_pklist_dashboard_checkbox">
                	<input type="checkbox" class="wf_slide_switch %1$s" id="%2$s" name="%3$s" value="%4$s" %5$s> %6$s
            	</div>',
            	esc_attr($class),
            	esc_attr($id),
            	esc_attr($name),
            	$value,
            	checked($result,$value,false),
            	esc_html($checkbox_label));
    	$html .=sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display the horziontal dotted line
	 */
	public function wt_hr_line($args,$base_id){
		extract($this->verify_the_fields($args));
		return sprintf('<tr><td colspan="3" style="border-bottom: dashed 1px #ccc;" class="%1$s"></td></tr>',esc_attr($class));
	}

	/**
	 * @since 4.0.0
	 * Function to display the horziontal dotted line
	 */
	/*public function wt_plaintext($args,$base_id){
		extract($this->verify_the_fields($args));
		return sprintf('<tr><td>%1$s</td><td>%2$s</td><td></td></tr>',wp_kses_post($label),wp_kses_post($value));
	}*/

	/**
	 * @since 4.0.0
	 * Function to display sub headings for the fields
	 */
	public function wt_sub_head($args,$base_id){
		extract($this->verify_the_fields($args));
		if(trim($heading_number) != ""){
			$heading_number = sprintf('<span style="background: #3157A6;color: #fff;border-radius: 25px;padding: 4px 9px;margin-right: 5px;">%1$s</span>',$heading_number);
		}
		if($col_3 !== ""){
			return sprintf('<tr><td style=""><div class="%1$s">%2$s %3$s</div></td><td></td><td>%4$s</td></tr>',esc_attr($class),$heading_number,wp_kses_post($label),$col_3);
		}
		return sprintf('<tr><td colspan="3" style=""><div class="%1$s">%2$s %3$s</div></td></tr>',esc_attr($class),$heading_number,wp_kses_post($label));
	}

	/**
	 * @since 4.0.0
	 * Function to display multi selected order status field
	 */
	public function order_multi_select_new($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = $result ? $result : array();

		$html = sprintf('<td>
			<input type="hidden" name="%1$s" value="1"/>
			<select class="wc-enhanced-select" id="%2$s" data-placeholder="%3$s" name="%4$s" multiple="multiple">',$name.'_hidden',
			esc_attr($id),
			esc_attr($placeholder),
			$name.'[]');
		foreach($checkbox_fields as $val_key => $val_label){
			$selected = in_array($val_key, $result) ? 'selected="selected"' : "";
			$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
				esc_attr($val_key),
				$selected,
				wp_kses_post($val_label));
		}
		$html .= sprintf('</select>%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display single checkbox field
     */
	public function wt_single_checkbox($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_string($result) ? stripslashes($result) : $result;
		$checkbox_label = "";
		if(is_array($checkbox_fields)){
			if(isset($checkbox_fields[$value])){
				$checkbox_label = $checkbox_fields[$value];
			}
		}

		$html = sprintf('<td><input type="checkbox" name="%5$s" value="%1$s" id="%2$s" class="%3$s %6$s" %7$s %4$s> <span class="option_label %9$s"> %8$s </span>',
			$value,
			esc_attr($id),
			esc_attr($class),
			checked($result,$value,false),
			$name,
			$form_toggler_p_class,
			$form_toggler_register,
			esc_html($checkbox_label),
			esc_attr($id).'_option_label'
		);
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display multi checkbox field
	 */
	public function wt_multi_checkbox($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_array($result) ? $result : array();
		$html = "<td>";
		foreach($checkbox_fields as $checkbox_key => $checkbox_label){
			$checked = in_array($checkbox_key, $result) ? 'checked' : "";
			$html .= sprintf('<input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="%4$s" %5$s> %6$s',
				$name.'[]',
				$name.'_'.$checkbox_key,
				esc_attr($class),
				esc_attr($checkbox_key),
				$checked,$checkbox_label);
			if("vertical_with_label" === $alignment){
				$html .= "<br><br>";
			}
		} 
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to display select2 dropdown multi checkbox field
	 */
	public function wt_select2_checkbox($args,$base_id)
	{	
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_array($result) ? $result : array();
		$html = "<td>";
		$html .= sprintf('<input type="hidden" name="%1$s_hidden" value="1">',$name);
		$html .= sprintf('<select class="wc-enhanced-select" id="%1$s" data-placeholder="%2$s" name="%3$s[]" multiple="multiple" %4$s>',
				esc_attr($id),
				esc_attr($placeholder),
				esc_attr($name),
				$attr
				);
		foreach($checkbox_fields as $checkbox_key => $checkbox_label){
			$selected = in_array($checkbox_key, $result) ? 'selected' : "";
			$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
					esc_attr($checkbox_key),
					esc_attr($selected),
					$checkbox_label
					);
		}
		$html .= '</select>';
		$html .= sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;

	}

	/**
	 * @since 4.0.0
	 * Function to display single checkbox field
	 */

	public function wt_radio( $args,$base_id ) {
		extract( $this->verify_the_fields( $args ) );
		$result			= Wf_Woocommerce_Packing_List::get_option( $name, $base_id );
		$result			= is_string( $result ) ? stripslashes( $result ) : $result;
		$td_sytle 		= "";
		$radio_opt_name = $name;

		if( "woocommerce_wf_generate_for_taxstatus" === $name ) {
			$radio_opt_name = "woocommerce_wf_generate_for_taxstatus[]";
		}

		$html	= sprintf( '<td style="%1$s">', $td_sytle );
		$i = 1;
		foreach( $radio_fields as $radio_key => $radio_label ) {
			$checked = "";
			if ( ( is_array( $result ) && in_array( $radio_key, $result ) ) || ( is_string( $result ) && $result == $radio_key ) ) {
				$checked	= 'checked';
			}

			$html	.= '<div style="display:flex;">';
			$html	.= sprintf(
				'<input type="radio" name="%1$s" id="%8$s" class="%3$s %4$s" %7$s value="%2$s" %5$s style="margin:0 .25rem 0 0;"> <p style="margin:-3px 5px 0;">%6$s</p>',
				$radio_opt_name,
				$radio_key,
				$class,
				$form_toggler_p_class,
				$checked,
				$radio_label,
				$form_toggler_register,
				$name.'_'.$radio_key
			);
			$html 	.= '</div>';

			if ( "vertical_with_label" === $alignment ) {
				$html	.= ($i !== count($radio_fields)) ?  "<br>" : '';
			} else {
				$html	.="&nbsp;&nbsp;";
			}
			$i++;
		}
		
		if ( "" !== trim( $end_col_call_back ) ) {
			$col3_data	= $this->{$end_col_call_back}($base_id,$module_base);
			$row_span	= 'rowspan="3" style="position:relative;"';
		} else {
			$col3_data	= "";
			$row_span	= '';
		}
		
		$html .= sprintf(
			'%1$s</td><td %3$s>%2$s</td>',
			$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),
			$col3_data,
			$row_span
		);
		return $html;
	}

	public function wt_select_dropdown($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

		$html = sprintf('<td><select name="%1$s" id="%1$s" class="%2$s %3$s" %4$s>',esc_attr($name),$class,$form_toggler_p_class,$form_toggler_register);
		foreach($select_dropdown_fields as $select_key => $select_label){
			$selected = ($select_key === $result) ? 'selected' : "";
			$disabled = "";
			if($select_key === "wfte_select_disabled_option"){
				$disabled = "disabled";
			}
			$html .= sprintf('<option value="%1$s" %2$s %4$s>%3$s</option>',
				esc_attr($select_key),
				$selected,
				$select_label,
				$disabled);		
		} 
		$html .=sprintf('</select>%1$s<td></td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_text($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

    	$html = sprintf('<td><input type="text" name="%5$s" id="%1$s" class="%2$s" value="%3$s" %6$s>%4$s<td><td></td>',esc_attr($id),
    		esc_attr($class),
    		esc_attr($result),
    		$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),
    		esc_attr($name),
    		esc_attr($attr));
		return $html;
	}

	public function wt_textarea($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;
    	$html = sprintf('<td><textarea name="%1$s" id="%2$s" class="%3$s" placeholder="%5$s">%4$s</textarea>',esc_attr($name),
    		esc_attr($id),
    		esc_attr($class),
    		$result,
    		esc_attr($placeholder));

    	$html .=sprintf('%1$s<td></td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_number($args,$base_id){
		extract($this->verify_the_fields($args));
		$result=Wf_Woocommerce_Packing_List::get_option($name,$base_id);
    	$result=is_string($result) ? stripslashes($result) : $result;

    	$html = sprintf('<td><input type="number" name="%5$s" id="%1$s" class="%2$s" value="%3$s" %6$s>%4$s<td><td></td>',
    		esc_attr($id),
    		esc_attr($class),
    		esc_attr($result),
    		$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field),
    		esc_attr($name),
    		$attr);
		return $html;
	}

	public function wt_additional_fields($args,$base_id){
		include WF_PKLIST_PLUGIN_PATH."admin/views/_custom_field_editor_form.php";
		extract($this->verify_the_fields($args));
		$fields=array();

        $add_data_flds=Wf_Woocommerce_Packing_List::$default_additional_data_fields; 
        $user_created=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');		            
        $result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;

        if(is_array($user_created))  //user created
        {
            $fields=array_merge($add_data_flds,$user_created);
        }else
        {
            $fields=$add_data_flds; //default
        }
        
    	$user_selected_arr = $result && is_array($result) ? $result : array();

    	// merge all the vat meta key to vat , label to VAT.
    	$vat_fields = array('vat','vat_number','eu_vat_number');
    	$temp = array();
    	foreach($user_selected_arr as $user_val){
    		if(in_array($user_val,$vat_fields)){
    			if(!in_array('vat',$temp)){
    				$temp[] = 'vat';
    			}
    		}else{
    			$temp[] = $user_val;
    		}
    	}
    	$user_selected_arr = $temp;

    	$d_temp = array();
    	foreach($fields as $d_key => $d_val){
    		if(in_array($d_key,$vat_fields)){
    			if(!array_key_exists('vat',$d_temp)){
    				$d_temp[$d_key] = 'VAT';
    			}
    		}else{
    			$d_temp[$d_key] = $d_val;
    		}
    	}
    	$wt_fields = $d_temp;

		/**
		 * @since 4.1.0 - [Tweak] - Remove the default order fields from the settings page and moved them to the customizer
		 */
		$unset_keys = array('contact_number','email','ssn','vat','vat_number','eu_vat_number','cus_note','aelia_vat');
		foreach($unset_keys as $unset_key){
			if(isset($wt_fields[$unset_key])){
				unset($wt_fields[$unset_key]);
			}
		}

    	$html = sprintf('<td>
    		<div class="wf_select_multi">
    		<input type="hidden" name="wf_%1$s_contactno_email_hidden" value="1" />
    		<select class="wc-enhanced-select" name="wf_%1$s_contactno_email[]" multiple="multiple">',$module_base);
				foreach ($wt_fields as $wt_fields_key => $wt_field_name) 
	            { 
	                $meta_key_display=Wf_Woocommerce_Packing_List::get_display_key($wt_fields_key);
	                $selected = in_array($wt_fields_key, $user_selected_arr) ? 'selected' : '';
	                $html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
	                	$wt_fields_key,
	                	$selected,
	                	$wt_field_name.$meta_key_display);
	            }
	            $html .=sprintf('</select>
	            	<br>
	            	<button type="button" class="button button-secondary" data-wf_popover="1" data-title="%1$s" data-module-base="%2$s" data-content-container=".wt_pklist_custom_field_form" data-field-type="order_meta" style="margin-top:5px; margin-left:5px; float: right;">%3$s</button>
	            	</div>%4$s</td>
	            	<td></td>',
	            	__("Order Meta","print-invoices-packing-slip-labels-for-woocommerce"),
	            	esc_attr($module_base),
	            	__("Add/Edit Order Meta Field","print-invoices-packing-slip-labels-for-woocommerce"),
	            	$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_uploader($args,$base_id){
		$wf_admin_img_path=WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;
		$img_url = $result ? $result : $wf_admin_img_path;
		$html = sprintf('<td>
							<input id="%1$s" type="hidden" name="%2$s" value="%3$s">
							<div class="wf_file_attacher_dv">
								<div class="wf_file_attacher_inner_dv">
									<span class="dashicons dashicons-dismiss wt_logo_dismiss"></span>
									<img class="wf_image_preview_small" src="%4$s">
								</div>
								<p>%5$s</p>
								<span class="size_rec">%6$s</span>
								<input type="button" name="upload_image" class="wf_button button button-primary wf_file_attacher" wf_file_attacher_target="#%1$s" value="Upload">
							</div>',
			esc_attr($id),
			esc_attr($name),
			esc_url($result),
			esc_url($img_url),
			__("Upload your image","print-invoices-packing-slip-labels-for-woocommerce"),
			__("Recommended size is 150x50px.","print-invoices-packing-slip-labels-for-woocommerce"));
		$html .=sprintf('%1$s</td><td></td>',$this->wt_add_help_text($help_text,$conditional_help_html,$after_form_field));
		return $html;
	}

	public function wt_add_help_text($help_text,$conditional_help_html,$after_form_field){
		$html = "";
		if(trim($after_form_field) != ""){
			$html .= $after_form_field;
		}
		if(trim($help_text) != ""){
			$html .= sprintf('<span class="wf_form_help">%1$s</span>',wp_kses_post($help_text));
		}
		if(trim($conditional_help_html) != ""){
			$html .= $conditional_help_html;
		}
		return $html;
	}

	public function wt_invoice_start_number_text_input($args,$base_id){
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result=is_string($result) ? stripslashes($result) : $result;

		$current_inv_no = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$base_id);
		$html = sprintf('<td>
				<div class="wf-form-group">
					<input type="number" min="1" step="1" readonly style="%4$s" name="%1$s" value="%2$s" class="invoice_preview_assert" id="invoice_start_number">
					<input style="float: right;" id="reset_invoice_button" type="button"  class="button button-primary" value="%3$s"/>
				</div>
				<input type="hidden" class="wf_current_invoice_number" value="%5$s" name="woocommerce_wf_Current_Invoice_number" class="invoice_preview_assert">
			</td><td rowspan="2" id="invoice_number_prev_div"></td>',
			$name,
			$result,
			__("Reset","print-invoices-packing-slip-labels-for-woocommerce"),
			"background:#eee; float:left;width:60%;",
			$current_inv_no);
		return $html;
	}

	public function wt_wc_country_dropdown($args,$base_id){ 
		extract($this->verify_the_fields($args));
		$result = Wf_Woocommerce_Packing_List::get_option($name,$base_id);
		$result = is_string($result) ? stripslashes($result) : $result;
        if( strstr( $result, ':' ))
        {
			$result = explode( ':', $result );
			$country         = current( $result );
			$state           = end( $result );                                            
		}else 
		{
			$country = $result;
			$state   = '*';
		}

		
		$coutries_list = $this->wt_get_country($country,$state);
		$country_options = sprintf('<option value="%1$s"></option>%2$s',
			__("Select country","print-invoices-packing-slip-labels-for-woocommerce"),
			$coutries_list
		);
		
		$html = sprintf('<td>
				<select name="%1$s" placeholder="%2$s" %3$s>
				%4$s
				</select>
			</td>',
			esc_attr($name),
			esc_attr($placeholder),
			esc_attr($attr),
			$country_options);
		return $html;
	}

	public function invoice_number_preview($base_id,$template_type){
		ob_start();
		include WF_PKLIST_PLUGIN_PATH.'public/modules/invoice/views/invoice_number_preview.php';
		$html=ob_get_clean();
		return $html;
	}

	public function wt_get_country($country,$state){
		ob_start();
		WC()->countries->country_dropdown_options($country,$state);
		$html=ob_get_clean();
		return $html;
	}

	public function wt_temp_file_path($args,$base_id){
		$file_path = Wf_Woocommerce_Packing_List::get_temp_dir('path');
		$html = sprintf('<td>%1$s</td></td></td>',esc_html($file_path));
		return $html;
	}

	public function wt_temp_file_total($args,$base_id){
		$total_temp_files	= Wf_Woocommerce_Packing_List_Admin::get_total_temp_files(true);
		$html	= '<td>';
		$html .='<table class="wt_pklist_temp_table">
					<thead>
						<tr>
							<th>'.__("Document","print-invoices-packing-slip-labels-for-woocommerce").'</th>
							<th>'.__("PDF","print-invoices-packing-slip-labels-for-woocommerce").'</th>
							<th>'.__("HTML","print-invoices-packing-slip-labels-for-woocommerce").'</th>
							<th>'.__("Action","print-invoices-packing-slip-labels-for-woocommerce").'</th>
						</tr>
					</thead>
					<tbody class="wt_pklist_temp_table_body">';
		if(0 === $total_temp_files['total_files']['total_file_count']){
			$html .='<tr><td colspan="4">'.__("No temporary file found","print-invoices-packing-slip-labels-for-woocommerce").'</td></tr>';
		}else{
			foreach($total_temp_files as $doc_key => $doc_data){
				if("total_files" !== $doc_key && false === apply_filters('wt_pklist_show_document_temp_files_'.$doc_key,false,$doc_key)){
					continue;
				}
				if($doc_data['total_file_count'] > 0){
					$html .= '<tr>
						<td>'.esc_html($doc_data["label"]).'</td>
						<td>'.esc_html($doc_data["pdf"]).'</td>
						<td>'.esc_html($doc_data["html"]).'</td>
						<td class="action">
							<a class="wt_pklist_temp_files_btn" data-action="delete_all_temp" data-document="'.esc_attr($doc_key).'"><span class="dashicons dashicons-trash"></span></a>
							<a class="wt_pklist_temp_files_btn" data-action="download_all_temp" data-document="'.esc_attr($doc_key).'"><span class="dashicons dashicons-download"></span></a>
						</td>
					</tr>';
				}
			}
		}
		$html 	.='</tbody></table>';
		$html  	.= '</td>';
		return $html;
	}
	
	public function invoice_number_format($args,$base_id){
		$template_type = Wf_Woocommerce_Packing_List::get_module_base($base_id);
		$query = new WC_Order_Query( array(
			'limit' => 1,
			'orderby' => 'date',
			'order' => 'DESC',
			'parent'=>0,
		) );
		
		$orders = $query->get_orders();
		$order_number = "123";
		if(count($orders)>0)
		{
			$order=$orders[0];
			$order_number=$order->get_order_number();
		}

		$current_invoice_number =(int) Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_Current_Invoice_number',$base_id);
		$current_invoice_number_in_db = $current_invoice_number=($current_invoice_number<0 ? 0 : $current_invoice_number);
		
		$inv_num=++$current_invoice_number;
		$use_wc_order_number = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$base_id);

		$attach_invoice = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_add_invoice_in_customer_mail',$base_id);
		$invoice_no_type = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_as_ordernumber',$base_id);
		$invoice_no_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_format',$base_id);
		$prefix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_prefix',$base_id);
		$suffix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_number_postfix',$base_id);
		$invoice_start_number = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_start_number',$base_id);
		$invoice_no_length = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_invoice_padding_number',$base_id);
		$order_no_selected =  "Yes" === $invoice_no_type ? 'selected' : '';
		$custom_no_selected =  "No" === $invoice_no_type ? 'selected' : '';

		$preview_invoice_number_text_display = ("No" === $use_wc_order_number) ? "display: none;" : '';
		$preview_invoice_number_text_custom_display = ("Yes" === $use_wc_order_number) ? "display: none;" : '';
		$date_frmt_tooltip=__('Click to append with existing data','print-invoices-packing-slip-labels-for-woocommerce');
		$input_checkbox = '';
		$popup_title = __("Add date to invoice number","print-invoices-packing-slip-labels-for-woocommerce");
		$setting_title = __("Invoice number format","print-invoices-packing-slip-labels-for-woocommerce");
		$starting_no_label = __("What should be the starting number for your invoices?","print-invoices-packing-slip-labels-for-woocommerce");
		$num_length_label = __("What length would you prefer for your invoice","print-invoices-packing-slip-labels-for-woocommerce");
		if(isset($_GET['page']) && ("wf_woocommerce_packing_list_invoice" === $_GET['page'] || "wf_woocommerce_packing_list_creditnote" === $_GET['page']))
		{
			if(is_plugin_active('wt-woocommerce-invoice-addon/wt-woocommerce-invoice-addon.php')){
				$input_checkbox = '<p style="text-align:left; max-width:400px; margin-top:0px;">
				<input type="checkbox" name="wf_inv_num_frmt_data_val_pdf_fw" id="wf_inv_num_frmt_order_date" value="order_date"> <label for="wf_inv_num_frmt_order_date">'.__("Use order date as input instead.","print-invoices-packing-slip-labels-for-woocommerce").'</label>
				</p>';
			}

			if("wf_woocommerce_packing_list_creditnote" === $_GET['page'])
			{
				$popup_title = __("Add date to credit note number","print-invoices-packing-slip-labels-for-woocommerce");
				$setting_title = __("Creditnote number format","print-invoices-packing-slip-labels-for-woocommerce");
				$starting_no_label = __("What should be the starting number for your credit notes?","print-invoices-packing-slip-labels-for-woocommerce");
				$num_length_label = __("What length would you prefer for your credit note","print-invoices-packing-slip-labels-for-woocommerce");
			}
		}
		elseif(isset($_GET['page']) && "wf_woocommerce_packing_list_proformainvoice" === $_GET['page'])
		{
			if(is_plugin_active('wt-woocommerce-proforma-addon/wt-woocommerce-proforma-addon.php')){
				$input_checkbox = '<p style="text-align:left; max-width:400px; margin-top:0px;">
				<input type="checkbox" name="wf_inv_num_frmt_data_val_pdf_fw" id="wf_inv_num_frmt_order_date" value="order_date"> <label for="wf_inv_num_frmt_order_date">'.__("Use order date as input instead.","print-invoices-packing-slip-labels-for-woocommerce").'</label>
				</p>';
			}
			$popup_title = __("Add date to proforma invoice number","print-invoices-packing-slip-labels-for-woocommerce");
			$setting_title = __("Proforma invoice number format","print-invoices-packing-slip-labels-for-woocommerce");
			$starting_no_label = __("What should be the starting number for your proforma invoices?","print-invoices-packing-slip-labels-for-woocommerce");
			$num_length_label = __("What length would you prefer for your proforma invoice","print-invoices-packing-slip-labels-for-woocommerce");
		}

		$html = '<tr valign="top">
			<th>'.esc_html($setting_title).'</th>
			<td colspan="2">
				<table style="margin-top:-14px;width:100%;">
					<tbody>
					<tr>
						<td style="width:35%;">
							<input type="hidden" id="woocommerce_wf_invoice_number_format" name="woocommerce_wf_invoice_number_format_pdf_fw" value="'.esc_attr($invoice_no_format).'">
							<div class="choose_date_div">
								<input type="text" name="woocommerce_wf_invoice_number_prefix_pdf_fw" placeholder="'.__("Prefix","print-invoices-packing-slip-labels-for-woocommerce").'" value="'.esc_attr($prefix).'" class="reset_invoice_check_fields" data-field-type="prefix" data-warning-message="'.__('Removing year [Y] from the prefix will disable the annual reset functionality','print-invoices-packing-slip-labels-for-woocommerce').'" autocomplete="off">
								<img class="choose_date_img" data-target-id="woocommerce_wf_invoice_number_prefix_pdf_fw" src="'.esc_url(WF_PKLIST_PLUGIN_URL . "admin/images/choose_date.png").'">
								<a class="choose_date_drop_down" data-target-id="woocommerce_wf_invoice_number_prefix_pdf_fw">'.__("Choose date","print-invoices-packing-slip-labels-for-woocommerce").'</a>
							</div>
						</td>
						<td style="width:25%;position:relative;">
							<select name="woocommerce_wf_invoice_as_ordernumber_pdf_fw" class="reset_invoice_check_fields" data-field-type="number_type" data-warning-message="'.__('Switching to order number will disable the annual reset functionality','print-invoices-packing-slip-labels-for-woocommerce').'">
								<option value="Yes" '.esc_attr($order_no_selected).'>'.__("Order number","print-invoices-packing-slip-labels-for-woocommerce").'</option>
								<option value="No" '.esc_attr($custom_no_selected).'>'.__("Custom number","print-invoices-packing-slip-labels-for-woocommerce").'</option>
							</select>
						</td>
						<td style="width:35%;">
							<div class="choose_date_div">
								<input type="text" name="woocommerce_wf_invoice_number_postfix_pdf_fw" placeholder="'.__("Suffix","print-invoices-packing-slip-labels-for-woocommerce").'" value="'.esc_attr($suffix).'" class="reset_invoice_check_fields" data-field-type="suffix" data-warning-message="'.__('Removing year [Y] from the suffix will disable the annual reset functionality','print-invoices-packing-slip-labels-for-woocommerce').'" autocomplete="off">
								<img class="choose_date_img" data-target-id="woocommerce_wf_invoice_number_postfix_pdf_fw" src="'.esc_url(WF_PKLIST_PLUGIN_URL . "admin/images/choose_date.png").'">
								<a class="choose_date_drop_down" data-target-id="woocommerce_wf_invoice_number_postfix_pdf_fw">'.__("Choose date","print-invoices-packing-slip-labels-for-woocommerce").'</a>
							</div>	
						</td>
					</tr>
					<tr class="wc_custom_no_div">
						<td colspan="3">
							<p>'.esc_html($starting_no_label).'</p>
						</td>
					</tr>
					<tr class="wc_custom_no_div">
						<td style="position:relative;">
							<input type="number" name="woocommerce_wf_invoice_start_number_preview_pdf_fw" value="'.esc_attr($invoice_start_number).'" min="0" style="width:40%;" class="reset_invoice_check_fields" data-field-type="start_number" data-warning-message="'.__('The latest specified starting number will be used after reset','print-invoices-packing-slip-labels-for-woocommerce').'">
							<input type="hidden" name="woocommerce_wf_invoice_start_number_pdf_fw" value="'.esc_attr($invoice_start_number).'" min="0" style="width:40%;">
							<input type="hidden" class="wf_current_invoice_number_pdf_fw" value="'. esc_attr($current_invoice_number_in_db) .'" name="woocommerce_wf_Current_Invoice_number_pdf_fw" class="">
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<p>'.esc_html($num_length_label).'</p>
						</td>
					</tr>
					<tr>
						<td>
						<input type="number" name="woocommerce_wf_invoice_padding_number_pdf_fw" value="'.esc_attr($invoice_no_length).'" min="0" style="width:40%;">
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" value="'.esc_attr($order_number).'" id="sample_invoice_number_pdf_fw">
							<input type="hidden" id="sample_current_invoice_number_pdf_fw" value="'.esc_attr($current_invoice_number).'">
							<div id="invoice_number_prev_div" style="width: auto;border: 1px solid #dadadc;padding: 5px 12px;border-radius: 5px;display: inline-block;background: #f0f0f1;margin-top:25px;">
								<p style="font-weight: bold;line-height: 0;">
									 '.__("PREVIEW","print-invoices-packing-slip-labels-for-woocommerce").'
								</p>
								<p style="margin: 1em  0 0.5em 0;'.$preview_invoice_number_text_display.'" id="preview_invoice_number_text">
									'.__("If the order number is","print-invoices-packing-slip-labels-for-woocommerce").' '.$order_number.', 
									<br> 
									'.sprintf(__("the %s number would be",'print-invoices-packing-slip-labels-for-woocommerce'),$template_type).'
								</p>
								<p style="margin: 1em  0 0.5em 0;'.$preview_invoice_number_text_custom_display.'" id="preview_invoice_number_text_custom">
									'.sprintf(__('Your next %s number would be','print-invoices-packing-slip-labels-for-woocommerce'),$template_type).'
								</p>
								<span id="preview_invoice_number_pdf_fw" style="background: #ffffff;padding: 5px;color: #3c434a;border-radius: 3px;float: left;font-weight: bold;margin-bottom: 0.5em;"></span>    
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>';
		
		$html .='<tr valign="top">
			
			<td>
				<div class="wf_inv_num_frmt_hlp_fw wf_pklist_popup" style="width: 450px;">
					<div class="wf_pklist_popup_hd">
						<span class="popup_title">'.$popup_title.'</span>
						<div class="wf_pklist_popup_close"><span class="dashicons dashicons-dismiss"></span></div>
					</div>
					<div class="wf_pklist_popup_body">
						'.$input_checkbox.'
						<table class="wp-list-table widefat choose_date_table">
							<thead>
								<tr>
									<th style="width:40%;">'.__("Date format strings",'print-invoices-packing-slip-labels-for-woocommerce').'</th><th>'.__("Output",'print-invoices-packing-slip-labels-for-woocommerce').'</th><th></th>
								</tr>
							</thead>
							<tbody>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[F]</a></td>
									<td class="date_format_text">'.date('F').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[dS]</a></td>
									<td class="date_format_text">'.date('dS').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[M]</a></td>
									<td class="date_format_text">'.date('M').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[m]</a></td>
									<td class="date_format_text">'.date('m').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[d]</a></td>
									<td class="date_format_text">'.date('d').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[D]</a></td>
									<td class="date_format_text">'.date('D').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[y]</a></td>
									<td class="date_format_text">'.date('y').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[Y]</a></td>
									<td class="date_format_text">'.date('Y').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[d/m/y]</a></td>
									<td class="date_format_text">'.date('d/m/y').'</td>
									<td class="date_format_add"></td>
								</tr>
								<tr class="wf_inv_num_frmt_fw_append_btn_tr">
									<td><a class="wf_inv_num_frmt_fw_append_btn" title="'.$date_frmt_tooltip.'">[d-m-Y]</a></td>
									<td class="date_format_text">'.date('d-m-Y').'</td>
									<td class="date_format_add"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>';
		return $html;
	}

	/**
	 * @since 4.0.0
	 * Function to verify the arguments before displaying the fields
	 */
	public function verify_the_fields($args){
		$args['id'] 	= isset($args['id']) ? $args['id'] : "";
		$args['name'] 	= isset($args['name']) ? $args['name'] : "";
		$args['class'] 	= isset($args['class']) ? $args['class'] : "";
		$args['label'] 	= isset($args['label']) ? $args['label'] : "";
		$args['col'] 	= isset($args['col']) ? (int)$args['col'] : 3;
		$args['col_3']	= isset($args['col_3']) ? $args['col_3'] : "";

		$args['value'] 	= isset($args['value']) ? $args['value'] : '';
		$args['attr']	=	(isset($args['attr']) ? $args['attr'] : '');
		$args['tooltip']	=	(boolean) (isset($args['tooltip']) ? $args['tooltip'] : false);
		$args['help_text'] 	= 	isset($args['help_text']) ? $args['help_text'] : '';
		$args['mandatory']	=	(boolean) (isset($args['mandatory']) ? $args['mandatory'] : false);
		$args['placeholder'] = 	isset($args['placeholder']) ? $args['placeholder'] : "";
		$args['after_form_field']	=	(isset($args['after_form_field']) ? $args['after_form_field'] : ''); 
		$args['before_form_field']	=	(isset($args['before_form_field']) ? $args['before_form_field'] : '');
		
		$args['select_dropdown_fields'] = isset($args['select_dropdown_fields']) ? $args['select_dropdown_fields'] : array();
		$args['checkbox_fields'] = isset($args['checkbox_fields']) ? $args['checkbox_fields'] : array();
		$args['radio_fields'] = isset($args['radio_fields']) ? $args['radio_fields'] : array();
		$args['alignment'] = isset($args['alignment']) ? $args['alignment'] : "";
		$args['module_base'] = isset($args['module_base']) ? $args['module_base'] : "";
		$args['heading_number'] = isset($args['heading_number']) ? $args['heading_number'] : "";
		$args['tr_id'] = isset($args['tr_id']) ? $args['tr_id'] : "";
		
		$args['end_col_call_back'] =  isset($args['end_col_call_back']) ? $args['end_col_call_back'] : "";

		$args['conditional_help_html']='';
		if(isset($args['help_text_conditional']) && is_array($args['help_text_conditional']))
		{		
			foreach ($args['help_text_conditional'] as $help_text_config)
			{
				if(is_array($help_text_config))
				{
					$condition_attr='';
					if(is_array($help_text_config['condition']))
					{
						$previous_type=''; /* this for avoiding fields without glue */
						foreach ($help_text_config['condition'] as $condition)
						{
							if(is_array($condition))
							{
								if($previous_type!='field')
								{
									$condition_attr.='['.$condition['field'].'='.$condition['value'].']';
									$previous_type='field';
								}
							}else
							{
								if(is_string($condition))
								{
									$condition=strtoupper($condition);
									if(($condition=='AND' || $condition=='OR') && $previous_type!='glue')
									{
										$condition_attr.='['.$condition.']';
										$previous_type='glue';
									}
								}
							}
						}
					}			
					$args['conditional_help_html'].='<span class="wf_form_help wt_pklist_conditional_help_text" data-wt_pklist-help-condition="'.esc_attr($condition_attr).'">'.$help_text_config['help_text'].'</span>';
				}	
			}
		}
		$args['form_toggler_p_class']="";
		$args['form_toggler_register']="";
		$args['form_toggler_child']="";
		if(isset($args['form_toggler']))
		{
			if($args['form_toggler']['type']=='parent')
			{
				$args['form_toggler_p_class']="wf_form_toggle";
				$args['form_toggler_register']=' wf_frm_tgl-target="'.$args['form_toggler']['target'].'"';
			}
			elseif($args['form_toggler']['type']=='child')
			{
				$args['form_toggler_child']=' wf_frm_tgl-id="'.$args['form_toggler']['id'].'" wf_frm_tgl-val="'.$args['form_toggler']['val'].'" '.(isset($args['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$args['form_toggler']['chk'].'"' : '').(isset($args['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$args['form_toggler']['lvl'].'"' : '');	
			}else
			{
				$args['form_toggler_child']=' wf_frm_tgl-id="'.$args['form_toggler']['id'].'" wf_frm_tgl-val="'.$args['form_toggler']['val'].'" '.(isset($args['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$args['form_toggler']['chk'].'"' : '').(isset($args['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$args['form_toggler']['lvl'].'"' : '');	
				$args['form_toggler_p_class']="wf_form_toggle";
				$args['form_toggler_register']=' wf_frm_tgl-target="'.$args['form_toggler']['target'].'"';				
			}
			
		}

		if($args['mandatory'])
		{
			$args['attr'].=' required="required"';	
		}
		return $args;
	}
}
?>