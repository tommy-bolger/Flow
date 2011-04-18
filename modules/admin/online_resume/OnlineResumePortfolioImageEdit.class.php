<?php
/**
* The management page for an image of a portfolio project in the Online Resume module.
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
class OnlineResumePortfolioImageEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Portfolio Project Image Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'));
        
        request()->get->setRequired(array('portfolio_project_image_id'));
        
        $portfolio_project_image_id = request()->get->portfolio_project_image_id;
        
        $image_data = db()->getRow("
            SELECT 
                portfolio_project_image_id,
                image_name
            FROM online_resume.portfolio_project_images
            WHERE portfolio_project_image_id = ?
        ", array($portfolio_project_image_id));
        
        if(!empty($image_data)) {
            /* ----- The image form -----*/
            $image_form = new Form('image_form');
            
            $image_path = "{$this->images_path}/portfolio_images";
            
            $image_form->addSingleImageField('image_name', 'Image', $image_path, 150);
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
            
                db()->update('online_resume.portfolio_project_images', $table_data, array('portfolio_project_image_id' => $portfolio_project_image_id));
                
                $image_form->addError('Your image has been successfully changed.');
            }
            
            $project_image_data = db()->getRow("
                SELECT 
                    title,
                    portfolio_project_id
                FROM online_resume.portfolio_project_images
                WHERE portfolio_project_image_id = ?
            ", array($portfolio_project_image_id));
            
            $portfolio_edit_page_url = Http::getPageBaseUrl() . "OnlineResumePortfolioImagesEdit&portfolio_project_id={$project_image_data['portfolio_project_id']}";
            
            $content->setText("
                <h2>Changing Image File for Portfolio Project Image {$project_image_data['title']}</h2><br />
                <a href=\"{$portfolio_edit_page_url}\"><- Return to Portfolio Project Images</a><br /><br />
            ");
            
            $content->addChild($image_form);
        }
        else {
            $portfolio_edit_url = Http::getPageBaseUrl() . 'OnlineResumePortfolioEdit';
        
            $content->addParagraph("
                The specified portfolio_project_image_id is not associated with a valid portfolio project. Go <a href=\"{$portfolio_edit_url}\">here</a> to manage your portfolio projects.
            ");
        }
        
        $this->body->addChild($content);
	}
}