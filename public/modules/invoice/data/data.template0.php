<!-- DC ready -->
<style type="text/css">
body, html{font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;line-height: 1.5;}
.clearfix::after{ display:block; clear:both; content:""; }

.wfte_invoice-main{ color:#2A3646; font-size:12px; font-weight:400; box-sizing:border-box; width:100%; height:auto; }
.wfte_invoice-main *{ box-sizing:border-box;}
.template_footer{color:#2A3646; font-size:12px; font-weight:400; box-sizing:border-box; padding:30px 0px; height:auto;}
.template_footer *{ box-sizing:border-box;}


.wfte_row{ width:100%; display:block; }
.wfte_sub_row{ width:100%; display:block; }
.wfte_col-1{ width:100%; display:block;}
.wfte_col-2{ width:50%; display:block;}
.wfte_col-3{ width:33%; display:block;}
.wfte_col-4{ width:25%; display:block;}
.wfte_col-6{ width:30%; display:block;}
.wfte_col-7{ width:69%; display:block;}

.wfte_hr{ height:1px; font-size:0px; padding:0px; background:#cccccc; margin-bottom: 20px;}

.wfte_company_logo_img_box{ margin-bottom:10px; }
.wfte_company_logo_img{ width:150px; max-width:100%; }
.wfte_doc_title{ color:#263140; font-size:30px; font-weight:bold; height:auto; width:100%;line-height: 1;}
.wfte_company_name{ font-size:18px; font-weight:bold; }
.wfte_company_logo_extra_details{ font-size:12px; margin-top:3px;}
.wfte_invoice_data div span:first-child, .wfte_extra_fields span:first-child{ font-weight:bold; }
.wfte_invoice_data{ font-size:12px; }
.wfte_shipping_address{ width:95%;}
.wfte_billing_address{ width:95%; }
.wfte_address-field-header{ font-weight:bold; font-size:12px; color:#2A3646;}

.wfte_product_table{ width:100%; border-collapse:collapse; margin:0px; }
.wfte_payment_summary_table_body .wfte_right_column{ text-align:left; }
.wfte_payment_summary_table{ margin-bottom:10px; }
.wfte_table_head_color{ color:#2A3646; }

.wfte_product_table_head th{ height:36px; padding:0px 5px; font-size:.75rem; text-align:left; border-bottom:solid 1px #D7DBDF; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;}
.wfte_product_table_body td, .wfte_payment_summary_table_body td{ font-size:12px;}
.wfte_product_table_body td{ padding:10px 5px; border-bottom:solid 1px #dddee0;}
.wfte_product_table .wfte_right_column{ width:20%;}
.wfte_payment_summary_table .wfte_left_column{ width:60%; }
.wfte_product_table_body .product_td b{ font-weight:normal; }
.wfte_product_table_head_product{width: 30%;}

.wfte_payment_summary_table_body td{ border:none;}

.wfte_product_table_payment_total td{ font-size:13px; color:#2A3646; height:28px; border-top:solid 1px #D7DBDF;}
.wfte_product_table_payment_total td:nth-child(3){ font-weight:bold; }
.wfte_product_table_payment_total td.wfte_left_column{border: none;}

/* .product_td b{line-height: 15px;}  */

/* for mPdf */
.wfte_invoice_data{ border:solid 0px #fff; }
.wfte_invoice_data td, .wfte_extra_fields td{ font-size:12px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;}
.wfte_invoice_data tr td:nth-child(1), .wfte_extra_fields tr td:nth-child(1){ font-weight:bold; }

.wfte_signature{ width:100%; height:auto; min-height:60px;}
.wfte_signature_label{ font-size:12px; }
.wfte_image_signature_box{ display:inline-block;}
.wfte_return_policy{width:100%; height:auto; border-bottom:solid 1px #dfd5d5; margin-top:5px; }
.wfte_footer{width:100%; height:auto; margin-top:5px;font-size: 12px;}

.wfte_received_seal{ position:absolute; z-index:10; margin-top:90px; margin-left:0px; width:130px; border-radius:5px; font-size:22px; height:40px; border:solid 5px #00ccc5; color:#00ccc5; font-weight:900; text-align:center; transform:rotate(-45deg); opacity:.5; }

.float_left{ float:left; }
.float_right{ float:right; }

/* .wfte_product_table_category_row td{ padding:10px 5px;} */
table.wfte_product_table th, table.wfte_product_table td{ line-height: 1.6; padding:5px 5px;}
table.wfte_payment_summary_table th, table.wfte_payment_summary_table td{ line-height: 1.6; padding:5px 5px;}
</style>
<div class="wfte_rtl_main wfte_invoice-main wfte_invoice_basic_main" style="padding-top:30px; padding-right:30px; padding-bottom:30px; padding-left:30px; margin-top: 0px; margin-bottom:0px; margin-left:0px; margin-right:0px;">
    <div class="wfte_row clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_doc_title wfte_template_element" data-hover-id="doc_title">__[INVOICE]__</div>
        </div>
        <div class="wfte_col-3 float_left" style="height: 1px;">
            
        </div>
        <div class="wfte_col-3 float_right">
            <div class="wfte_invoice_data">
                <div class="wfte_qrcode wfte_template_element wfte_hidden" data-hover-id="qrcode">
                    <img src="[wfte_qrcode_url]" class="wfte_img_qrcode">
                </div>
                <div class="wfte_barcode wfte_template_element" data-hover-id="barcode">
                    <img src="[wfte_barcode_url]" class="wfte_img_barcode">
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_company_logo wfte_template_element" data-hover-id="company_logo">
                <div class="wfte_company_logo_img_box">
                    <img src="[wfte_company_logo_url]" class="wfte_company_logo_img">
                </div>
                <div class="wfte_company_name wfte_hidden"> [wfte_company_name]</div>
                <div class="wfte_company_logo_extra_details">__[]__</div>
            </div>
        </div>
        <div class="wfte_col-3 float_left" style="height: 1px;">
            
        </div>
        <div class="wfte_col-3 float_right">
            <div class="wfte_from_address wfte_template_element" data-hover-id="from_address">
                <div class="wfte_address-field-header wfte_from_address_label">__[From]__</div>
                <div class="wfte_from_address_val">
                    [wfte_from_address]
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_billing_address wfte_template_element" data-hover-id="billing_address">
                <div class="wfte_address-field-header wfte_billing_address_label">__[Bill to]__</div>
                <div class="wfte_billing_address_val">
                    [wfte_billing_address]
                </div>
            </div>
            <div class="wfte_invoice_data">
                <div class="wfte_email wfte_template_element" data-hover-id="email">
                    <span class="wfte_email_label wfte_order_fields_label">__[]__</span>
                    <span class="wfte_email_val wfte_order_fields_val">[wfte_email]</span>
                </div>
                <div class="wfte_tel wfte_template_element" data-hover-id="tel">
                    <span class="wfte_tel_label wfte_order_fields_label">__[]__ </span>
                    <span class="wfte_tel_val wfte_order_fields_val">[wfte_tel]</span>
                </div>
            </div>
        </div>
        <div class="wfte_col-3 float_left">
            <div class="wfte_shipping_address wfte_template_element wfte_hidden" data-hover-id="shipping_address">
                <div class="wfte_address-field-header wfte_shipping_address_label">__[Ship to]__</div>
                <div class="wfte_shipping_address_val">
                    [wfte_shipping_address]
                </div>
            </div>
        </div>
        <div class="wfte_col-3 float_right">
            <div class="wfte_invoice_data">
                <div class="wfte_invoice_number wfte_template_element" data-hover-id="invoice_number" style="font-size: 20px;">
                    <span class="wfte_invoice_number_label wfte_order_fields_label">__[Invoice no:]__ </span> 
                    <span class="wfte_invoice_number_val wfte_order_fields_val">[wfte_invoice_number]</span>
                </div>
                <div class="wfte_invoice_date wfte_template_element wfte_hidden" data-invoice_date-format="m-d-Y" data-hover-id="invoice_date">
                    <span class="wfte_invoice_date_label wfte_order_fields_label">__[Invoice date:]__ </span> 
                    <span class="wfte_invoice_date_val wfte_order_fields_val">[wfte_invoice_date]</span>
                </div>
                <div class="wfte_order_number wfte_template_element wfte_hidden" data-hover-id="order_number">
                    <span class="wfte_order_number_label wfte_order_fields_label">__[Order no:]__ </span> 
                    <span class="wfte_order_number_val wfte_order_fields_val">[wfte_order_number]</span>
                </div>
                <div class="wfte_order_date wfte_template_element" data-order_date-format="m-d-Y" data-hover-id="order_date">
                    <span class="wfte_order_date_label wfte_order_fields_label">__[Order date:]__ </span> 
                    <span class="wfte_order_date_val wfte_order_fields_val">[wfte_order_date]</span>
                </div>
                <div class="wfte_ssn_number wfte_template_element wfte_hidden" data-hover-id="ssn_number">
                    <span class="wfte_ssn_number_label wfte_order_fields_label">__[SSN:]__ </span> 
                    <span class="wfte_ssn_number_val wfte_order_fields_val">[wfte_ssn_number]</span>
                </div>
                <div class="wfte_vat_number wfte_template_element wfte_hidden" data-hover-id="vat_number">
                    <span class="wfte_vat_number_label wfte_order_fields_label">__[VAT:]__ </span> 
                    <span class="wfte_vat_number_val wfte_order_fields_val">[wfte_vat_number]</span>
                </div>
                <div class="wfte_product_table_payment_method wfte_template_element" data-hover-id="payment_method">
                    <span class="wfte_product_table_payment_method_label wfte_order_fields_label">__[Payment method:]__ </span>
                    <span class="wfte_product_table_payment_method_val wfte_order_fields_val">[wfte_product_table_payment_method]</span>
                </div>
                <div class="wfte_order_item_meta">[wfte_order_item_meta]</div>
                [wfte_extra_fields]
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-2 float_left">
        </div>
        <div class="wfte_col-2 float_right">
            <div class="wfte_received_seal wfte_hidden wfte_template_element" data-hover-id="received_seal"><span class="wfte_received_seal_text">__[RECEIVED]__</span>[wfte_received_seal_extra_text]</div>
        </div>
    </div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-1">
            [wfte_product_table_start]
            <table class="wfte_product_table wfte_side_padding_table wfte_template_element" data-hover-id="product_table">
                <thead class="wfte_product_table_head wfte_table_head_color wfte_product_table_head_bg">
                    <tr>
                        <th class="wfte_product_table_head_serial_no wfte_product_table_head_bg wfte_table_head_color" col-type="serial_no">__[S.No]__</th>
                        <th class="wfte_product_table_head_image wfte_product_table_head_bg wfte_table_head_color" col-type="-image" data-img-width="50px">__[Image]__</th>
                        <th class="wfte_product_table_head_sku wfte_product_table_head_bg wfte_table_head_color" col-type="-sku">__[SKU]__</th>
                        <th class="wfte_product_table_head_product wfte_product_table_head_bg wfte_table_head_color" col-type="product">__[Product]__</th>
                        <th class="wfte_product_table_head_quantity wfte_product_table_head_bg wfte_table_head_color wfte_text_center" col-type="quantity">__[Quantity]__</th>
                        <th class="wfte_product_table_head_price wfte_product_table_head_bg wfte_table_head_color" col-type="price">__[Unit price]__</th>
                        <th class="wfte_product_table_head_tax wfte_product_table_head_bg wfte_table_head_color" col-type="-tax">__[Total Tax]__</th>
                        <th class="wfte_product_table_head_total_price wfte_product_table_head_bg wfte_table_head_color wfte_text_right" col-type="total_price">__[Total price]__</th>
                    </tr>
                </thead>
                <tbody class="wfte_product_table_body wfte_table_body_color">
                </tbody>
            </table>
            [wfte_product_table_end]
            <table class="wfte_payment_summary_table wfte_product_table wfte_side_padding_table">
                <tbody class="wfte_payment_summary_table_body wfte_table_body_color">
                    <tr class="wfte_payment_summary_table_row wfte_product_table_subtotal">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_subtotal_label wfte_text_left">__[Subtotal]__</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_subtotal]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_shipping">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_shipping_label wfte_text_left">__[Shipping]__</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_shipping]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_cart_discount">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_cart_discount_label wfte_text_left">__[Cart discount]__</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_cart_discount]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_order_discount">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_order_discount_label wfte_text_left">__[Order discount]__</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_order_discount]</td>
                    </tr>
                    <tr data-row-type="wfte_tax_items" class="wfte_payment_summary_table_row wfte_product_table_tax_item">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_tax_item_label wfte_text_left">[wfte_product_table_tax_item_label]</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_tax_item]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_fee">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_fee_label wfte_text_left">__[Fee]__</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_fee]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_hidden wfte_product_table_additional_info" data-row-type="wfte_product_table_additional_info">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_additional_info_label wfte_text_left">[wfte_product_table_additional_info_label]</td>
                        <td class="wfte_right_column wfte_text_right">[wfte_product_table_additional_info_value]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_payment_total">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_payment_total_label wfte_text_left">__[Total]__</td>
                        <td class="wfte_product_table_payment_total_val wfte_right_column wfte_text_right">[wfte_product_table_payment_total]</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-1 float_left">
            <div class="wfte_invoice_data">
                <div class="wfte_customer_note wfte_template_element" data-hover-id="customer_note">
                    <span class="wfte_customer_note_label wfte_order_fields_label">__[Customer note:]__ </span> 
                    <span class="wfte_customer_note_val wfte_order_fields_val">[wfte_customer_note]</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="template_footer clearfix">
    <div class="wfte_col-1">
        <div class="wfte_footer clearfix wfte_template_element" data-hover-id="footer">
            [wfte_footer]
        </div>
    </div>
</div>