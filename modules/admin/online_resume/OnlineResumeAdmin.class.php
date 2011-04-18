<?php
/**
* The home page of the Online Resume section of the Admin module.
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
class OnlineResumeAdmin
extends AdminHome {
    protected $name = __CLASS__;

    protected $title = "Online Resume Admin Control Panel";
    
    protected $files_path;
    
    protected $images_path;

    public function __construct() {
        parent::__construct();
    }
    
    protected function initialize() {
        $this->loadModuleConfiguration('online_resume');
        
        $this->files_path = "{$this->assets_path}/modules/online_resume/files";
        
        $this->images_path = "{$this->assets_path}/modules/online_resume/images";
    }
    
    protected function constructSidebar() {
        $sidebar = new Div(array('id' => 'sidebar'), '<h2>Resume Management</h2>');
    
        $resume_management_pages = new LinkList(array(
            'General Information' => '?page=OnlineResumeGeneralInformationEdit',
            'Photo' => '?page=OnlineResumePhotoEdit',
            'Print Files' => '?page=OnlineResumePrintFileEdit',
            'Education' => '?page=OnlineResumeEducationEdit',
            'Skills' => '?page=OnlineResumeSkillsEdit',
            'Skill Categories' => '?page=OnlineResumeSkillCategoriesEdit',
            'Work History' => '?page=OnlineResumeWorkHistoryEdit',
            'Portfolio' => '?page=OnlineResumePortfolioEdit',
            'Code Examples' => '?page=OnlineResumeCodeExamplesEdit'
        ));
        
        $sidebar->addChild($resume_management_pages);
        
        $this->body->addChild($sidebar);
    }
    
    protected function constructRightContent() {
        $this->body->addDiv(array('id' => 'current_menu_content'), '
            <h1>Online Resume Module Administration</h1>
            <br />
            <p>
                This is the home page for the Online Resume module control panel.
            </p>
        ');
	}
}