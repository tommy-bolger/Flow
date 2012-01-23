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

namespace Modules\Admin\Resume;

use \Framework\Utilities\File;
use \Framework\Utilities\Http;
use \Framework\Html\Form\Form;
use \Framework\Html\Misc\Div;

class PhotoEdit
extends Home {
    protected $title = "Resume Photo Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Photo'] = Http::getCurrentLevelPageUrl('photo-edit');
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Photo Edit</h2><br />');
        
        $photo_data = db()->getRow("
            SELECT
                general_information_id,
                photo
            FROM resume_general_information
            WHERE general_information_id = 1
        ");
        
        if(!empty($photo_data)) {
            /* ----- The photo form -----*/
            $photo_form = new Form('photo_form');
            
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
                
                $photo_form->addError('Your images has been successfully uploaded.');
            }
            
            $content->addChild($photo_form);
        }
        else {
            $general_information_edit_url = Http::getCurrentLevelPageUrl('general-information-edit');
        
            $content->addChild("
                <p>
                    Your general information needs to be added before managing your photo. Go <a href=\"{$general_information_edit_url}\">here</a> to manage your general information.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}