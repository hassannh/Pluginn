<?php
/**
 * DOMPDF library
 *
 * @link       
 * @since 2.6.6     
 *
 * @package  Wf_Woocommerce_Packing_List  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wt_Pklist_Dompdf
{
	public $dompdf=null;
    public $option = null;
	public function __construct()
	{
		$path=plugin_dir_path(__FILE__).'vendor/';
        include_once($path.'autoload.php');
	}

    /**
     * To generate the PDF for WooCommerce order documents
     *
     * @since 4.4.2 - Added a filter to have more customization in templatewise
     * 
     */
	public function generate($upload_dir, $html, $action, $is_preview, $file_path, $args=array())
	{
        $upload_loc             = Wf_Woocommerce_Packing_List::get_temp_dir();
        $plugin_upload_folder   = $upload_loc['path'];
        $template_type          = str_replace($plugin_upload_folder,'',$upload_dir);
        $template_type          = str_replace('/','',$template_type);

        $dompdf_options_default = $dompdf_options = array(
            'paper_size'        => 'A4',
            'paper_orientation' => 'portrait',
        );
        $dompdf_options     = apply_filters( 'wt_pklist_alter_page_properties_in_dompdf', $dompdf_options, $template_type);
        $dompdf_options_set = false;
        if( !empty( $dompdf_options ) && is_array( $dompdf_options ) ) {
            $dompdf_options_set = true;
        }
        // check paper size
        $paper_size         = ( true === $dompdf_options_set && isset( $dompdf_options['paper_size'] ) && !empty( $dompdf_options['paper_size'] ) ) ? $dompdf_options['paper_size'] : $dompdf_options_default['paper_size'];
        $paper_orientation  = ( true === $dompdf_options_set && isset( $dompdf_options['paper_orientation'] ) && !empty( $dompdf_options['paper_orientation'] ) ) ? $dompdf_options['paper_orientation'] : $dompdf_options_default['paper_orientation'];

        // initiate dompdf option.
        $this->option = new \Wtpklistpdf\Dompdf\Options();
        $this->option->set('isHtml5ParserEnabled', true);
        $this->option->set('enableCssFloat', true);
        $this->option->set('isRemoteEnabled', true);
        $this->option->set('defaultFont', 'dejavu Sans');
        $this->option->set('fontHeightRatio','1.0');
        $this->option->set('enable_font_subsetting', true);
        $this->option->set('isFontSubsettingEnabled',true);


        // Use additional font library for currency.
        $this->option->set('fontDir', __DIR__ . '/pdf-assets/fonts');
        $this->option->set('fontCache', __DIR__ . '/pdf-assets/fonts');
        $this->option = apply_filters( 'wt_pklist_alter_dompdf_options', $this->option, $template_type);
       
        // initialize the dompdf with required options.
        $this->dompdf = new \Wtpklistpdf\Dompdf\Dompdf( $this->option );
        $this->dompdf->setPaper($paper_size, $paper_orientation);

        // (Optional) Setup the paper size and orientation
        $this->dompdf->loadHtml($html);
        
        // Render the HTML as PDF
        $this->dompdf->render();

        if("download" === $action || "preview" === $action)
        {  
        	$is_attachment=($is_preview ? false : true);
            $this->dompdf->stream($file_path, array("Attachment" =>$is_attachment));              
        }else
        {
        	@file_put_contents($file_path, $this->dompdf->output());
        }
        return true;    
	}
}