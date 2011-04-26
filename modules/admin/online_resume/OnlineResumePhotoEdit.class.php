<?php
/**
* The management page for the user photo of the Online Resume module.
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
class OnlineResumePhotoEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Photo Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Photo Edit</h2><br />');
        
        $photo_data = db()->getRow("
            SELECT
                general_information_id,
                photo
            FROM online_resume.general_information
            WHERE general_information_id = 1
        ");
        
        if(!empty($photo_data)) {
            /* ----- The photo form -----*/
            $photo_form = new Form('photo_form');
            
            $image_path = "{$this->assets_path}/modules/online_resume/images";
            
            $photo_form->addSingleImageField('photo', 'Photo', $image_path, 50);
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
            
                db()->update('online_resume.general_information', $table_data, array('general_information_id' => 1));
                
                $photo_form->addError('Your images has been successfully uploaded.');
            }
            
            $content->addChild($photo_form);
        }
        else {
            $general_information_edit_url = Http::getPageBaseUrl() . 'OnlineResumeGeneralInformationEdit';
        
            $content->addParagraph("
                Your general information needs to be added before managing your photo. Go <a href=\"{$general_information_edit_url}\">here</a> to manage your general information.
            ");
        }
        
        $this->body->addChild($content);
    }
}