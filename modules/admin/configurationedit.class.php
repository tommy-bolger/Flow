<?php
/**
* The management page for a configuration.
* Copyright (c) 2011, Tommy Bolger
* All rights reserved.
* 
* Redistribution and use in source and binary forms, with or without 
* modification, are permitted provided that the following conditions 
* are met:
* 
* Redistributions of source code must retain the above copyright 
* notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright 
* notice, this list of conditions and the following disclaimer in the 
* documentation and/or other materials provided with the distribution.
* Neither the name of the author nor the names of its contributors may 
* be used to endorse or promote products derived from this software 
* without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
* COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
* POSSIBILITY OF SUCH DAMAGE.
*/

namespace Modules\Admin;

use \Framework\Utilities\Http;
use \Framework\Html\Misc\Div;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\Fields\BooleanCheckbox;
use \Framework\Html\Form\Fields\IntField;
use \Framework\Html\Form\Fields\FloatField;
use \Framework\Html\Form\Fields\Textbox;
use \Framework\Html\Form\Fields\Dropdown;

class ConfigurationEdit
extends Home {
    protected $title = "Configuration Settings";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {        
        $this->page_links = session()->module_path;
        
        $this->page_links[$this->title] = Http::getCurrentBaseUrl() . 'configuration-edit';
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Configuration Settings</h2><br />');
        
        $module_id = request()->get->module_id;
        
        $module_where_clause = '';
        $module_placeholder_values = array();
        
        if(empty($module_id)) {
            $module_id = NULL;
        }
        else {
            $module_placeholder_values[] = $module_id;
        }
        
        $module_where_clause = db()->generateWhereClause(array('module_id' => $module_id));
        
        $parameters = db()->getAll("
            SELECT 
                cp.configuration_parameter_id,
                cp.parameter_name,
                cp.display_name,
                COALESCE(cp.value, cp.default_value) AS value,
                pdt.data_type,
                cp.has_value_list
            FROM cms_configuration_parameters cp
            JOIN cms_parameter_data_types pdt USING (parameter_data_type_id)
            {$module_where_clause}
            ORDER BY cp.sort_order
        ", $module_placeholder_values);

        if(!empty($parameters)) {
            $configuration_form = new Form('configuration_form');
            
            foreach($parameters as $parameter) {
                $parameter_id = $parameter['configuration_parameter_id'];
                $parameter_name = $parameter['parameter_name'];
                $display_name = $parameter['display_name'];
                
                $parameter_field = NULL;
            
                if(empty($parameter['has_value_list'])) {
                    $data_type = $parameter['data_type'];
                
                    switch($data_type) {
                        case 'boolean':
                            $parameter_field = new BooleanCheckbox($parameter_id, $display_name);
                            break;
                        case 'integer':
                            $parameter_field = new IntField($parameter_id, $display_name);
                            break;
                        case 'float':
                            $parameter_field = new FloatField($parameter_id, $display_name);
                            break;
                        default:
                            $parameter_field = new Textbox($parameter_id, $display_name);
                            break;
                    }
                }
                else {
                    $parameter_options = db()->getMappedColumn("
                        SELECT 
                            parameter_value AS display,
                            parameter_value AS value
                        FROM cms_parameter_values
                        WHERE configuration_parameter_id = ?
                        ORDER BY sort_order ASC
                    ", array($parameter_id));
                    
                    $parameter_field = new Dropdown($parameter_id, $display_name, $parameter_options);
                    $parameter_field->addBlankOption();
                }
                
                $parameter_field->setDefaultValue($parameter['value']);
                
                $configuration_form->addField($parameter_field);
            }
            
            $configuration_form->addSubmit('save', 'Save');
            
            if($configuration_form->wasSubmitted() && $configuration_form->isValid()) {
                $form_data = $configuration_form->getData();
                
                dump($form_data);
            }
            
            $content->addChild($configuration_form);
        }
        else {
            throw new \Exception("Module ID '{$module_id}' is not valid.");
        }
        
        $this->body->addChild($content);
    }
}