<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wt_ipc_addon_arr = array(
'title' => __("WooCommerce PDF Invoices, Packing Slips and Credit Notes plugin","print-invoices-packing-slip-labels-for-woocommerce"),
'page_link' => 'https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_comparison&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION,
'features_list' => array(
    array(
        'feature_title' => __("Supported documents","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $inv_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $psl_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $cnt_text,     
                ),
            ),
        'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $inv_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $psl_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $cnt_text,     
                ),
            ),
        ),
    array(
        'feature_title' => __("Automatically create invoice based on order status","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),
    array(
        'feature_title' => __("Attach document to order email","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text,
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text,
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Custom invoice date","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_label' => __("Use order date","print-invoices-packing-slip-labels-for-woocommerce"),
                    'v_status' => true,
                ),
                array(
                    'v_label' => __("Use invoice created date","print-invoices-packing-slip-labels-for-woocommerce"),
                    'v_status' => true,
                )
            ),
        'pro' => array(
                array(
                    'v_label' => __("Use order date","print-invoices-packing-slip-labels-for-woocommerce"),
                    'v_status' => true,
                ),
                array(
                    'v_label' => __("Use invoice created date","print-invoices-packing-slip-labels-for-woocommerce"),
                    'v_status' => true,
                )
            ), 
        ),
    array(
        'feature_title' => __("Show print invoice button to customers","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_ol_page
                ),
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_od_page
                ),
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_oe_page
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_ol_page
                ),
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_od_page
                ),
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_oe_page
                ),
            ),
        ),
    array(
        'feature_title' => __("Show print packing slip button to customers","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $my_acc_ol_page
                ),
                array(
                    'v_status' => false,
                    'v_label' => $my_acc_od_page
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_ol_page
                ),
                array(
                    'v_status' => true,
                    'v_label' => $my_acc_od_page
                ),
            ),
        ),
    array(
        'feature_title' => __("Use order number as document number","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Use custom number series to number documents","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Change document number format & length","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Group products by ‘Category’","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Sort order items in product table","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Show product variation data","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Display options for bundled products","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Generate invoices for old orders","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Generate invoices for free orders","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Option to include/exclude tax to Invoice","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Separate tax column in the product table for multiple tax options in invoices","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Customizable tax items, tax fields (SSN, VAT, etc.)","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Display free line items on invoice","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Customize invoice PDF name format","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Add a payment link to invoice","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Show Pay Later option at checkout","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Customize Pay Later option display","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => __("Title","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Description","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Instruction","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => __("Title","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Description","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Instruction","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        ),
    array(
        'feature_title' => __("Add order meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text." ".__("(But customer notes can be added)","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Add product meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Add product attributes","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Custom logo for invoice","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Add product images to packing slips","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    array(
        'feature_title' => __("Custom footer","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Document customizer","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text." ".__("(Visual editor)","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text." ".__("(Visual and code editor)","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Pre-built layouts for documents","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Save document layout as template","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => false,
                    'v_label' => $cnt_text
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => $inv_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $psl_text
                ),
                array(
                    'v_status' => true,
                    'v_label' => $cnt_text
                ),
            ),
        ),
    array(
        'feature_title' => __("Add additional data to invoice layout","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => false,
                    'v_label' => __("Total tax","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Tracking number","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Subtotal","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Shipping","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Cart discount","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Order discount","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Fee","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Coupon info","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Payment method","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => false,
                    'v_label' => __("Total","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => __("Total tax","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Tracking number","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Subtotal","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Shipping","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Cart discount","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Order discount","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Fee","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Coupon info","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Payment method","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Total","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        ),
    array(
        'feature_title' => __("WPML compatibility","print-invoices-packing-slip-labels-for-woocommerce"),
        'free' => array(
                array(
                    'v_status' => true
                )
            ),
        'pro' => array(
                array(
                    'v_status' => true
                )
            ),
        ),
    ),
);
?>