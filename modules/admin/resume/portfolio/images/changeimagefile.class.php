<?php
/**
* The management page for an image of a portfolio project in the Online Resume module.
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

namespace Modules\Admin\Resume\Portfolio\Images;

use \Framework\Html\Form\TableForm;
use \Framework\Utilities\Http;
use \Framework\Utilities\File;
use \Framework\Utilities\Image;
use \Framework\Html\Misc\TemplateElement;

class ChangeImageFile
extends Home {
    protected $title = "Change Image File";
    
    protected $active_sub_nav_link = "Change Image File";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links["Change Image File"] = Http::getCurrentLevelPageUrl('change-image-file');
    }
    
    protected function constructRightContent() {
        $portfolio_project_images = db()->getAll("
            SELECT
                pp.project_name,
                ppi.portfolio_project_image_id,
                ppi.title
            FROM resume_portfolio_project_images ppi
            JOIN resume_portfolio_projects pp USING (portfolio_project_id)
            ORDER BY pp.sort_order, ppi.sort_order
        ");
        
        if(!empty($portfolio_project_images)) {
            $portfolio_project_image_id = request()->get->portfolio_project_image_id;
        
            $image_name = db()->getOne("
                SELECT image_name
                FROM resume_portfolio_project_images
                WHERE portfolio_project_image_id = ?
            ", array($portfolio_project_image_id));
            
            $image_path = "{$this->managed_module->getImagesPath()}/portfolio_images";
        
            /* ----- The image form -----*/
            $image_form = new TableForm('image_form');
            
            $image_form->setTitle("Change this Project Image");
            
            $image_options = array();
            
            foreach($portfolio_project_images as $portfolio_project_image) {
                $image_options[$portfolio_project_image['project_name']][$portfolio_project_image['portfolio_project_image_id']] = $portfolio_project_image['title'];
            } 
            
            $image_form->addDropdown('portfolio_project_image_id', 'Project Image', $image_options)->addBlankOption();
            $image_form->addSingleImage('image_name', 'Image', $image_path, 200);
            $image_form->addSubmit('save', 'Save');
    
            $image_form->setDefaultValues(array(
                'portfolio_project_image_id' => $portfolio_project_image_id,
                'image_name' => $image_name
            ));
            
            $image_form->setRequiredFields(array(
                'portfolio_project_image_id',
                'image_name'
            ));
            
            if($image_form->wasSubmitted() && $image_form->isValid()) {
                $form_data = $image_form->getData();
                
                $portfolio_project_image_id = $form_data['portfolio_project_image_id'];

                $table_data = array();
                
                if(!empty($form_data['image_name'])) {
                    $image = $form_data['image_name'];
                    
                    if(!empty($image)) {
                        $image_file_name = $image['name'];
                        
                        $table_data['image_name'] = $image_file_name;
                        
                        File::moveUpload($image, $image_path);
    
                        //Generate the thumbnail
                        $image_file_name_split = explode('.', $image_file_name);
                        
                        $thumbnail_file_name = "{$image_file_name_split[0]}_thumb";
                        $table_data['thumbnail_name'] = "{$thumbnail_file_name}.{$image_file_name_split[1]}";
                        
                        $thumbnail = new Image("{$image_path}/{$image_file_name}");
                        $thumbnail->resizeScaleByWidth(100, $image_path, $thumbnail_file_name);
                    }
                }
                else {
                    $table_data['image_name'] = NULL;
                    $table_data['thumbnail_name'] = NULL;
                }
            
                db()->update('resume_portfolio_project_images', $table_data, array('portfolio_project_image_id' => $portfolio_project_image_id));
                
                $image_form->addConfirmation('Your image has been successfully changed.');
            }
            
            $this->body->addChild($image_form, 'current_menu_content');
        }
        else {
            $portfolio_images_edit_url = Http::getCurrentLevelPageUrl('manage');
            
            $required_template = new TemplateElement('resume/required_records_warning.php');
            
            $required_template->addChild('Project Images', 'prerequisite');
            $required_template->addChild('Images', 'context');
            $required_template->addChild($portfolio_images_edit_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}