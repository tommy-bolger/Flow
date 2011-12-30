<?php
/**
* The management page for the general user information of the Online Resume module.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class OnlineResumeGeneralInformationEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume General Information Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>General Information Edit</h2><br />');
        
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
            FROM online_resume.general_information
            WHERE general_information_id = 1
        ");
        
        $general_information_form = new Form('general_information_form');
        
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
                db()->update('online_resume.general_information', $table_data, array('general_information_id' => 1));
            }
            else {
                $table_data['general_information_id'] = 1;
            
                db()->insert('online_resume.general_information', $table_data);
            }
            
            $general_information_form->addError('Your information has been updated.');
        }
        
        $content->addChild($general_information_form);
        
        $this->body->addChild($content);
    }
}