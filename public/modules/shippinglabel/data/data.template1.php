<!-- DC ready -->
<style type="text/css">
body, html{font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;line-height: 1.5;}
.clearfix::after{ display:block; clear:both; content:""; }

.wfte_invoice-main{ color:#2A3646; font-size:12px; font-weight:400; box-sizing:border-box; height:auto;width: auto; }
.wfte_invoice-main *{ box-sizing:border-box;}

.wfte_row{ width:100%; display:block; }
.wfte_sub_row{ width:100%; display:block; }
.wfte_col-1{ width:100%; display:block;}
.wfte_col-2{ width:50%; display:block;}
.wfte_col-3{ width:33%; display:block;}
.wfte_col-4{ width:25%; display:block;}
.wfte_col-6{ width:30%; display:block;}
.wfte_col-7{ width:69%; display:block;}

.wfte_invoice_data div span:first-child, .wfte_extra_fields span:first-child{ font-weight:bold; }
.wfte_invoice_data{ font-size:12px; }

.float_left{ float:left; }
.float_right{ float:right; }
</style>
<div class="wfte_rtl_main wfte_invoice-main wfte_main wfte_custom_shipping_size wfte_invoice_basic_main" style="padding-top:30px; padding-right:30px; padding-bottom:30px; padding-left:30px; margin-top: 0px; margin-bottom:0px; margin-left:0px; margin-right:0px;">
    <div class="wfte_row clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_from_address wfte_template_element" data-wfte_name="from_address" data-hover-id="from_address">
                <div class="wfte_address-field-header wfte_from_address_label">__[]__</div>
                <div class="wfte_from_address_val">[wfte_from_address]</div>
            </div>
        </div>
        <div class="wfte_col-3 float_left" style="height: 1px;"></div>
        <div class="wfte_col-3 float_right">
            <div class="wfte_shipping_details wfte_invoice_data" data-wfte_name="invoice_data">
                <div class="wfte_order_number wfte_template_element" data-hover-id="order_number">
                    <span class="wfte_order_number_label" style="font-weight:bold;">__[Order No:]__</span>
                    <span class="wfte_order_number_val">[wfte_order_number]</span>
                </div>
                <div class="wfte_order_date wfte_template_element" data-order_date-format="m-d-Y" data-hover-id="order_date">
                    <span class="wfte_order_date_label wfte_order_fields_label" style="font-weight:bold;">__[Order date:]__ </span> 
                    <span class="wfte_order_date_val wfte_order_fields_val">[wfte_order_date]</span>
                </div>
                <div class="wfte_weight wfte_template_element" data-hover-id="weight">
                    <span class="wfte_weight_label" style="font-weight:bold;">__[Weight:]__</span>
                    <span class="wfte_weight_val">[wfte_weight]</span>
                </div>
                <div class="wfte_shipping_method wfte_template_element wfte_hidden" data-hover-id="shipping_method">
                    <span class="wfte_shipping_method_label" style="font-weight:bold;">__[Shipping method:]__</span>
                    <span class="wfte_shipping_method_val">[wfte_shipping_method]</span>
                </div>
                <div class="wfte_vat_number wfte_template_element wfte_hidden" data-hover-id="vat_number">
                    <span class="wfte_vat_number_label" style="font-weight:bold;">__[VAT:]__</span>
                    <span class="wfte_vat_number_val">[wfte_vat_number]</span>
                </div>
                <div class="wfte_ssn_number wfte_template_element wfte_hidden" data-hover-id="ssn_number">
                    <span class="wfte_ssn_number_label" style="font-weight:bold;">__[SSN:]__</span>
                    <span class="wfte_ssn_number_val">[wfte_ssn_number]</span>
                </div>
                <div class="wfte_customer_note wfte_template_element" data-hover-id="customer_note">
                    <span class="wfte_customer_note_label" style="font-weight:bold;">__[Customer note:]__</span>
                    <span class="wfte_customer_note_val">[wfte_customer_note]</span>
                </div>
                <div class="wfte_order_item_meta">[wfte_order_item_meta]</div>
                [wfte_extra_fields]
                [wfte_additional_data]
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding: 40px;">
        <div class="wfte_col-3 float_left" style="height: 1px;"></div>
        <div class="wfte_col-3 float_left">
            <div class="wfte_shipping_address wfte_template_element" data-wfte_name="shipping_address" data-hover-id="shipping_address">
                <div class="wfte_address-field-header wfte_shipping_address_label">__[To:]__</div>
                <div class="wfte_shipping_address_val">[wfte_shipping_address]</div>
            </div>
            <div class="wfte_invoice_data" data-wfte_name="invoice_data">
                <div class="wfte_tel wfte_template_element" data-hover-id="tel">
                    <span class="wfte_tel_label" style="font-weight:bold;">__[Tel:]__</span>
                    <span class="wfte_tel_val">[wfte_tel]</span>
                </div>
                <div class="wfte_email wfte_template_element" data-hover-id="email">
                    <span class="wfte_email_label" style="font-weight:bold;">__[Email:]__</span>
                    <span class="wfte_email_val">[wfte_email]</span>
                </div>
            </div>
        </div>
        <div class="wfte_col-3 float_right" style="height: 1px;"></div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix" style="padding-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix">
        <div class="wfte_col-1 float_left">
            <div class="wfte_footer wfte_text_left clearfix wfte_template_element" data-wfte_name="footer" data-hover-id="footer">
            [wfte_footer]
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>