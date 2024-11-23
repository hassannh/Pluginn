<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
$wt_sdd_addon_arr = array(
'title' => __("WooCommerce Shipping labels, Dispatch labels and Delivery Notes plugin","print-invoices-packing-slip-labels-for-woocommerce"),
'page_link' => 'https://www.webtoffee.com/product/woocommerce-shipping-labels-delivery-notes/?utm_source=free_plugin_comparison&utm_medium=pdf_basic&utm_campaign=Shipping_Label&utm_content='.WF_PKLIST_VERSION,
'features_list' => array(
        array(
            'feature_title' => __("Supported documents","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Shipping label size","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => __("Full page","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => __("Custom","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
            ),
            'pro' => array(
                 array(
                    'v_status'  => true,
                    'v_label'   => __("Full page","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => __("Custom","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add multiple shipping labels in one page","print-invoices-packing-slip-labels-for-woocommerce"),
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
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Group products by ‘Category’","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Show product variation data","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add order meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add product meta fields","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add product attributes","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add print document button to selected order emails","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Pre-built layout for documents","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Document customizer","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text." ".__("(Visual editor)","print-invoices-packing-slip-labels-for-woocommerce"), 
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text." ".__("(Visual and code editor)","print-invoices-packing-slip-labels-for-woocommerce"), 
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add custom fields to shipping label layout","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => false,
                    'v_label'   => __("Company logo","print-invoices-packing-slip-labels-for-woocommerce")     
                ),
                array(
                    'v_status'  => false,
                    'v_label'   => __("Return policy","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => __("Company logo","print-invoices-packing-slip-labels-for-woocommerce")     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => __("Return policy","print-invoices-packing-slip-labels-for-woocommerce"),     
                ),
            ),
        ),

        array(
            'feature_title' => __("Include product image in delivery note","print-invoices-packing-slip-labels-for-woocommerce"),
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
            'feature_title' => __("Add customer note","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Add footer","print-invoices-packing-slip-labels-for-woocommerce"),
            'free' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text, 
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
            'pro' => array(
                array(
                    'v_status'  => true,
                    'v_label'   => $shp_text, 
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $dis_text,     
                ),
                array(
                    'v_status'  => true,
                    'v_label'   => $del_text,     
                ),
            ),
        ),

        array(
            'feature_title' => __("Separate tax column in product table for multiple tax options in dispatch labels","print-invoices-packing-slip-labels-for-woocommerce"),
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
                    'v_status' => true,
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
?>