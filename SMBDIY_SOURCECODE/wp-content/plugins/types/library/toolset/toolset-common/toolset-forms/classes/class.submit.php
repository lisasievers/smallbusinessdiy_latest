<?php

/**
 *
 *
 */
require_once 'class.textfield.php';

class WPToolset_Field_Submit extends WPToolset_Field_Textfield {

    public function metaform() {
        $attributes = $this->getAttr();
        $shortcode_class = array_key_exists( 'class', $attributes ) ? $attributes['class'] : "";

        $metaform = array();
        $metaform[] = array(
            '#type' => 'submit',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => esc_attr( __( $this->getValue(), 'wpv-views' ) ),
            '#validate' => $this->getValidationData(),
            '#attributes' => array(
                'class' => $shortcode_class,
            ),
        );
                        
        return $metaform;
    }

}
