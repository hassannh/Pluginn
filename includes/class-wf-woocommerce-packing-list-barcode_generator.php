<?php
class Wf_Woocommerce_Packing_List_Barcode_generator{
	public function __construct()
	{

	}
	public static function generate($invoice_number)
	{
		$path=plugin_dir_path(__FILE__).'vendor/';
        require_once($path.'autoload.php');
		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
		$image = 'data:image/png;base64,' . base64_encode($generator->getBarcode($invoice_number, $generator::TYPE_CODE_128));
		unset( $generator );
		return $image;
	}
	
}