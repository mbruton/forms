<?php

namespace extensions\forms{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class view_section_layout_four_col extends view{
        
        protected $_items = array();
        
        public function add($item){
            
            $count = count($this->_items);
            $mod = $count % 4;
            
            if ($item instanceof \frameworks\adapt\html){
                $item->add_class('col-xs-12 col-sm-3');
                if ($mod == 0){
                    $row = new html_div($item, array('class' => 'row'));
                    parent::add($row);
                }else{
                    $this->find('.row')->last()->append($item);
                }
                
                $this->_items[] = $item;
            }
            
            $count = count($this->_items);
            $mod = $count % 4;
            
            if ($mod == 0){
                $this->find('.row')->last()->append(new html_div(array('class' => 'clearfix')));
            }
            
        }
        
    }
    
}

?>