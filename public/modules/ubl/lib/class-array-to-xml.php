<?php
namespace Wtpdf\Ubl\Lib;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\Ubl\\Lib\\ArrayToXmlConverter')) {

    class ArrayToXmlConverter
    {
        public function convertToXml( $data, &$xmlconvert ) {
    
            $namespaces = array(
                'xmlns' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                'xmlns:cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
                'xmlns:cbc' => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
            );
            foreach( $data as $item ) {

                if ( is_array( $item ) && !isset( $item['name'] ) && !isset( $item['value'] ) ) {
                    $item_arr_values = array_values( $item );
                    $this->convertToXml( $item_arr_values, $xmlconvert );
                }
                
                if ( isset( $item['name'] ) && !empty( $item['name'] ) && isset( $item['value'] ) && !empty( $item['value'] ) ) {
                    if ( is_array( $item['value'] ) ) {
                        $attr = array();
                        if ( isset( $item['attributes'] ) && is_array( $item['attributes'] ) ) {
                            $attr = $item['attributes'];
                        }
                        $parent_node = $xmlconvert->add( $item['name'], null, $attr );  
                        $this->convertToXml( $item['value'], $parent_node );
                    } else if ( !is_array( $item['value'] ) && !is_object( $item['value'] ) ) {
                        $attr = array();
                        if ( isset( $item['attributes'] ) && is_array( $item['attributes'] ) ) {
                            $attr = $item['attributes'];
                        }
                        $xmlconvert->add( $item['name'], $item['value'], $attr );
                    }
                }
            }
            return $xmlconvert->asXML();
        }
    }

}
?>