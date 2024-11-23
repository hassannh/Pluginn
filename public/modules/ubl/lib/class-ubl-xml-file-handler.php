<?php
namespace Wtpdf\Ubl\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\Ubl\\Handler\\XmlFileHandler' )) {

class XmlFileHandler{

    public function __construct() {
    }

    public function generate_xml( $xml_content, $basedir, $name, $action = '', $order = null ) {
        $upload_loc = \Wf_Woocommerce_Packing_List::get_temp_dir();
        $upload_dir = $upload_loc['path'];
        $upload_url = $upload_loc['url'];

        if( !is_dir( $upload_dir ) ) {
            @mkdir( $upload_dir, 0700 );
        }

        $upload_dir=$upload_dir.'/'.$basedir.'/ubl';
        $upload_url=$upload_url.'/'.$basedir.'/ubl';
        if( !is_dir( $upload_dir ) ) {
            @mkdir( $upload_dir, 0700 );
        }

        if( is_dir( $upload_dir ) ) {
            $file_path = $upload_dir . '/'.$name.'.xml';
            $file_url = $upload_url . '/'.$name.'.xml';
           
            // Write the XML content to the file locally
            file_put_contents($file_path, $xml_content);
            
            if( "download" === $action ) {  
                 // Set the appropriate headers to force the browser to download the file.
                header('Content-Type: application/xml');                          // Set the content type to XML.
                header('Content-Disposition: attachment; filename="' . $name . '.xml"');  // Set the content disposition to 'attachment' to force download.

                // Output the XML content (this will be downloaded as a file by the browser).
                echo $xml_content;
            } else {
                // email attachment.
                return $file_path;
            }
        }
    }
}

}