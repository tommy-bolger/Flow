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

namespace Modules\Admin\Resume;

use \Framework\Html\Misc\Div;
use \Framework\Html\Form\Form;
use \Framework\Utilities\Http;
use \Framework\Utilities\File;
use \Framework\Utilities\Image;

class PortfolioImageEdit
extends PortfolioImagesEdit {
    protected $title = "Resume Portfolio Project Image Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Portfolio Project Image Edit'] = Http::getCurrentBaseUrl() . 'portfolio-image-edit';
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'));

        request()->get->setRequired(array('portfolio_project_image_id'));
        
        $portfolio_project_image_id = request()->get->portfolio_project_image_id;
        
        $image_data = db()->getRow("
            SELECT 
                portfolio_project_image_id,
                image_name
            FROM resume_portfolio_project_images
            WHERE portfolio_project_image_id = ?
        ", array($portfolio_project_image_id));
        
        if(!empty($image_data)) {
            /* ----- The image form -----*/
            $image_form = new Form('image_form');
            
            $image_path = "{$this->managed_module->getImagesPath()}/portfolio_images";
            
            $image_form->addSingleImage('image_name', 'Image', $image_path, 200);
            $image_form->addSubmit('save', 'Save');
    
            $image_form->setDefaultValues(array('image_name' => $image_data['image_name']));
            $image_form->setRequiredFields(array('image_name'));
            
            if($image_form->wasSubmitted() && $image_form->isValid()) {
                $form_data = $image_form->getData();
                
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
                
                $image_form->addError('Your image has been successfully changed.');
            }
            
            $project_image_data = db()->getRow("
                SELECT 
                    title,
                    portfolio_project_id
                FROM resume_portfolio_project_images
                WHERE portfolio_project_image_id = ?
            ", array($portfolio_project_image_id));
            
            $portfolio_edit_page_url = Http::getCurrentBaseUrl() . "portfolio-images-edit&portfolio_project_id={$project_image_data['portfolio_project_id']}";
            
            $content->setText("
                <h2>Changing Image File for Portfolio Project Image {$project_image_data['title']}</h2><br />
                <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Project Images</a><br /><br />
            ");
            
            $content->addChild($image_form);
        }
        else {
            $portfolio_edit_url = Http::getCurrentBaseUrl() . 'portfolio-edit';
        
            $content->addChild("
                <p>
                    The specified portfolio_project_image_id is not associated with a valid portfolio project. Go <a href=\"{$portfolio_edit_url}\">here</a> to manage your portfolio projects.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}