<?php
/**
* The management page for the user photo of the Online Resume module.
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

namespace Modules\Resume\Admin\GeneralInformation;

use \Framework\Utilities\File;
use \Framework\Utilities\Http;
use \Framework\Html\Form\TableForm;
use \Framework\Html\Misc\TemplateElement;

class ChangePhoto
extends Home {
    protected $title = "Change Photo";
    
    protected $active_sub_nav_link = 'Change Photo';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Change Photo'] = Http::getCurrentLevelPageUrl('change-photo', array(), 'resume');
    }
    
    protected function constructRightContent() {        
        $photo_data = db()->getRow("
            SELECT
                general_information_id,
                photo
            FROM resume_general_information
            WHERE general_information_id = 1
        ");
        
        if(!empty($photo_data)) {
            /* ----- The photo form -----*/
            $photo_form = new TableForm('photo_form');
            
            $photo_form->setTitle('Change Your Photo');
            
            $image_path = $this->managed_module->getImagesPath();
            
            $photo_form->addSingleImage('photo', 'Photo', $image_path, 50);
            $photo_form->addSubmit('save', 'Save');
            
            $photo_form->setRequiredFields(array('photo'));
    
            $photo_form->setDefaultValues(array('photo' => $photo_data['photo']));
    
            if($photo_form->wasSubmitted() && $photo_form->isValid()) {
                $form_data = $photo_form->getData();
                
                $table_data = array();
                
                if(!empty($form_data['photo'])) {
                    $photo = $form_data['photo'];
                
                    $table_data['photo'] = $photo['name'];
                
                    File::moveUpload($photo, $image_path);
                }
                else {
                    $table_data['photo'] = NULL;
                }
            
                db()->update('resume_general_information', $table_data, array('general_information_id' => 1));
                
                $photo_form->addConfirmation('Your images has been successfully uploaded.');
            }
            
            $this->body->addChild($photo_form, 'current_menu_content');
        }
        else {
            $general_information_edit_url = Http::getCurrentLevelPageUrl('edit', array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Your General Information', 'prerequisite');
            $required_template->addChild('Photo', 'context');
            $required_template->addChild($general_information_edit_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}