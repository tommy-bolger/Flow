<?php
/**
* The management page for the images of a portfolio project in the Online Resume module.
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
class OnlineResumePortfolioImagesEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Portfolio Project Images Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $portfolio_images_table = new EditTableForm(
            'portfolio_images',
            'online_resume.portfolio_project_images',
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
        
        $image_path = "{$this->images_path}/portfolio_images";
        
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
            FROM online_resume.portfolio_project_images
            WHERE portfolio_project_id = ?
            ORDER BY sort_order ASC
        ", array($portfolio_project_id));
        
        $portfolio_image_edit_base_url = Http::getPageBaseUrl() . "OnlineResumePortfolioImageEdit";
        
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
            FROM online_resume.portfolio_projects
            WHERE portfolio_project_id = ?
        ", array($portfolio_project_id));
        
        $portfolio_edit_page_url = Http::getPageBaseUrl() . "OnlineResumePortfolioEdit";
        
        $content = new Div(array('id' => 'current_menu_content'), "
            <h2>Editing Skills for Portfolio Project {$project_name}</h2><br />
            <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Projects</a><br /><br />
        ");
        
        $content->addChild($portfolio_images_table);
        
        $this->body->addChild($content);
    }
}