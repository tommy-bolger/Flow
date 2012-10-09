<?php
/**
* The management page for the general user information of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\GeneralInformation;

use \Framework\Html\Form\TableForm;
use \Framework\Utilities\Http;

class Edit
extends Home {
    protected $title = "Edit General Information";
    
    protected $active_sub_nav_link = 'Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Edit'] = Http::getCurrentLevelPageUrl('edit', array(), 'resume');
    }
    
    protected function constructRightContent() {                
        $this->page->body->addChild($this->getForm(), 'current_menu_content');
    }
    
    protected function getForm() {
        $general_information = db()->getRow("
            SELECT
                first_name,
                last_name,
                address,
                city,
                state_id AS state_select,
                phone_number,
                email_address,
                specialty,
                summary
            FROM resume_general_information
            WHERE general_information_id = 1
        ");
        
        $general_information_form = new TableForm('general_information_form');
        
        $general_information_form->setTitle('General Information Edit');
        
        $general_information_form->addTextbox('first_name', 'First Name');
        $general_information_form->addTextbox('last_name', 'Last Name');
        $general_information_form->addTextbox('address', 'Street Address');
        $general_information_form->addTextbox('city', 'City');
        $general_information_form->addStateSelect('state_select', 'State')->addBlankOption();
        $general_information_form->addPhoneNumber('phone_number', 'Phone Number');
        $general_information_form->addSplitEmail('email_address', 'Email Address');
        $general_information_form->addTextbox('specialty', 'Specialty');
        $general_information_form->addTextArea('summary', 'Summary');
        
        $general_information_form->addSubmit('save', 'Save');
        
        $general_information_form->setRequiredFields(array(
            'first_name',
            'last_name',
            'city',
            'state_select',
            'email_address',
            'summary',
            'specialty'
        ));

        if(!empty($general_information)) {
            $general_information_form->setDefaultValues($general_information);
        }

        if($general_information_form->wasSubmitted() && $general_information_form->isValid()) {
            $form_data = $general_information_form->getData();
            
            $table_data = array(
                'first_name' => $form_data['first_name'],
                'last_name' => $form_data['last_name'],
                'address' => $form_data['address'],
                'city' => $form_data['city'],
                'state_id' => $form_data['state_select'],
                'phone_number' => $form_data['phone_number'],
                'email_address' => $form_data['email_address'],
                'summary' => $form_data['summary'],
                'specialty' => $form_data['specialty']
            );

            if(!empty($general_information)) {            
                db()->update('resume_general_information', $table_data, array('general_information_id' => 1));
            }
            else {
                $table_data['general_information_id'] = 1;
            
                db()->insert('resume_general_information', $table_data);
            }
            
            $general_information_form->addConfirmation('Your information has been updated.');
        }
        
        return $general_information_form;
    }
}