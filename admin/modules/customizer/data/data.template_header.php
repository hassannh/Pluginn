<?php
if (!defined('ABSPATH')){
    exit;
}
$the_options=!isset($the_options) ? Wf_Woocommerce_Packing_List::get_settings() : $the_options;
$print_preview=isset($the_options['woocommerce_wf_packinglist_preview']) ? $the_options['woocommerce_wf_packinglist_preview'] : 'No';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo (isset($page_title) && $page_title!="" ? $page_title : WF_PKLIST_PLUGIN_DESCRIPTION); ?></title>
		<style>
		body, html{margin:0px; padding:0px; }
		.clearfix::after { display: block; clear: both; content: "";}
		.wfte_hidden{ display:none !important; }
		.wfte_text_right{text-align:right !important; }
		.wfte_text_left{text-align:start !important; }
		.wfte_text_center{text-align:center !important; }
		.pagebreak {page-break-after: always;}
		.no-page-break {page-break-after: avoid;}
		.wt_pdf_currency_symbol{font-family: 'Currencies' !important;font-weight: normal;}
		.wfte_product_table_category_row td{ font-weight: bold;}
		</style>
		<style id="template_font_style">
		<?php if( isset( $template_for_pdf ) && true === $template_for_pdf ) { ?>
		*{font-family:"DeJaVu Sans", monospace;}
		<?php } ?>
		</style>
		<style>
		<?php echo ( isset( $custom_css ) ? $custom_css : '' ); ?>
		</style>
		<style>
		@media print {
		body{ -webkit-print-color-adjust:exact; print-color-adjust:exact;}
		#Header, #Footer { display:none !important; }
		@page { size:auto;  margin:0;  }
		body,html{ margin:0; background-color:#FFFFFF; }
		table.wfte_product_table tr, table.wfte_product_table tr td, table.wfte_payment_summary_table tr, table.wfte_payment_summary_table tr td{ page-break-inside: avoid; }
		}
		.wfte_received_seal{ page-break-inside:avoid; }
		</style>
		<?php 

			$document_action = '';
			$print_orders = array();
			
			if ( isset( $_REQUEST['attaching_pdf']) && isset( $_REQUEST['button_location'] ) && 'email' === sanitize_text_field( $_REQUEST['button_location'] ) ) {
				if ( isset( $_REQUEST['type'] ) && false !== strpos( sanitize_text_field( $_REQUEST['type'] ) , 'print_' ) ) {
					$document_action = 'print';
				}
			}
			
			
			if( ( 'No' === $print_preview && 'print' === $document_action ) || ( isset( $print_orders ) && is_array( $print_orders ) && count( $print_orders ) > 0 ) ){
			?>
			<script>
				
				window.onload = function() {
					window.print();
					setTimeout(function() {
						document.documentElement.style.display = 'none';
						window.close();
					}, 2000);
				};
			</script>
			<?php
			}
		?>
   </head>
<body>