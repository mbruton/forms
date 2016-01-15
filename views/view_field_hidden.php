<?php

namespace adapt\forms{
    
    /*
     * Prevent direct access
     */
    defined('ADAPT_STARTED') or die;
    
    class view_field_hidden extends view_form_page_section_group_field{
        
        public function __construct($form_data, $data_type, &$user_data){
            parent::__construct($form_data, $data_type, $user_data);
            $this->add_class('field input hidden');         
            $this->add(new html_input(array('type' => 'hidden', 'name' => $form_data['name'], 'value' => $this->user_value != "" ? $this->user_value : $form_data['default_value'])));
        }
        
    }
    
}

?>