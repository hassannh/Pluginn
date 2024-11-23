<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wt_pi_addon_arr = array(
'title' => __("WooCommerce Proforma Invoices plugin","print-invoices-packing-slip-labels-for-woocommerce"),
'page_link' => 'https://www.webtoffee.com/product/woocommerce-proforma-invoice/?utm_source=free_plugin_comparison&utm_medium=pdf_basic&utm_campaign=Proforma_Invoice&utm_content='.WF_PKLIST_VERSION,
'features_list' => array(
        array(
            'feature_title' => __("Create proforma invoice automatically for selected order statuses","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Attach invoice PDF to order emails","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Show print invoice button for customers","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
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
            'feature_title' => __("Set custom proforma invoice number","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                    'v_label' => __("Based on order number","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label' => __("Custom number series","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        ),

        array(
            'feature_title' => __("Separate tax column in product table for multiple tax options in proforma invoices","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Use custom number format & length","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Group products by 'Category'","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Sort order items in product table","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Show variation data of products","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Add order meta fields ","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Add product meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Add product attributes","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Add custom footer","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Attach special notes with proforma invoices","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Attach transport terms","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Attach sales terms","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Multiple pre-built layouts","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Document customizer","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("WPML compatibility","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),
    ),
);

$wt_al_addon_arr = array(
'title' => __("WooCommerce Address Labels plugin","print-invoices-packing-slip-labels-for-woocommerce"),
'page_link' => 'https://www.webtoffee.com/product/woocommerce-address-label/?utm_source=free_plugin_comparison&utm_medium=pdf_basic&utm_campaign=Address_Label&utm_content='.WF_PKLIST_VERSION,
'features_list' => array(
        array(
            'feature_title' => __("Generate address labels","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Supported address types","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                    'v_label'  => __("Shipping address","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label'  => __("Billing address","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label'  => __("From address","print-invoices-packing-slip-labels-for-woocommerce")
                ),
                array(
                    'v_status' => true,
                    'v_label'  => __("Return address","print-invoices-packing-slip-labels-for-woocommerce")
                ),
            ),
        ),

        array(
            'feature_title' => __("Multiple address label layouts","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Customize layout properties","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("WPML compatibilityCustomize label sizes","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Bulk print shipping labels","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),

        array(
            'feature_title' => __("Multilingual support","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status' => false,
                ),
            ),
            'pro' => array(
                array(
                    'v_status' => true,
                ),
            ),
        ),
    ),
);