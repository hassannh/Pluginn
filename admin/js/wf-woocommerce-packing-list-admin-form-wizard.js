(function( $ ) {
	'use strict';
	$(function() {
        var wt_pklist_setup_wizard = {
            Set:function(){
                wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                wt_pklist_setup_wizard.reset_invoice_number_option_check();
                $('.wt_form_wizard_next').on('click',function(){
                    var current_step_id = $(this).attr('data-wizard-step');
                    var next_step_id    = $(this).attr('data-wizard-next-step');
                    var next_step_div   = $(this).attr('data-target-class');

                    $('.'+next_step_div).hide();
                    $('.'+next_step_div+'[data-wizard-step="'+next_step_id+'"]').show();
                    $('.wt_form_wizard_progress_bar li').removeClass('stop_active');
                    $('.wt_form_wizard_progress_bar li.wt_form_wizard_progress_step_'+current_step_id).addClass('step_active');
                    $('.wt_form_wizard_progress_bar li.wt_form_wizard_progress_step_'+next_step_id).addClass('step_active stop_active');
                });

                $('.wt_form_wizard_prev').on('click',function(){
                    var current_step_id = $(this).attr('data-wizard-step');
                    var prev_step_id    = $(this).attr('data-wizard-prev-step');
                    var prev_step_div   = $(this).attr('data-target-class');

                    $('.'+prev_step_div).hide();
                    $('.'+prev_step_div+'[data-wizard-step="'+prev_step_id+'"]').show();
                    $('.wt_form_wizard_progress_bar li').removeClass('stop_active');
                    $('.wt_form_wizard_progress_bar li.wt_form_wizard_progress_step_'+current_step_id).removeClass('step_active');
                    $('.wt_form_wizard_progress_bar li.wt_form_wizard_progress_step_'+prev_step_id).addClass('step_active stop_active');
                });

                $('input[name="woocommerce_wf_invoice_number_prefix_pdf_fw"]').on('focus input keyup change',function(){
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });
                $('input[name="woocommerce_wf_invoice_number_postfix_pdf_fw"]').on('focus input keyup change',function(){
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });

                $('.choose_date_img').on('click',function(){
                    var target_id   = $(this).attr('data-target-id');
                    $('.choose_date_drop_down').hide();
                    $('.choose_date_drop_down[data-target-id="'+target_id+'"]').show();
                });

                $('.choose_date_drop_down').on('click',function(){
                    var trgt_field  = $(this).attr('data-target-id');
                    $('.wf_inv_num_frmt_hlp_fw').attr('data-wf-trget',trgt_field);
                    wf_popup.showPopup($('.wf_inv_num_frmt_hlp_fw'));
                    $(this).hide();
                });

                $('[name="woocommerce_wf_invoice_as_ordernumber_pdf_fw"]').on('change',function(){
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });
                
                $('.wf_inv_num_frmt_fw_append_btn_tr').on('mouseover',function(){
                    var add_text_html   = '<span class="dashicons dashicons-plus"></span>'+wf_pklist_params.msgs.add_date_string_text;
                    $('.choose_date_table').children().find('.date_format_add').html('');
                    $(this).children('.date_format_add').html(add_text_html);
                });

                $('.wf_inv_num_frmt_fw_append_btn_tr').on('click', function(){
                    var trgt_elm_name   = $(this).parents('.wf_inv_num_frmt_hlp_fw').attr('data-wf-trget');
                    var trgt_elm        = $('[name="'+trgt_elm_name+'"]');
                    var exst_vl         = trgt_elm.val();
                    var cr_vl           = $(this).children('td').children('.wf_inv_num_frmt_fw_append_btn').text();
                    
                    if ( $('[name="wf_inv_num_frmt_data_val_pdf_fw"]:checked').length > 0 ) {
                        var data_val    = $('[name="wf_inv_num_frmt_data_val_pdf_fw"]:checked').val();
                        const regex     = /\[(.*?)\]/gm;
                        cr_vl           = cr_vl.replace(regex, "[$1 data-val='"+data_val+"']");
                    }

                    trgt_elm.val(exst_vl+cr_vl);
                    wf_popup.hidePopup();
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });

                $('[name="woocommerce_wf_invoice_start_number_preview_pdf_fw"]').on('focus input keyup change',function(){
                    $('[name="woocommerce_wf_invoice_start_number_pdf_fw"]').val($(this).val());
                    $("#sample_current_invoice_number_pdf_fw").val($(this).val());
                    $(".wf_current_invoice_number_pdf_fw").val($(this).val()-1);
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });

                $('[name="woocommerce_wf_invoice_padding_number_pdf_fw"]').on('focus input keyup change',function(){
                    wt_pklist_setup_wizard.get_invoice_no_format();
                    wt_pklist_setup_wizard.wf_do_invoice_number_preview();
                    wt_pklist_setup_wizard.reset_invoice_number_option_check();
                });

                $('.wt_form_wizard_submit').on('click',function(){
                    wt_pklist_setup_wizard.wt_wrap_wizard_form_submit('wt_form_wizard_submit');
                });

                var timer;
                $('.reset_invoice_check_fields').on('input change',function(){
                    var warn_set    = $(this).attr('data-warn-set');
                    var field_type  = $(this).attr('data-field-type');
                    var warn_msg    = $(this).attr('data-warning-message');
                    var prefix_val  = $('[name="woocommerce_wf_invoice_number_prefix_pdf_fw"]').val();
                    var postfix_val = $('[name="woocommerce_wf_invoice_number_postfix_pdf_fw"]').val();
                    var num_type    = $('[name="woocommerce_wf_invoice_as_ordernumber_pdf_fw"]').val();
                    
                    if( 'undefined' === typeof warn_set && $("#wt_pklist_invoice_number_reset").is(':checked') ) {
                        var warn_div        = false;
                        var warn_msg_div    = '';

                        if ( field_type === 'prefix' || field_type === 'suffix' ) {
                            if ( -1 === prefix_val.indexOf( '[Y]' ) && -1 === postfix_val.indexOf( '[Y]' ) ){
                                warn_div        = true;
                                warn_msg_div    = '<span style="padding:3px 5px 0 0;"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 15.4995C9.48336 15.4995 10.9334 15.0596 12.1668 14.2355C13.4001 13.4114 14.3614 12.2401 14.9291 10.8696C15.4968 9.49919 15.6453 7.99119 15.3559 6.53633C15.0665 5.08148 14.3522 3.7451 13.3033 2.69621C12.2544 1.64732 10.918 0.933011 9.46317 0.643621C8.00832 0.354232 6.50032 0.502757 5.12987 1.07041C3.75942 1.63807 2.58809 2.59937 1.76398 3.83274C0.939865 5.0661 0.499997 6.51615 0.499997 7.99951C0.502209 9.98796 1.29309 11.8943 2.69914 13.3004C4.10518 14.7064 6.01155 15.4973 8 15.4995ZM8 1.99951C9.18668 1.99951 10.3467 2.35141 11.3334 3.01069C12.3201 3.66998 13.0891 4.60705 13.5433 5.70341C13.9974 6.79977 14.1162 8.00617 13.8847 9.17005C13.6532 10.3339 13.0818 11.403 12.2426 12.2422C11.4035 13.0813 10.3344 13.6527 9.17054 13.8842C8.00665 14.1157 6.80025 13.9969 5.7039 13.5428C4.60754 13.0887 3.67047 12.3196 3.01118 11.3329C2.35189 10.3462 2 9.1862 2 7.99951C2.00182 6.40877 2.63454 4.88371 3.75937 3.75888C4.88419 2.63406 6.40926 2.00133 8 1.99951ZM8 8.37451C8.19891 8.37451 8.38967 8.29549 8.53033 8.15484C8.67098 8.01419 8.75 7.82342 8.75 7.62451V5.37451C8.75 5.1756 8.67098 4.98483 8.53033 4.84418C8.38967 4.70353 8.19891 4.62451 8 4.62451C7.80108 4.62451 7.61032 4.70353 7.46967 4.84418C7.32901 4.98483 7.25 5.1756 7.25 5.37451V7.62451C7.25 7.82342 7.32901 8.01419 7.46967 8.15484C7.61032 8.29549 7.80108 8.37451 8 8.37451ZM8 11.3745C8.18542 11.3745 8.36667 11.3195 8.52084 11.2165C8.67501 11.1135 8.79518 10.9671 8.86613 10.7958C8.93709 10.6245 8.95566 10.436 8.91948 10.2541C8.88331 10.0723 8.79402 9.90521 8.66291 9.7741C8.5318 9.64299 8.36475 9.5537 8.18289 9.51753C8.00104 9.48135 7.81254 9.49992 7.64123 9.57088C7.46992 9.64183 7.32351 9.76199 7.22049 9.91616C7.11748 10.0703 7.0625 10.2516 7.0625 10.437C7.0625 10.6857 7.16127 10.9241 7.33708 11.0999C7.5129 11.2757 7.75136 11.3745 8 11.3745Z" fill="#D63638"/></svg></span> <span>' + warn_msg + '</span>';
                            }
                        } else if ( field_type === 'start_number' && ( -1 !== prefix_val.indexOf( '[Y]' ) || -1 !== postfix_val.indexOf( '[Y]' ) ) ) {
                            warn_div        = true;
                            warn_msg_div    = '<span style="padding:3px 5px 0 0;"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 0.499512C6.51664 0.499512 5.0666 0.939379 3.83323 1.76349C2.59986 2.5876 1.63856 3.75894 1.07091 5.12939C0.50325 6.49983 0.354725 8.00783 0.644114 9.46269C0.933503 10.9175 1.64781 12.2539 2.6967 13.3028C3.7456 14.3517 5.08197 15.066 6.53683 15.3554C7.99168 15.6448 9.49968 15.4963 10.8701 14.9286C12.2406 14.361 13.4119 13.3997 14.236 12.1663C15.0601 10.9329 15.5 9.48287 15.5 7.99951C15.4978 6.01107 14.7069 4.1047 13.3009 2.69865C11.8948 1.29261 9.98845 0.501723 8 0.499512ZM8 13.9995C6.81332 13.9995 5.65328 13.6476 4.66658 12.9883C3.67989 12.329 2.91085 11.392 2.45673 10.2956C2.0026 9.19925 1.88378 7.99286 2.11529 6.82897C2.3468 5.66508 2.91825 4.59599 3.75736 3.75687C4.59648 2.91776 5.66558 2.34631 6.82946 2.1148C7.99335 1.88329 9.19975 2.00211 10.2961 2.45623C11.3925 2.91036 12.3295 3.6794 12.9888 4.66609C13.6481 5.65279 14 6.81282 14 7.99951C13.9982 9.59025 13.3655 11.1153 12.2406 12.2401C11.1158 13.365 9.59074 13.9977 8 13.9995ZM8 7.62451C7.80109 7.62451 7.61033 7.70353 7.46967 7.84418C7.32902 7.98483 7.25 8.1756 7.25 8.37451V10.6245C7.25 10.8234 7.32902 11.0142 7.46967 11.1548C7.61033 11.2955 7.80109 11.3745 8 11.3745C8.19892 11.3745 8.38968 11.2955 8.53033 11.1548C8.67099 11.0142 8.75 10.8234 8.75 10.6245V8.37451C8.75 8.1756 8.67099 7.98483 8.53033 7.84418C8.38968 7.70353 8.19892 7.62451 8 7.62451ZM8 4.62451C7.81458 4.62451 7.63333 4.67949 7.47916 4.78251C7.32499 4.88552 7.20482 5.03194 7.13387 5.20325C7.06291 5.37455 7.04434 5.56305 7.08052 5.74491C7.11669 5.92677 7.20598 6.09381 7.33709 6.22492C7.4682 6.35604 7.63525 6.44532 7.81711 6.4815C7.99896 6.51767 8.18746 6.49911 8.35877 6.42815C8.53008 6.35719 8.67649 6.23703 8.77951 6.08286C8.88252 5.92869 8.9375 5.74743 8.9375 5.56201C8.9375 5.31337 8.83873 5.07491 8.66292 4.8991C8.4871 4.72328 8.24864 4.62451 8 4.62451Z" fill="#DBA617"/></svg></span> <span>' + warn_msg + '</span>';
                        } else if ( field_type === 'number_type' && ( -1 !== prefix_val.indexOf( '[Y]' ) || -1 !== postfix_val.indexOf( '[Y]' ) )) {
                            if("Yes" === num_type) {
                                warn_div        = true;
                                warn_msg_div    = '<span style="padding:3px 5px 0 0;"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 15.4995C9.48336 15.4995 10.9334 15.0596 12.1668 14.2355C13.4001 13.4114 14.3614 12.2401 14.9291 10.8696C15.4968 9.49919 15.6453 7.99119 15.3559 6.53633C15.0665 5.08148 14.3522 3.7451 13.3033 2.69621C12.2544 1.64732 10.918 0.933011 9.46317 0.643621C8.00832 0.354232 6.50032 0.502757 5.12987 1.07041C3.75942 1.63807 2.58809 2.59937 1.76398 3.83274C0.939865 5.0661 0.499997 6.51615 0.499997 7.99951C0.502209 9.98796 1.29309 11.8943 2.69914 13.3004C4.10518 14.7064 6.01155 15.4973 8 15.4995ZM8 1.99951C9.18668 1.99951 10.3467 2.35141 11.3334 3.01069C12.3201 3.66998 13.0891 4.60705 13.5433 5.70341C13.9974 6.79977 14.1162 8.00617 13.8847 9.17005C13.6532 10.3339 13.0818 11.403 12.2426 12.2422C11.4035 13.0813 10.3344 13.6527 9.17054 13.8842C8.00665 14.1157 6.80025 13.9969 5.7039 13.5428C4.60754 13.0887 3.67047 12.3196 3.01118 11.3329C2.35189 10.3462 2 9.1862 2 7.99951C2.00182 6.40877 2.63454 4.88371 3.75937 3.75888C4.88419 2.63406 6.40926 2.00133 8 1.99951ZM8 8.37451C8.19891 8.37451 8.38967 8.29549 8.53033 8.15484C8.67098 8.01419 8.75 7.82342 8.75 7.62451V5.37451C8.75 5.1756 8.67098 4.98483 8.53033 4.84418C8.38967 4.70353 8.19891 4.62451 8 4.62451C7.80108 4.62451 7.61032 4.70353 7.46967 4.84418C7.32901 4.98483 7.25 5.1756 7.25 5.37451V7.62451C7.25 7.82342 7.32901 8.01419 7.46967 8.15484C7.61032 8.29549 7.80108 8.37451 8 8.37451ZM8 11.3745C8.18542 11.3745 8.36667 11.3195 8.52084 11.2165C8.67501 11.1135 8.79518 10.9671 8.86613 10.7958C8.93709 10.6245 8.95566 10.436 8.91948 10.2541C8.88331 10.0723 8.79402 9.90521 8.66291 9.7741C8.5318 9.64299 8.36475 9.5537 8.18289 9.51753C8.00104 9.48135 7.81254 9.49992 7.64123 9.57088C7.46992 9.64183 7.32351 9.76199 7.22049 9.91616C7.11748 10.0703 7.0625 10.2516 7.0625 10.437C7.0625 10.6857 7.16127 10.9241 7.33708 11.0999C7.5129 11.2757 7.75136 11.3745 8 11.3745Z" fill="#D63638"/></svg></span> <span>' + warn_msg + '</span>';
                            }
                        }

                        if ( true === warn_div ) {
                            var htmlContent = '<div class="field_warn_outter"> <div class="field_warn_popup_arrow"></div> <div class="field_warn field_warn_popup" style="display:flex;">'+warn_msg_div+' </div></div>'; 
                            $(this).attr('data-warn-set','1');
                            $(this).next('.field_warn_outter').remove();
                            $(this).after(htmlContent);
                        }

                        // Clear the previous timer, if any
                        clearTimeout(timer);

                        // Set a new timer to remove the warning message after 5 seconds
                        timer = setTimeout(function() {
                            $('div.field_warn_outter').fadeOut(400, function() {
                                $(this).remove();
                            });
                        }, 3000);
                    } else {

                        if ( field_type === 'prefix' && -1 !== prefix_val.indexOf( '[Y]' )){
                            $(this).removeAttr('data-warn-set');
                        }else if (field_type === 'suffix' && -1 !== postfix_val.indexOf( '[Y]' )){
                            $(this).removeAttr('data-warn-set');
                        } else if ( field_type === 'number_type') {
                            if("No" === num_type) {
                                $(this).removeAttr('data-warn-set');
                            }
                        }

                        // Reset the timer if the user inputs a new value
                        clearTimeout(timer);

                        // Set a new timer to remove the warning message after 5 seconds
                        timer = setTimeout(function() {
                            $('div.field_warn_outter').fadeOut(400, function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            },
            
            get_invoice_no_format:function(){
                var invoice_no_format = '[number]';
                $('.choose_date_drop_down').hide();
                if("" !== $('input[name="woocommerce_wf_invoice_number_prefix_pdf_fw"]').val().trim()){
                    invoice_no_format = '[prefix]'+invoice_no_format;
                }

                if("" !== $('input[name="woocommerce_wf_invoice_number_postfix_pdf_fw"]').val().trim()){
                    invoice_no_format = invoice_no_format+'[suffix]';
                }

                $('input[name="woocommerce_wf_invoice_number_format_pdf_fw"]').val(invoice_no_format);
            },
            wf_do_invoice_number_preview:function(){
                if(0 < $("#sample_invoice_number_pdf_fw").length){
                    var invoice_no = $("#sample_invoice_number_pdf_fw").val();
                    var number_ref=$('[name="woocommerce_wf_invoice_as_ordernumber_pdf_fw"] option:selected').val();
                    var invoice_start_no = $('#sample_current_invoice_number_pdf_fw').val();
                    var number_format=$('[name="woocommerce_wf_invoice_number_format_pdf_fw"]').val();
                    var prefix_val =$('[name="woocommerce_wf_invoice_number_prefix_pdf_fw"]').val();
                    var postfix_val =$('[name="woocommerce_wf_invoice_number_postfix_pdf_fw"]').val();
                    var number_len = $('[name="woocommerce_wf_invoice_padding_number_pdf_fw"]').val();
                    
                    if("No" === number_ref){ // no means custom number
                        invoice_no = invoice_start_no;
                        $('.wc_custom_no_div').show();
                        $("#preview_invoice_number_text_custom").show();
                        $('#preview_invoice_number_text').hide();
                    }else{
                        $('.wc_custom_no_div').hide();
                        $('#preview_invoice_number_text').show();
                        $("#preview_invoice_number_text_custom").hide();
                    }
                    /* length change calculation */
                    var padded_invoice_number = "";
                    var invoice_no_length = invoice_no.length;
                    var padding_count = number_len - invoice_no_length;
                    if (padding_count > 0) {
                        for (var i = 0; i < padding_count; i++)
                        {
                            padded_invoice_number += '0';
                        }
                    }
                    invoice_no = padded_invoice_number+invoice_no;

                    if("[prefix][number][suffix]" === number_format){
                        invoice_no = prefix_val+invoice_no+postfix_val;
                    }else if("[prefix][number]" === number_format){
                        invoice_no = prefix_val+invoice_no;
                    }else if("[number][suffix]" === number_format){
                        invoice_no = invoice_no+postfix_val;
                    }
                    invoice_no = wt_pklist_setup_wizard.replace_date_val_invoice_number(invoice_no);

                    /* final preview */
                    $("#preview_invoice_number_pdf_fw").text(invoice_no);
                }
            },

            replace_date_val_invoice_number:function(invoice_no){
                invoice_no = invoice_no.replace(" data-val='order_date'","");
                const monthNames = ["January", "February", "March", "April", "May", "June",
                  "July", "August", "September", "October", "November", "December"
                ];
                const monthShortNamescaps = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                const daysShortNamescaps = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri",
                  "Sat"];
                var d = new Date();
                var full_year = d.getFullYear();
                var short_year = full_year.toString().substr(-2);
                invoice_no = invoice_no.replaceAll('[F]',monthNames[d.getMonth()]);
                invoice_no = invoice_no.replaceAll('[dS]',d.getDate()+'th');
                invoice_no = invoice_no.replaceAll('[M]',monthShortNamescaps[d.getMonth()]);
                invoice_no = invoice_no.replaceAll('[m]',("0" + (d.getMonth()+1)).slice(-2));
                invoice_no = invoice_no.replaceAll('[d]',("0" + d.getDate()).slice(-2));
                invoice_no = invoice_no.replaceAll('[y]',short_year);
                invoice_no = invoice_no.replaceAll('[Y]',full_year);
                invoice_no = invoice_no.replaceAll('[D]',daysShortNamescaps[d.getDay()]);
                invoice_no = invoice_no.replaceAll('[d/m/y]',("0" + d.getDate()).slice(-2)+'/'+("0" + (d.getMonth()+1)).slice(-2)+'/'+short_year);
                invoice_no = invoice_no.replaceAll('[d-m-Y]',("0" + d.getDate()).slice(-2)+'-'+("0" + (d.getMonth()+1)).slice(-2)+'-'+full_year);
                return invoice_no;
            },

            wt_wrap_wizard_form_submit:function(submit_btn_module){
                var invoice_skip = 0;
                if("wt_form_wizard_invoice_setup_skip" === submit_btn_module){
                    var invoice_skip = 1;
                }

                var elm = $('.wt_wrap_wizard_form');
                var data=elm.serialize();
                $.ajax({
                    url:wf_pklist_params.ajaxurl,
                    type:'POST',
                    dataType:'json',
                    data:data+'&action=wt_pklist_form_wizard_save&invoice_skip='+invoice_skip+'&_wpnonce='+wf_pklist_params.nonces.wf_packlist,
                    cache: false,
                    success:function(data)
				    {
                        if(true === data.status)
					    {
                            wf_popup.showPopup($('.wt_pklist_form_wizard_success'));

                            let url = window.location.href;    
                            if (url.indexOf('?') > -1){
                            url += '&complete_wizard=1'
                            } else {
                            url += '?complete_wizard=1'
                            }
                            
                            setTimeout(function () {
                                window.location.href = url; //will redirect to your blog page (an ex: blog.html)
                             }, 4000);
                        }
                    }
                });
            },

            reset_invoice_number_option_check:function() {
                if( $("#wt_pklist_invoice_number_reset").length <= 0 ){
                    return false;
                }
                var num_type = $('[name="woocommerce_wf_invoice_as_ordernumber_pdf_fw"]').val();
                if ( "No" === num_type ) {
                    $("#wt_pklist_invoice_number_reset").parents('tr').show();
                } else {
                    $("#wt_pklist_invoice_number_reset").parents('tr').hide();
                }
            }
        };
        wt_pklist_setup_wizard.Set();
    });

})( jQuery );