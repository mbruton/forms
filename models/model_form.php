<?php

namespace adapt\forms{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class model_form extends model{
        
        protected $_form_data;
        
        public function __construct($id = null){
            parent::__construct('form', $id);
        }
        
        /* Over-ride the initialiser to auto load children */
        public function initialise(){
            /* We must initialise first! */
            parent::initialise();
            
            /* We need to limit what we auto load */
            $this->_auto_load_only_tables = array(
                'form_page'
            );
            
            /* Switch on auto loading */
            //$this->_auto_load_children = true;
            
            
            
        }
        
        public function pget_form_data(){
            return $this->_form_data;
        }
        
        public function pset_form_data($form_data){
            $this->_form_data = $form_data;
        }
        
        public function load_by_data($data){
            $return = parent::load_by_data($data);
            
            if ($return){
                $form_data = $this->to_hash();
                
                $sql_cache_time = 60 * 60 * 12;
                
                /* Load pages */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page', 'p')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('p.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('p.form_id'),
                                '=',
                                $this->form_id
                            )
                        )
                    )
                    ->order_by('p.priority');
                
                $pages = $sql->execute($sql_cache_time)->results();
                foreach($pages as $page){
                    $form_data['form_page_id'][] = $page['form_page_id'];
                    $form_data['form_page'][] = $page;
                }
                
                
                /* Load page buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_id']) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_id')
                    ->order_by('b.priority');
                
                $page_buttons = $sql->execute($sql_cache_time)->results();
                foreach($page_buttons as $button){
                    $form_data['form_page_button'][] = $button;
                    $form_data['form_page_button_id'][] = $button['form_page_button_id'];
                }
                
                /* Load page conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_id']) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_id');
                
                $page_conditions = $sql->execute($sql_cache_time)->results();
                foreach($page_conditions as $condition){
                    $form_data['form_page_condition'][] = $condition;
                    $form_data['form_page_condition_id'][] = $condition['form_page_condition_id'];
                }
                
                
                /* Load sections */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section', 's')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('s.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('s.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_id']) . ')')
                            )
                        )
                    )
                    ->order_by('s.form_page_id')
                    ->order_by('s.priority');
                
                $sections = $sql->execute($sql_cache_time)->results();
                foreach($sections as $section){
                    $form_data['form_page_section'][] = $section;
                    $form_data['form_page_section_id'][] = $section['form_page_section_id'];
                }
                
                /* Load section buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_id']) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_section_id')
                    ->order_by('b.priority');
                
                $section_buttons = $sql->execute($sql_cache_time)->results();
                foreach($section_buttons as $button){
                    $form_data['form_page_section_button'][] = $button;
                }
                
                /* Load section conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_id']) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_section_id');
                
                $section_conditions = $sql->execute($sql_cache_time)->results();
                foreach($section_conditions as $condition){
                    $form_data['form_page_section_condition'][] = $condition;
                }
                
                
                /* Load groups */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group', 'g')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('g.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('g.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_id']) . ')')
                            )
                        )
                    )
                    ->order_by('g.form_page_section_id')
                    ->order_by('g.priority');
                
                $groups = $sql->execute($sql_cache_time)->results();
                foreach($groups as $group){
                    $form_data['form_page_section_group'][] = $group;
                    $form_data['form_page_section_group_id'][] = $group['form_page_section_group_id'];
                }
                
                
                /* Load group buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_group_id']) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_section_group_id')
                    ->order_by('b.priority');
                
                $group_buttons = $sql->execute($sql_cache_time)->results();
                
                foreach($group_buttons as $button){
                    $form_data['from_page_section_group_button'][] = $button;
                }
                
                /* Load group conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_group_id']) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_section_group_id');
                
                $group_conditions = $sql->execute($sql_cache_time)->results();
                foreach($group_conditions as $condition){
                    $form_data['form_page_section_group_condition'][] = $condition;
                }
                
                /* Load fields */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_field', 'f')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('f.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('f.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_group_id']) . ')')
                            )
                        )
                    )
                    ->order_by('f.form_page_section_group_id')
                    ->order_by('f.priority');
                
                $fields = $sql->execute($sql_cache_time)->results();
                foreach($fields as $field){
                    $form_data['form_page_section_group_field'][] = $field;
                    $form_data['form_page_section_group_field_id'][] = $field['form_page_section_group_field_id'];
                }
                
                
                /* Load field addons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_field_addon', 'a')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('a.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('a.form_page_section_group_field_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $form_data['form_page_section_group_field_id']) . ')')
                            )
                        )
                    )
                    ->order_by('a.form_page_section_group_field_id');
                $field_addons = $sql->execute($sql_cache_time)->results();
                
                foreach($field_addons as $addon){
                    $form_data['form_page_section_group_field_addon'][] = $addon;
                }
                
                
                $this->_form_data = $form_data;
            }
            
            return $return;
            
            if ($return){
                /* We are going to auto load manually
                * so that we can load the data as fast as
                * possible
                */
                $pages = array();
                $page_objects = array();
                $page_ids = array();
                
                $page_buttons = array();
                $page_button_ids = array();
                
                $page_conditions = array();
                $page_condition_ids = array();
               
                $sections = array();
                $section_objects = array();
                $section_ids = array();
                
                $section_buttons = array();
                $section_conditions = array();
                
                $groups = array();
                $group_objects = array();
                $group_ids = array();
                
                $group_buttons = array();
                $group_conditions = array();
                
                $fields = array();
                $field_objects = array();
                $field_ids = array();
                
                $field_addons = array();
                
                $sql_cache_time = 60 * 60 * 12;
               
                /* Load pages */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page', 'p')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('p.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('p.form_id'),
                                '=',
                                $this->form_id
                            )
                        )
                    )
                    ->order_by('p.priority');
                
                $pages = $sql->execute($sql_cache_time)->results();
                foreach($pages as $page){
                    $o = new model_form_page();
                    $o->load_by_data($page);
                    $page_objects[] = $o;
                    $this->add($o);
                    
                    $page_ids[] = $page['form_page_id'];
                }
                
                /* Load page buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $page_ids) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_id')
                    ->order_by('b.priority');
                
                $page_buttons = $sql->execute($sql_cache_time)->results();
                foreach($page_buttons as $button){
                    $o = new model_form_page_button();
                    $o->load_by_data($button);
                    foreach($page_objects as $p){
                        if ($p->form_page_id == $o->form_page_id){
                            $p->add($o);
                            break;
                        }
                    }
                    
                    $page_button_ids[] = $button['form_page_button_id'];
                }
                
                /* Load page conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $page_ids) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_id');
                
                $page_conditions = $sql->execute($sql_cache_time)->results();
                foreach($page_conditions as $condition){
                    $o = new model_form_page_condition();
                    $o->load_by_data($condition);
                    foreach($page_objects as $p){
                        if ($p->form_page_id == $o->form_page_id){
                            $p->add($o);
                            break;
                        }
                    }
                    
                    $page_condition_ids[] = $condition['form_page_condition_id'];
                }
                
                /* Load sections */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section', 's')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('s.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('s.form_page_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $page_ids) . ')')
                            )
                        )
                    )
                    ->order_by('s.form_page_id')
                    ->order_by('s.priority');
                
                $sections = $sql->execute($sql_cache_time)->results();
                foreach($sections as $section){
                    $o = new model_form_page_section();
                    $o->load_by_data($section);
                    $section_objects[] = $o;
                    foreach($page_objects as $p){
                        if ($p->form_page_id == $o->form_page_id){
                            $p->add($o);
                            break;
                        }
                    }
                    
                    $section_ids[] = $section['form_page_section_id'];
                }
                
                /* Load section buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $section_ids) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_section_id')
                    ->order_by('b.priority');
                
                $section_buttons = $sql->execute($sql_cache_time)->results();
                foreach($section_buttons as $button){
                    $o = new model_form_page_section_button();
                    $o->load_by_data($button);
                    
                    foreach($section_objects as $p){
                        if ($p->form_page_section_id == $o->form_page_section_id){
                            $p->add($o);
                            break;
                        }
                    }
                }
                
                /* Load section conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $section_ids) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_section_id');
                
                $section_conditions = $sql->execute($sql_cache_time)->results();
                foreach($section_conditions as $condition){
                    $o = new model_form_page_section_condition();
                    $o->load_by_data($condition);
                    
                    foreach($section_objects as $p){
                        if ($p->form_page_section_id == $o->form_page_section_id){
                            $p->add($o);
                            break;
                        }
                    }
                }
                
                /* Load groups */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group', 'g')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('g.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('g.form_page_section_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $section_ids) . ')')
                            )
                        )
                    )
                    ->order_by('g.form_page_section_id')
                    ->order_by('g.priority');
                
                $groups = $sql->execute($sql_cache_time)->results();
                foreach($groups as $group){
                    $o = new model_form_page_section_group();
                    $o->load_by_data($group);
                    $group_objects[] = $o;
                    
                    foreach($section_objects as $p){
                        if ($p->form_page_section_id == $o->form_page_section_id){
                            $p->add($o);
                            break;
                        }
                    }
                    
                    $group_ids[] = $group['form_page_section_group_id'];
                }
                    
                    
                
                /* Load group buttons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_button', 'b')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('b.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $group_ids) . ')')
                            )
                        )
                    )
                    ->order_by('b.form_page_section_group_id')
                    ->order_by('b.priority');
                
                $group_buttons = $sql->execute($sql_cache_time)->results();
                
                foreach($group_buttons as $button){
                    $o = new model_form_page_section_group_button();
                    $o->load_by_data($button);
                    
                    foreach($group_objects as $p){
                        if ($p->form_page_section_group_id == $o->form_page_section_group_id){
                            $p->add($o);
                            break;
                        }
                    }
                }
                
                /* Load group conditions */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_condition', 'c')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('c.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $group_ids) . ')')
                            )
                        )
                    )
                    ->order_by('c.form_page_section_group_id');
                
                $group_conditions = $sql->execute($sql_cache_time)->results();
                foreach($group_conditions as $condition){
                    $o = new model_form_page_section_group_condition();
                    $o->load_by_data($condition);
                    
                    foreach($group_objects as $p){
                        if ($p->form_page_section_group_id == $o->form_page_section_group_id){
                            $p->add($o);
                            break;
                        }
                    }
                }
                //print new html_pre('Group conditions:' . print_r($group_conditions, true));
                
                /* Load fields */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_field', 'f')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('f.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('f.form_page_section_group_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $group_ids) . ')')
                            )
                        )
                    )
                    ->order_by('f.form_page_section_group_id')
                    ->order_by('f.priority');
                
                $fields = $sql->execute($sql_cache_time)->results();
                foreach($fields as $field){
                    $o = new model_form_page_section_group_field();
                    $o->load_by_data($field);
                    $field_objects[] = $o;
                    
                    foreach($group_objects as $p){
                        if ($p->form_page_section_group_id == $o->form_page_section_group_id){
                            $p->add($o);
                            break;
                        }
                    }
                    
                    $field_ids[] = $field['form_page_section_group_field_id'];
                }
                
                /* Load field addons */
                $sql = $this->data_source->sql;
                $sql->select('*')
                    ->from('form_page_section_group_field_addon', 'a')
                    ->where(
                        new \adapt\sql_and(
                            new \adapt\sql_condition(
                                $this->data_source->sql('a.date_deleted'),
                                'is',
                                $this->data_source->sql('null')
                            ),
                            new \adapt\sql_condition(
                                $this->data_source->sql('a.form_page_section_group_field_id'),
                                'in',
                                $this->data_source->sql('(' . implode(",", $field_ids) . ')')
                            )
                        )
                    )
                    ->order_by('a.form_page_section_group_field_id')
                    ->order_by('a.priority');
                
                $field_addons = $sql->execute($sql_cache_time)->results();
                
                foreach($field_addons as $addon){
                    $o = new model_form_page_section_group_field_addon();
                    $o->load_by_data($addon);
                    
                    foreach($field_objects as $p){
                        if ($p->form_page_section_group_field_id == $o->form_page_section_group_field_id){
                            $p->add($o);
                            break;
                        }
                    }
                }
            }
            
            return $return;
        }
        
        public function get_view($user_data = array()){
            //$user_data = $this->convert_user_data($user_data);
            //return new html_pre(print_r($this->response, true));
            if ($this->is_loaded){
                
                $errors = array();
                if ($response = $this->response[$this->name]){
                    if (is_array($response) && is_array($response['errors'])){
                        $errors = array_merge($errors, $response['errors']);
                        
                        $response = $this->response['request'];
                        if (is_array($response)){
                            $user_data = array_merge($response, $user_data);
                        }
                        
                        $user_data = array_merge($this->request, $user_data);
                    }
                }
                
                //TODO: Handle custom view
                
                /* Load button styles */
                $button_styles = $this->data_source->sql
                    ->select('*')
                    ->from('form_button_style')
                    ->where(
                        new \adapt\sql_condition(
                            new \adapt\sql('date_deleted'),
                            'is',
                            new \adapt\sql('null')
                        )
                    )
                    ->execute(60 * 60 * 12)
                    ->results();
                
                /* Load section layouts */
                $section_layouts = $this->data_source->sql
                    ->select('*')
                    ->from('form_page_section_layout')
                    ->where(
                        new \adapt\sql_condition(
                            new \adapt\sql('date_deleted'),
                            'is',
                            new \adapt\sql('null')
                        )
                    )
                    ->execute(60 * 60 * 12)
                    ->results();
                    
                /* Load group layouts */
                $group_layouts = $this->data_source->sql
                    ->select('*')
                    ->from('form_page_section_group_layout')
                    ->where(
                        new \adapt\sql_condition(
                            new \adapt\sql('date_deleted'),
                            'is',
                            new \adapt\sql('null')
                        )
                    )
                    ->execute(60 * 60 * 12)
                    ->results();
                
                /* Load field types */
                $field_types = $this->data_source->sql
                    ->select('*')
                    ->from('form_field_type')
                    ->where(
                        new \adapt\sql_condition(
                            new \adapt\sql('date_deleted'),
                            'is',
                            new \adapt\sql('null')
                        )
                    )
                    ->execute(60 * 60 * 12)
                    ->results();
                
                /* Create a form view */
                $view = null;
                
                if (isset($this->custom_view) && trim($this->custom_view) != ''){
                    $class = $this->custom_view;
                    $view = new $class($this->form_data, $user_data);
                }else{
                    $view = new view_form($this->_form_data['form'], $user_data);
                }
                
                /* Add the pages */
                foreach($this->_form_data['form_page'] as $page){
                    //print new html_pre(print_r($user_data, true));
                    $view->add(new view_form_page($page, $user_data, $errors));
                }
                
                /* Add the buttons to the page */
                foreach($this->_form_data['form_page_button'] as $button){
                    //$view->add(new html_pre(print_r($button, true)));
                    $page = $view->find("[data-form-page-id='{$button['form_page_id']}']");
                    
                    if ($page->size() > 0){
                        $page = $page->get(0);
                        
                        $button_view = null;
                        $style = null;
                        
                        /* Get the style */
                        foreach($button_styles as $button_style){
                            if ($button_style['form_button_style_id'] == $button['form_button_style_id']){
                                $style = $button_style;
                                break;
                            }
                        }
                        
                        
                        
                        if (isset($button['custom_view']) && trim($button['custom_view']) != ""){
                            $class = $button['custom_view'];
                            if (class_exists($class)){
                                $button_view = new $class($button, $style);
                            }
                        }else{
                            $button_view = new view_form_page_button($button, $style);
                        }
                            
                        $page->add_control($button_view);
                    }
                }
                
                /* Add page conditions */
                foreach($this->_form_data['form_page_condition'] as $condition){
                    $page = $view->find("[data-form-page-id='{$condition['form_page_id']}']");
                    
                    if ($page->size() > 0){
                        $page->add_condition(new view_form_page_condition($condition, $user_data));
                    }
                }
                
                /* Add sections */
                foreach($this->_form_data['form_page_section'] as $section){
                    $page = $view->find("[data-form-page-id='{$section['form_page_id']}']");
                    
                    if ($page->size() > 0){
                        $class = $section['custom_view'];
                        $section_view = null;
                        
                        if (class_exists($class)){
                            $section_view = new $class($section, $user_data);
                        }else{
                            $section_view = new view_form_page_section($section, $user_data);
                        }
                        
                        $page = $page->get(0);
                        $page->add($section_view);
                        
                        /* Add layout engine */
                        foreach($section_layouts as $layout){
                            if ($layout['form_page_section_layout_id'] == $section['form_page_section_layout_id']){
                                $class = $layout['custom_view'];
                                
                                if (class_exists($class)){
                                    $section_view->add_layout_engine(new $class($layout));
                                }
                                
                                break;
                            }
                        }
                    }
                }
                /* Add section controls */
                foreach($this->_form_data['form_page_section_button'] as $button){
                    $section = $view->find("[data-form-page-section-id='{$button['form_page_section_id']}']");
                    
                    if ($section->size() > 0){
                        $section = $section->get(0);
                        
                        $button_view = null;
                        $style = null;
                        
                        /* Get the style */
                        foreach($button_styles as $button_style){
                            if ($button_style['form_button_style_id'] == $button['form_button_style_id']){
                                $style = $button_style;
                                break;
                            }
                        }
                        
                        
                        
                        if (isset($button['custom_view']) && trim($button['custom_view']) != ""){
                            $class = $button['custom_view'];
                            if (class_exists($class)){
                                $button_view = new $class($button, $style);
                            }
                        }else{
                            $button_view = new view_form_page_section_button($button, $style);
                        }
                            
                        $section->add_control($button_view);
                    }
                }
                
                /* Add section conditions */
                foreach($this->_form_data['form_page_section_condition'] as $condition){
                    $section = $view->find("[data-form-page-section-id='{$condition['form_page_section_id']}']");
                    
                    if ($section->size() > 0){
                        $section->add_condition(new view_form_page_section_condition($condition, $user_data));
                    }
                }
                
                /* Build groups */
                $group_container = new html_div(); //Temp container to hold the group until it's fully built
                
                foreach($this->_form_data['form_page_section_group'] as $group){
                    $group_view = new view_form_page_section_group($group, $user_data);
                    
                    /* Add the layout engine */
                    foreach($group_layouts as $layout){
                        if ($layout['form_page_section_group_layout_id'] == $group['form_page_section_group_layout_id']){
                            $class = $layout['custom_view'];
                            
                            if (class_exists($class)){
                                $group_view->add_layout_engine(new $class($layout));
                            }
                            
                            break;
                        }
                    }
                    
                    $group_container->add($group_view);
                }
                
                /* Add group controls */
                foreach($this->_form_data['form_page_section_group_button'] as $button){
                    $group = $group_container->find("[data-form-page-section-group-id='{$button['form_page_section_group_id']}']");
                    
                    if ($group->size() > 0){
                        $group = $group->get(0);
                        
                        $button_view = null;
                        $style = null;
                        
                        /* Get the style */
                        foreach($button_styles as $button_style){
                            if ($button_style['form_button_style_id'] == $button['form_button_style_id']){
                                $style = $button_style;
                                break;
                            }
                        }
                        
                        
                        
                        if (isset($button['custom_view']) && trim($button['custom_view']) != ""){
                            $class = $button['custom_view'];
                            if (class_exists($class)){
                                $button_view = new $class($button, $style);
                            }
                        }else{
                            $button_view = new view_form_page_section_group_button($button, $style);
                        }
                            
                        $group->add_control($button_view);
                    }
                }
                
                /* Add group conditions */
                foreach($this->_form_data['form_page_section_group_condition'] as $condition){
                    //print new html_pre(print_r($condition, true));
                    $group = $group_container->find("[data-form-page-section-group-id='{$condition['form_page_section_group_id']}']");
                    
                    if ($group->size() > 0){
                        $group = $group->get(0);
                        $group->add_condition(new view_form_page_section_group_condition($condition, $user_data));
                    }
                }
                
                /* Add fields */
                foreach($this->_form_data['form_page_section_group_field'] as $field){
                    $group = $group_container->find("[data-form-page-section-group-id='{$field['form_page_section_group_id']}']");
                    
                    if ($group->size() > 0){
                        $group = $group->get(0);
                        
                        $field_view = null;
                        $data_type = $this->data_source->get_data_type($field['data_type_id']);
                        
                        if ($field['allowed_values'] && trim($field['allowed_values']) != ""){
                            $field['allowed_values'] = json_decode($field['allowed_values'], true);
                        }elseif($field['lookup_table'] && trim($field['lookup_table']) != ""){
                            
                            /* Get the schema for the lookup table */
                            $struct = $this->data_source->get_row_structure($field['lookup_table']);
                            
                            if (count($struct)){
                                /* Do we have a label, name / date deleted field? */
                                $has_date_deleted = false;
                                $has_label = false;
                                $has_name = false;
                                $id_field = null;
                                $label_field = null;
                                
                                foreach($struct as $f){
                                    if ($f['field_name'] == 'date_deleted') $has_date_deleted = true;
                                    if ($f['field_name'] == 'label') $has_label = true;
                                    if ($f['field_name'] == 'name') $has_name = true;
                                    if ($f['primary_key'] == 'Yes') $id_field = $f['field_name'];
                                }
                                
                                if (!is_null($id_field) && ($has_label || $has_name)){
                                    if ($has_label){
                                        $label_field = 'label';
                                    }else{
                                        $label_field = 'name';
                                    }
                                    
                                    /* Build the query */
                                    $sql = $this->data_source->sql;
                                    
                                    $sql->select(array(
                                        'lookup_id' => $this->data_source->sql($id_field),
                                        'label' => $this->data_source->sql($label_field)
                                    ))
                                    ->from($field['lookup_table']);
                                    
                                    if ($has_date_deleted){
                                        $sql->where(
                                            new \adapt\sql_condition(
                                                $this->data_source->sql('date_deleted'),
                                                'is',
                                                $this->data_source->sql('null')
                                            )
                                        );
                                    }
                                    
                                    if ($label_field == 'label'){
                                        $sql->order_by('label');
                                    }
                                    
                                    $field['allowed_values'] = \adapt\view_select::sql_result_to_assoc($sql->execute()->results());
                                }
                                
                                
                                
                                //$group->add(new html_pre(print_r($struct, true)));
                            }
                        }
                        
                        if (isset($field['custom_view'])){
                            $class = $field['custom_view'];
                            if (class_exists($class)){
                                $field_view = new $class($field, $data_type, $user_data);
                            }
                        }
                        
                        if (is_null($field_view)){
                            /* Get the field type */
                            $field_type = null;
                            
                            foreach($field_types as $type){
                                if ($type['form_field_type_id'] == $field['form_field_type_id']){
                                    $field_type = $type;
                                    break;
                                }
                            }
                            
                            if (isset($field_type) && isset($field_type['view'])){
                                $class = $field_type['view'];
                                //print "{$class}||";
                                if (class_exists($class)){
                                    $field_view = new $class($field, $data_type, $user_data);
                                }
                            }
                        }
                        
                        if ($field_view) $group->add($field_view);
                    }
                }
                
                
                /* Add add-ons to fields */
                foreach($this->_form_data['form_page_section_group_field_addon'] as $addon){
                    $field = $group_container->find("[data-form-page-section-group-field-id='{$addon['form_page_section_group_field_id']}']");
                    
                    if ($field->size()){
                        $field = $field->get(0);
                        
                        switch($addon['type']){
                        case 'Icon':
                            if (isset($addon['icon_class']) && isset($addon['icon_name'])){
                                $class = $addon['icon_class'];
                                if (class_exists($class)){
                                    $icon = new $class($addon['icon_name']);
                                    if ($icon instanceof \adapt\html){
                                        $field->add_addon(new html_span($icon, array('class' => 'input-group-addon', 'data-form-page-section-group-field-addon-id' => $addon['form_page_section_group_field_addon_id'])), $addon['position'] == 'Before' ? true : false);
                                    }
                                }
                            }
                            break;
                        case "Text":
                            if (isset($addon['label'])){
                                $field->add_addon(new html_span($addon['label'], array('class' => 'input-group-addon', 'data-form-page-section-group-field-addon-id' => $addon['form_page_section_group_field_addon_id'])), $addon['position'] == 'Before' ? true : false);
                            }
                            break;
                        case "Button":
                            $button = new html_button(array('class' => 'btn btn-default ' . $addon['name']));
                            if (isset($addon['icon_class']) && isset($addon['icon_name'])){
                                $class = $addon['icon_class'];
                                if (class_exists($class)){
                                    $icon = new $class($addon['icon_name']);
                                    if ($icon instanceof \adapt\html){
                                        $button->add($icon);
                                        $button->add(' ');
                                    }
                                }
                            }
                            
                            if (isset($addon['label'])){
                                $button->add($addon['label']);
                            }
                            
                            $field->add_addon(new html_span($button, array('class' => 'input-group-btn', 'data-form-page-section-group-field-addon-id' => $addon['form_page_section_group_field_addon_id'])), $addon['position'] == 'Before' ? true : false);
                            break;
                        case "Radio":
                            $radio = new html_input(array('type' => 'radio', 'name' => $addon['name'], 'value' => $addon['default_value']));
                            $field->add_addon(new html_span($radio, array('class' => 'input-group-addon', 'data-form-page-section-group-field-addon-id' => $addon['form_page_section_group_field_addon_id'])), $addon['position'] == 'Before' ? true : false);
                            break;
                        case "Checkbox":
                            $checkbox = new html_input(array('type' => 'checkbox', 'name' => $addon['name'], 'value' => $addon['default_value']));
                            $field->add_addon(new html_span($checkbox, array('class' => 'input-group-addon', 'data-form-page-section-group-field-addon-id' => $addon['form_page_section_group_field_addon_id'])), $addon['position'] == 'Before' ? true : false);
                            break;
                        case "Select":
                            if ($addon['allowed_values'] && trim($addon['allowed_values']) != ""){
                                $addon['allowed_values'] = json_decode($addon['allowed_values'], true);
                                
                            }elseif($addon['lookup_table'] && trim($addon['lookup_table']) != ""){
                                /* Get the schema for the lookup table */
                                $struct = $this->data_source->get_row_structure($addon['lookup_table']);
                                
                                if (count($struct)){
                                    /* Do we have a label, name / date deleted field? */
                                    $has_date_deleted = false;
                                    $has_label = false;
                                    $has_name = false;
                                    $id_field = null;
                                    $label_field = null;
                                    
                                    foreach($struct as $f){
                                        if ($f['field_name'] == 'date_deleted') $has_date_deleted = true;
                                        if ($f['field_name'] == 'label') $has_label = true;
                                        if ($f['field_name'] == 'name') $has_name = true;
                                        if ($f['primary_key'] == 'Yes') $id_field = $f['field_name'];
                                    }
                                    
                                    if (!is_null($id_field) && ($has_label || $has_name)){
                                        if ($has_label){
                                            $label_field = 'label';
                                        }else{
                                            $label_field = 'name';
                                        }
                                        
                                        /* Build the query */
                                        $sql = $this->data_source->sql;
                                        
                                        $sql->select(array(
                                            'lookup_id' => $this->data_source->sql($id_field),
                                            'label' => $this->data_source->sql($label_field)
                                        ))
                                        ->from($addon['lookup_table']);
                                        
                                        if ($has_date_deleted){
                                            $sql->where(
                                                new sql_cond(
                                                    'date_deleted',
                                                    sql::IS,
                                                    new sql_null()
                                                )
                                            );
                                        }
                                        
                                        if ($label_field == 'label'){
                                            $sql->order_by('label');
                                        }
                                        
                                        $addon['allowed_values'] = \adapt\view_select::sql_result_to_assoc($sql->execute()->results());
                                    }
                                    
                                    $value = $addon['default_value'];
                                    $key = $addon['name'];
                                    if ($user_data[$key]){
                                        $value = $user_data[$key];
                                    }
                                    if (!$value){
                                        $keys = array_keys($addon['allowed_values']);
                                        if (is_array($keys) && count($keys)){
                                            $value = $keys[0];
                                        }
                                    }
                                    
                                    $select = new view_dropdown_select($addon['name'], $addon['allowed_values'], $value);
                                    $select->remove_class('dropdown');
                                    $select->add_class('input-group-btn');
                                    $select->attr('ata-form-page-section-group-field-addon-id', $addon['form_page_section_group_field_addon_id']);
                                    $field->add_addon($select, $addon['position'] == 'Before' ? true : false);
                                    //$group->add(new html_pre(print_r($struct, true)));
                                }
                            }
                            break;
                        }
                    }
                }
                
                /* Add groups to sections */
                $groups = $group_container->get();
                foreach($groups as $group){
                    $section = $view->find(".form-page-section[data-form-page-section-id='" . $group->attr('data-form-page-section-id') . "']");
                    
                    if ($section->size() > 0){
                        $section = $section->get(0);
                        
                        $section->add($group);
                    }
                }
                
                
                
                //$view = new html_pre(print_r($this->_form_data, true));
                return $view;
                
                //$actions = split(",", $this->actions);
                //$errors = array();
                //
                //foreach($actions as $action){
                //    $response = $this->response[$action];
                //    if (is_array($response) && is_array($response['errors'])){
                //        $errors = array_merge($errors, $response['errors']);
                //        
                //        $response = $this->response['request'];
                //        if (is_array($response)){
                //            $user_data = array_merge($response, $user_data);
                //        }
                //        
                //        $user_data = array_merge($this->request, $user_data);
                //    }
                //}
                
                $view = null;
                
                if (isset($this->custom_view) && trim($this->custom_view) != ''){
                    $class = $this->custom_view;
                    $view = new $class($this->form_data, $user_data);
                }else{
                    $view = new view_form($this->form_data, $user_data);
                }
                
                //if ($view && $view instanceof \frameworks\adapt\html){
                //    
                //    for($i = 0; $i < $this->count(); $i++){
                //        $child = $this->get($i);
                //        if (is_object($child) && $child instanceof \frameworks\adapt\model && $child->table_name == 'form_page'){
                //            $view->add($child->get_view($user_data, $errors));
                //        }
                //    }
                //}
                
                return $view;
            }
            
            return null;
        }
        
        public function convert_user_data($user_data){
            $output = array();
            
            foreach($user_data as $name => $values){
                $key = $name;
                if (is_array($values) && is_assoc($values)){
                    foreach($values as $field => $value){
                        if (is_array($value)){
                            foreach($value as $v){
                                $key = "{$name}[$field][]";
                                $output[] = array('key' => $key, 'value' => $v, 'used' => false);
                            }
                        }else{
                            $key = "{$name}[$field]";
                            $output[] = array('key' => $key, 'value' => $value, 'used' => false);
                        }
                    }
                }else{
                    $output[] = array('key' => $key, 'value' => $values, 'used' => false);
                }
            }
            
            return $output;
        }

    }
    
}

?>