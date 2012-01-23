<?php
/**
* The management page for the images of a portfolio project in the Online Resume module.
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
use \Framework\Html\Table\EditTableForm;
use \Framework\Utilities\Http;

class PortfolioImagesEdit
extends PortfolioEdit {
    protected $title = "Resume Portfolio Project Images Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Portfolio Project Images Edit'] = Http::getCurrentLevelPageUrl('portfolio-images-edit');
    }
    
    protected function constructRightContent() {
        $portfolio_images_table = new EditTableForm(
            'portfolio_images',
            'resume_portfolio_project_images',
            'portfolio_project_image_id',
            'sort_order',
            array('portfolio_project_id')
        );
        
        $portfolio_images_table->setNumberOfColumns(4);
        
        $portfolio_images_table->addHeader(array(
            'Image',
            'title' => 'Title',
            'description' => 'Description'
        ));
        
        $image_path = "{$this->managed_module->getImagesPath()}/portfolio_images";
        
        //Get the project id these images are linked to.
        $portfolio_project_id = request()->get->portfolio_project_id;
        
        if($portfolio_images_table->getFormVisibility()) {
            $portfolio_images_table->addTextbox('title', 'Title');
            $portfolio_images_table->addTextArea('description', 'Description');
            $portfolio_images_table->addSubmit('save', 'Save');

            $portfolio_images_table->setRequiredFields(array('title'));
            
            $portfolio_images_table->processForm();
        }
        
        $portfolio_images = db()->getAll("
            SELECT
                thumbnail_name,
                title,
                description,
                portfolio_project_image_id
            FROM resume_portfolio_project_images
            WHERE portfolio_project_id = ?
            ORDER BY sort_order ASC
        ", array($portfolio_project_id));
        
        $portfolio_image_edit_base_url = Http::getCurrentLevelPageUrl("portfolio-image-edit");
        
        foreach($portfolio_images as &$portfolio_image) {
            $portfolio_image_edit_url = "{$portfolio_image_edit_base_url}&portfolio_project_image_id={$portfolio_image['portfolio_project_image_id']}";
        
            if(!empty($portfolio_image['thumbnail_name'])) {
                $portfolio_image['thumbnail_name'] = "
                    <img src=\"{$image_path}/{$portfolio_image['thumbnail_name']}\" />
                    <br />
                    <br />
                ";
            }
        
            $portfolio_image['thumbnail_name'] .= "<a href=\"{$portfolio_image_edit_url}\">Change Image</a>";
        }
        
        $portfolio_images_table->addRows($portfolio_images);
        
        $project_name = db()->getOne("
            SELECT project_name
            FROM resume_portfolio_projects
            WHERE portfolio_project_id = ?
        ", array($portfolio_project_id));
        
        $portfolio_edit_page_url = Http::getCurrentLevelPageUrl("portfolio-edit");
        
        $content = new Div(array('id' => 'current_menu_content'), "
            <h2>Editing Images for Portfolio Project {$project_name}</h2><br />
            <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Projects</a><br /><br />
        ");
        
        $content->addChild($portfolio_images_table);
        
        $this->body->addChild($content);
    }
}