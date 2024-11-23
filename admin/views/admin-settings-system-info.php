<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
?>
<h3 style="display:inline-block;"><?php esc_html_e('System Configuration', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></h3>
<a id="sys_info_copy" class="page-title-action" style="display:inline-block;"><span class="dashicons dashicons-admin-page"></span> <?php echo esc_html(__('Copy', 'print-invoices-packing-slip-labels-for-woocommerce')); ?></a>
<span id="wt_sys_info_copied" style="color:#3F7E00;font-weight: 600;display: none;"><i><?php echo esc_html(__('Copied', 'print-invoices-packing-slip-labels-for-woocommerce')); ?></i></span>
<?php
$wp_memory_limit = function_exists('wc_let_to_num') ? wc_let_to_num(WP_MEMORY_LIMIT) : woocommerce_let_to_num(WP_MEMORY_LIMIT);
$php_memory_limit = function_exists('memory_get_usage') ? @ini_get('memory_limit') : '-';
$gmagick_loaded = extension_loaded('gmagick');
$imagick_loaded = extension_loaded('imagick');
$upload_loc = Wf_Woocommerce_Packing_List::get_temp_dir();

$server_configs = array(
	'PHP version' => array(
		'required' => esc_html__('5.6 or higher recommended', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => PHP_VERSION,
		'result' => version_compare(PHP_VERSION, '5.6', '>'),
	),
	'DOMDocument extension' => array(
		'required' => true,
		'value' => phpversion('DOM'),
		'result' => class_exists('DOMDocument'),
	),
	'MBString extension' => array(
		'required' => true,
		'value' => phpversion('mbstring'),
		'result' => function_exists('mb_send_mail'),
		'fallback' => esc_html__('Recommended, will use fallback functions', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'GD' => array(
		'required' => true,
		'value' => phpversion('gd'),
		'result' => function_exists('imagecreate'),
		'fallback' => esc_html__('Required if you have images in your documents', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'GMagick or IMagick' => array(
		'required' => esc_html__('Better with transparent PNG images', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => extension_loaded('gmagick') || extension_loaded('imagick'),
		'fallback' => esc_html__('Recommended for better performances', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'WebP Support' => array(
		'required' => esc_html__('Required when using .webp images', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => function_exists('imagecreatefromwebp'),
		'fallback' => esc_html__('Required if you have .webp images in your documents', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'Zlib' => array(
		'required' => esc_html__('To compress PDF documents', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => phpversion('zlib'),
		'result' => function_exists('gzcompress'),
		'fallback' => esc_html__('Recommended to compress PDF documents', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'opcache' => array(
		'required' => esc_html__('For better performances', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => false,
		'fallback' => esc_html__('Recommended for better performances', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'glob()' => array(
		'required' => esc_html__('Required to detect custom templates and to clear the temp folder periodically', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => function_exists('glob'),
		'fallback' => esc_html__('Check PHP disable_functions', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'WP Memory Limit' => array(
		'required' => esc_html__('Recommended 128MB or more', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => sprintf('WordPress: %s, PHP: %s', WP_MEMORY_LIMIT, $php_memory_limit),
		'result' => $wp_memory_limit > 67108864,
	),
	'fileinfo' => array(
		'required' => esc_html__('Necessary to verify the MIME type of local images.', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => extension_loaded('fileinfo'),
		'fallback' => esc_html__('fileinfo disabled', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'allow_url_fopen' => array(
		'required' => esc_html__('Allow remote stylesheets and images', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => null,
		'result' => ini_get('allow_url_fopen'),
		'fallback' => esc_html__('allow_url_fopen disabled', 'print-invoices-packing-slip-labels-for-woocommerce'),
	),
	'upload_folder' => array(
		'required' => esc_html__('Writable', 'print-invoices-packing-slip-labels-for-woocommerce'),
		'value' => is_writable($upload_loc['path']) ? "Yes" : "No",
		'result' => is_writable($upload_loc['path']),
	),
);

if (($xc = extension_loaded('xcache')) || ($apc = extension_loaded('apc')) || ($zop = extension_loaded('Zend OPcache')) || ($op = extension_loaded('opcache'))) {
	$server_configs['opcache']['result'] = true;
	$server_configs['opcache']['value'] = (
		$xc ? 'XCache ' . phpversion('xcache') : (
			$apc ? 'APC ' . phpversion('apc') : (
				$zop ? 'Zend OPCache ' . phpversion('Zend OPcache') : 'PHP OPCache ' . phpversion('opcache')
			)
		)
	);
}
if (($gm = extension_loaded('gmagick')) || ($im = extension_loaded('imagick'))) {
	$server_configs['GMagick or IMagick']['value'] = ($im ? 'IMagick ' . phpversion('imagick') : 'GMagick ' . phpversion('gmagick'));
}
?>
<style type="text/css">
	.wt_sys_info_border_right { border-right: 1px solid #CFCFCF; }
	.wt_sys_info_correct { background-color: #E8FFD1; color: #3F7E00; }
	.wt_sys_info_fallback { background: #ffeba7; color: #836500; }
	.wt_sys_info_warning { background-color: #FFCDC9; color: #A51205; }
</style>
<table id="wt_pklist_sys_info_table" cellspacing="1px" cellpadding="10px" width="100%" style="border: 1px solid #CFCFCF;margin: 25px 0;border-collapse: collapse;">
	<tr style="background: #F0F0F1;">
		<th class="wt_sys_info_border_right" align="left">&nbsp;</th>
		<th class="wt_sys_info_border_right" align="left"><?php esc_html_e('Required', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></th>
		<th align="left"><?php esc_html_e('Present', 'print-invoices-packing-slip-labels-for-woocommerce'); ?></th>
	</tr>
	<?php foreach ($server_configs as $label => $server_config) {
		if ($server_config['result']) {
			$sys_info_class = "wt_sys_info_correct";
		} elseif (isset($server_config['fallback'])) {
			$sys_info_class = "wt_sys_info_fallback";
		} else {
			$sys_info_class = "wt_sys_info_warning";
		}

		if ($label == "upload_folder") {
			$label = "PDF upload folder <br><i>(" . esc_html($upload_loc['path']) . ")</i>";
		}
	?>
		<tr style="background:#FFF;border: 1px solid #CFCFCF;">
			<td class="title wt_sys_info_border_right" width="35%"><?php echo $label; ?></td>
			<td class="wt_sys_info_border_right"><?php echo ($server_config['required'] === true ? 'Yes' : esc_html($server_config['required'])); ?></td>
			<td class="<?php echo esc_attr($sys_info_class); ?>">
				<?php
				echo esc_html($server_config['value']);
				if ($server_config['result'] && !$server_config['value']) {
					echo 'Yes';
				}
				if (!$server_config['result']) {
					if (isset($server_config['fallback'])) {
						echo '<div>No. ' . esc_html($server_config['fallback']) . '</div>';
					}
					if (isset($server_config['failure'])) {
						echo '<div>' . esc_html($server_config['failure']) . '</div>';
					}
				}
				?>
			</td>
		</tr>
	<?php } ?>
</table>

<er id="wt_sys_info_box" style="display: none;">
	<?php foreach ($server_configs as $label => $server_config) {
		echo esc_html($label) . "<br>";
		echo "Required: " . ($server_config['required'] === true ? 'Yes' : esc_html($server_config['required'])) . "<br>";
		echo "Present: " . esc_html($server_config['value']) . "<br>";
		if ($server_config['result'] && !$server_config['value']) {
			echo 'Yes' . "<br>";
		}
		if (!$server_config['result']) {
			if (isset($server_config['fallback'])) {
				echo 'No. ' . esc_html($server_config['fallback']) . "<br>";
			}
			if (isset($server_config['failure'])) {
				echo esc_html($server_config['failure']) . "<br>";
			}
		}
		echo "===============<br>";
	} ?>
</er>