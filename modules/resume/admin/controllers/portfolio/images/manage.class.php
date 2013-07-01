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

namespace Modules\Resume\Admin\Controllers\Portfolio\Images;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;

class Manage
extends Home {
    protected $title = "Manage Project Images";
    
    protected $active_sub_nav_link = "Manage";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function getDataTable() {
        $image_path = "{$this->managed_module->getImagesHttpPath()}/portfolio_images";
    
        $resultset = new SQL('portfolio_project_images');
        
        $resultset->setBaseQuery("
            SELECT
                thumbnail_name,
                title,
                description,
                portfolio_project_image_id,
                ? AS image_path
            FROM resume_portfolio_project_images
            {{WHERE_CRITERIA}}
        ", array($image_path));
        
        $resultset->setSortCriteria('sort_order', 'ASC');
    
        $page_filter = array();
        $portfolio_project_options = array();
    
        if(!empty(request()->get->portfolio_project_id)) {
            $page_filter = array('portfolio_project_id');
        }
        else {
            $portfolio_projects = db()->getAll("
                SELECT
                    portfolio_project_id,
                    project_name
                FROM resume_portfolio_projects
                ORDER BY sort_order
            ");
            
            if(!empty($portfolio_projects)) {
                foreach($portfolio_projects as $portfolio_project) {                    
                    $portfolio_project_options["{$portfolio_project['project_name']}"] = "portfolio_project_id = {$portfolio_project['portfolio_project_id']}"; 
                }
            }
        }
    
        $portfolio_images_table = new EditTable(
            'portfolio_images',
            'resume_portfolio_project_images',
            'add',
            'portfolio_project_image_id',
            'sort_order',
            $page_filter
        );
        
        $portfolio_images_table->setNumberOfColumns(3);
        
        $portfolio_images_table->setHeader(array(
            'Image',
            'title' => 'Title',
            'description' => 'Description'
        ));
        
        if(!empty($portfolio_project_options)) {
            $portfolio_images_table->addFilterDropdown('portfolio_projects', $portfolio_project_options, 'Select a Portfolio Project');
        
            $portfolio_images_table->setPrimaryDropdown('portfolio_projects');
        }

        $portfolio_images_table->process($resultset, function($results_data) {
            if(!empty($results_data)) {            
                foreach($results_data as $index => $row) {        
                    if(!empty($row['thumbnail_name'])) {
                        $row['thumbnail_name'] = "
                            <img src=\"{$row['image_path']}/{$row['thumbnail_name']}\" />
                            <br />
                            <br />
                        ";
                    }
                    
                    $portfolio_image_edit_url = Http::getCurrentLevelPageUrl("change-image-file", array(
                        'portfolio_project_image_id' => $row['portfolio_project_image_id']
                    ), 'resume');
                
                    $row['thumbnail_name'] .= "<a href=\"{$portfolio_image_edit_url}\">Change Image</a>";
                    
                    unset($row['image_path']);
                    
                    $results_data[$index] = $row;
                }
            }
            
            return $results_data;
        });
        
        return $portfolio_images_table;
    }
    
    protected function constructRightContent() {        
        $this->page->body->addChild($this->getDataTable(), 'current_menu_content');
    }
}