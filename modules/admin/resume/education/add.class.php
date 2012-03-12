<?php
/**
* The management page for the user education of the Online Resume module.
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

namespace Modules\Admin\Resume\Education;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Education";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('add');
    }
    
    protected function constructRightContent() {
        $education_id = request()->get->education_id;
    
        //The education history table
        $education_form = new EditTableForm('education_history', 'resume_education', 'education_id', 'sort_order');
        
        $education_form->setTitle('Add a New Education Institution');
        
        $degree_levels = db()->getConcatMappedColumn("
            SELECT 
                degree_level_id, 
                abbreviation,
                degree_level_name
            FROM resume_degree_levels
        ", ' - ');
        
        $education_form->addTextbox('institution_name', 'Institution Name');     
        $education_form->addTextbox('institution_city', 'City');        
        $education_form->addStateSelect('state_id', 'State')->addBlankOption(); 
        $education_form->addDropdown('degree_level_id', 'Degree Level', $degree_levels)->addBlankOption();
        $education_form->addTextbox('degree_name', 'Degree Name');
        $education_form->addDate('date_graduated', 'Graduation Date');        
        $education_form->addFloatField('cumulative_gpa', 'Cumulative GPA')->setPrecision(1, 2);
        $education_form->addSubmit('save', 'Save');
        
        if(!empty($education_id)) {
            $education_record = db()->getRow("
                SELECT 
                    institution_name,
                    institution_city,
                    state_id,
                    degree_level_id,
                    degree_name,
                    date_graduated,
                    cumulative_gpa
                FROM resume_education
                WHERE education_id = ?
            ", array($education_id));
            
            $education_form->setDefaultValues($education_record);
        }
        
        $education_form->setRequiredFields(array(
            'institution_name',
            'institution_city',
            'state_id',
            'degree_level_id',
            'degree_name',
            'date_graduated',
            'cumulative_gpa'
        ));
        
        $education_form->processForm();
        
        $this->body->addChild($education_form, 'current_menu_content');
    }
}