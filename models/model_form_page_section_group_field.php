<?php

namespace adapt\forms{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class model_form_page_section_group_field extends model{
        
        const EVENT_ON_LOAD_BY_FIELD_NAME = "model_form_page_section_group_field.field_name";
        
        public function __construct($id = null){
            parent::__construct('form_page_section_group_field', $id);
        }
        
        public function initialise(){
            parent::initialise();
            
            /*$this->_auto_load_only_tables = array(
                'form_page_section_group_field_addon'
            );*/
            
            //$this->_auto_load_children = true;
        }
        
        public function get_view($user_data = array()){
            $user_data = $this->convert_user_data($user_data);
            $value = null;
            
            foreach($user_data as $item){
                if ($item['key'] == $this->name){
                    $value = $item['value'];
                }
            }
            
            $hash = $this->to_hash();
            $hash['form_page_section_group_field']['value'] = $value;
            
            if (isset($hash['form_page_section_group_field']['allowed_values'])){
                $hash['form_page_section_group_field']['allowed_values'] = json_decode($hash['form_page_section_group_field']['allowed_values'], true);
            }elseif(isset($hash['form_page_section_group_field']['lookup_table'])){
                $structure = $this->data_source->get_row_structure($hash['form_page_section_group_field']['lookup_table']);
                if (isset($structure) && is_array($structure)){
                    $key = '';
                    $label = '';
                    $date_deleted = '';
                    $permission_id = '';
                    $values = array();
                    
                    foreach($structure as $field){
                        if ($field['primary_key'] == 'Yes'){
                            $key = $field['field_name'];
                        }elseif($field['field_name'] == 'label'){
                            $label = 'label';
                        }elseif($field['field_name'] == 'name' && $label == ''){
                            $label = 'name';
                        }elseif($field['field_name'] == 'date_deleted'){
                            $date_deleted = $field['field_name'];
                        }elseif($field['field_name'] == 'permission_id'){
                            $permission_id = $field['field_name'];
                        }
                    }
                    
                    if (isset($key) && isset($label)){
                        $select_fields = array(
                            'id' => $key,
                            'name' => $label
                        );
                        if (isset($permission_id) && $permission_id != ''){
                            $select_fields[$permission_id] = $permission_id;
                        }
                        
                        $sql = $this->data_source->sql;
                        
                        $sql->select($select_fields)
                            ->from($hash['form_page_section_group_field']['lookup_table']);
                        
                        if (isset($date_deleted) && $date_deleted != ''){
                            $sql->where(new \adapt\sql_condition(new \adapt\sql('date_deleted'), 'is', new \adapt\sql('null')));
                        }
                        
                        $results = $sql->execute()->results();
                        $allowed_values = array();
                        
                        foreach($results as $result){
                            if (!isset($permission_id) || $permission_id == '' || is_null($result[$permission_id]) || $this->session->user->has_permission($result[$permission_id])){
                                $allowed_values[$result['id']] = $result['name'];
                            }
                        }
                        
                        $hash['form_page_section_group_field']['allowed_values'] = $allowed_values;
                    }
                }
            }elseif(isset($hash['form_page_section_group_field']['lookup_sql_statement'])){
                $statement_handle = $this->data_source->read($hash['form_page_section_group_field']['lookup_sql_statement']);
                $results = null;
                
                if ($statement_handle){
                    $results = $this->data_source->fetch($statement_handle, \adapt\data_source_sql::FETCH_ALL_ASSOC);
                }
                
                if (is_array($results) && count($results) && isset($results[0]['name'])){
                    $allowed_values = [];
                    
                    foreach($results as $result){
                        $label_field = 'name';
                        if (isset($result['label'])){
                            $label_field = 'label';
                        }
                        
                        if (!isset($result['permission_id']) || $result['permission_id'] == '' || is_null($result['permission_id']) || $this->session->user->has_permission($result['permission_id'])){
                            $last_cat = null;
                            if (isset($result['category'])){
                                if ($last_cat != $result['category']){
                                    $last_cat = $result['category'];
                                    $allowed_values[$last_cat] = [];
                                }
                                
                                if (is_null($last_cat)){
                                    $allowed_values[$result['id']] = $result[$label_field];
                                }else{
                                    $allowed_values[$last_cat][$result['id']] = $result[$label_field];
                                }
                            }
                        }
                    }
                    
                    $hash['form_page_section_group_field']['allowed_values'] = $allowed_values;
                }
            }elseif(isset($hash['form_page_section_group_field']['lookup_class_name']) && isset($hash['form_page_section_group_field']['lookup_method'])){
                $class_name = $hash['form_page_section_group_field']['lookup_class_name'];
                $method = $hash['form_page_section_group_field']['lookup_method'];
                if (class_exists($class_name)){
                    $class = new $class_name();
                    if (method_exists($class, $method)){
                        if ($class instanceof \adapt\controller){
                            $permission_method = "permission_{$method}";
                            if (!method_exists($class, $permission_method) || $class->$permission_method()){
                                $hash['form_page_section_group_field']['allowed_values'] = $class->$method();
                            }
                        }
                    }
                }
            }
            
            if ($this->is_loaded){
                $view = null;
                
                if ($this->custom_view && trim($this->custom_view) != ''){
                    $class = $this->custom_view;
                    $view = new $class($hash, $user_data);
                    return $view;
                }else{
                    /* Load the field type */
                    $type = $this->get_form_field_type($this->form_field_type_id);
                    //$type = new model_form_field_type($this->form_field_type_id);
                    if ($type->is_loaded){
                        $class = $type->view;
                        //print new html_pre($class);
                        $view = new $class($hash, $user_data);
                        if ($type->name == 'Hidden'){
                            $view->add_class('hidden');
                        }
                        
                    }
                }
                
                if ($view){
                    $children = $this->get();
                    foreach($children as $child){
                        if ($child instanceof \adapt\model){
                            switch($child->table_name){
                            case 'form_page_section_group_field_addon':
                                switch($child->type){
                                case "Text":
                                    $group = new html_span($child->label, array('class' => 'input-group-addon'));
                                    if ($child->position == 'Before'){
                                        $view->add_on(true, $group);
                                    }else{
                                        $view->add_on(false, $group);
                                    }
                                    break;
                                case "Button":
                                    break;
                                case "Icon":
                                    $class = $child->icon_class;
                                    $icon = new $class($child->icon_name);
                                    if ($icon && $icon instanceof \adapt\html){
                                        $group = new html_span($icon, array('class' => 'input-group-addon'));
                                        if ($child->position == 'Before'){
                                            $view->add_on(true, $group);
                                        }else{
                                            $view->add_on(false, $group);
                                        }
                                    }
                                    break;
                                case "Radio":
                                    break;
                                case "Checkbox":
                                    break;
                                case "Select":
                                    break;
                                }
                                break;
                            }
                        }
                    }
                }
                
                
                return $view;
            }
            
            return null;
        
        
            
            if ($this->is_loaded){
                $type = new model_form_field_type($this->form_field_type_id);
                if ($type->is_loaded){
                    $model = $type->view;
                    $hash = $this->to_hash();
                    $hash = $hash[$this->table_name];
                    
                    $hash = array_merge($this->data_source->get_data_type($this->data_type_id), $hash);
                    
                    foreach($form_data as $table_name => $items){
                        
                        foreach($items as $field_name => $value){
                            $key = "{$table_name}[{$field_name}]";
                            if ($key == $hash['name']){
                                $hash['value'] = $value;
                            }
                        }
                    }
                    
                    if (isset($hash['allowed_values']) && is_json($hash['allowed_values'])){
                        $hash['allowed_values'] = json_decode($hash['allowed_values'], true);
                    }elseif(isset($hash['lookup_table'])){
                        /* We need to get a list of values from a table */
                        $structure = $this->data_source->get_row_structure($hash['lookup_table']);
                        if (isset($structure) && is_array($structure)){
                            $key = '';
                            $label = '';
                            $date_deleted = '';
                            $permission_id = '';
                            $values = array();
                            
                            foreach($structure as $field){
                                if ($field['primary_key'] == 'Yes'){
                                    $key = $field['field_name'];
                                }elseif($field['field_name'] == 'label'){
                                    $label = 'label';
                                }elseif($field['field_name'] == 'name' && $label == ''){
                                    $label = 'name';
                                }elseif($field['field_name'] == 'date_deleted'){
                                    $date_deleted = $field['field_name'];
                                }elseif($field['field_name'] == 'permission_id'){
                                    $permission_id = $field['field_name'];
                                }
                            }
                            
                            if (isset($key) && isset($label)){
                                $select_fields = array(
                                    'id' => $key,
                                    'name' => $label
                                );
                                if (isset($permission_id) && $permission_id != ''){
                                    $select_fields[$permission_id] = $permission_id;
                                }
                                
                                $sql = $this->data_source->sql;
                                
                                $sql->select($select_fields)
                                    ->from($hash['lookup_table']);
                                
                                if (isset($date_deleted) && $date_deleted != ''){
                                    $sql->where(new \adapt\sql_condition(new \adapt\sql('date_deleted'), 'is', new \adapt\sql('null')));
                                }
                                
                                $results = $sql->execute()->results();
                                $allowed_values = array();
                                
                                foreach($results as $result){
                                    if (!isset($permission_id) || $permission_id == '' || is_null($result[$permission_id]) || $this->session->user->has_permission($result[$permission_id])){
                                        $allowed_values[$result['id']] = $result['name'];
                                    }
                                }
                                
                                $hash['allowed_values'] = $allowed_values;
                            }
                        }
                        //print new html_pre(print_r($this->data_source->get_row_structure($hash['lookup_table']), true));
                    }
                    
                    $hash['mandatory'] = $hash['mandatory'] == 'Yes' ? true : false;
                    
                    $hash['depends_on'] = array();
                    
                    //$view = new $model($hash);
                    //$view->add_class('form-page-section-field');
                    
                    $children = $this->get();
                    
                    foreach($children as $child){
                        if(is_object($child) && $child instanceof \model_form_page_section_field_condition){
                            
                            $condition = array();
                            
                            $field = new model_form_page_section_field($child->depends_on_form_page_section_field_id);
                            if ($field->is_loaded){
                                $condition['field_name'] = $field->name;
                                
                                
                                $operator = '=';
                                $value = $child->value;
                                
                                switch($child->operator){
                                case 'Equal to':
                                    $operator = '=';
                                    break;
                                case 'Less than':
                                    $operator = '<';
                                    break;
                                case 'Less than or equal to':
                                    $operator = '<=';
                                    break;
                                case 'Greater than':
                                    $operator = '>';
                                    break;
                                case 'Greater than or equal to':
                                    $operator = '>=';
                                    break;
                                case 'One of':
                                    $operator = 'in';
                                    $value = preg_replace("/\"/", "'", $value);
                                    break;
                                case 'Javascript function':
                                    $operator = 'function';
                                    break;
                                }
                                
                                $condition['operator'] = $operator;
                                $condition['value'] = $value;
                                $hash['depends_on'][] = $condition;
                                //$input = new html_input(array('type' => 'hidden', 'name' => 'depends_on', 'value' => $field->name, 'data-operator' => $operator, 'data-values' => $value));
                                //$view->add($input);
                            }
                        }
                    }
                    
                    $view = new $model($hash);
                    $view->add_class('form-page-section-field');
                    
                    return $view;
                }
            }
            
            return null;
        }
        
        public function load_by_field_name($field_name){
            $this->initialise();
            
            if ($field_name){
                $sql = $this->data_source->sql;
                
                $sql->select('*')
                    ->from('form_page_section_group_field')
                    ->where(
                        new sql_and(
                            new sql_cond('field_name', sql::EQUALS, sql::q($field_name)),
                            new sql_cond('date_deleted', sql::IS, new sql_null())
                        )
                    );
                
                $results = $sql->execute(0)->results();
                
                if (count($results) == 1){
                    $this->trigger(self::EVENT_ON_LOAD_BY_FIELD_NAME);
                    return $this->load_by_data($results[0]);
                }elseif(count($results) == 0){
                    $this->error("Unable to find a record");
                }elseif(count($results) > 1){
                    $this->error(count($results) . " records found, expecting 1.");
                }
            }
            
            $this->initialise();
            return false;
        }
        
        public function load_by_name($name){
            return $this->load_by_field_name($name);
        }
        
        protected function convert_user_data($user_data){
            $output = array();
            
            foreach($user_data as $name => $values){
                $key = $name;
                if (is_array($values) && is_assoc($values)){
                    foreach($values as $field => $value){
                        if (is_array($value)){
                            foreach($value as $v){
                                $key = "{$name}[$field][]";
                                $output[] = array('key' => $key, 'value' => $v);
                            }
                        }else{
                            $key = "{$name}[$field]";
                            $output[] = array('key' => $key, 'value' => $value);
                        }
                    }
                }else{
                    $output[] = array('key' => $key, 'value' => $values);
                }
            }
            
            
            return $output;
        }
        
    }
    
}

