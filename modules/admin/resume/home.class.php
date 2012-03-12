<?php
/**
* The home page of the Online Resume section of the Admin module.
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

use \Modules\Admin as Admin;
use \Framework\Html\Misc\Div;
use \Framework\Html\Lists\LinkList;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Home
extends Admin\Home {
    protected $title = "Resume Admin Home";
    
    protected $active_top_link = 'Resume';

    public function __construct() {
        $this->loadManagedModule('resume');
    
        parent::__construct();
    }
    
    protected function getModuleMenuLinks() {    
        return array(
            'Resume' => Http::getInternalUrl('', array('resume')),
            'General Information' => Http::getInternalUrl('', array(
                'resume',
                'general-information'
            ), 'edit'),
            'Education' => Http::getInternalUrl('', array(
                'resume',
                'education'
            ), 'manage'),
            'Skills' => Http::getInternalUrl('', array(
                'resume',
                'skills'
            ), 'manage'),
            'Work History' => Http::getInternalUrl('', array(
                'resume',
                'work-history'
            ), 'manage'),
            'Portfolio' => Http::getInternalUrl('', array(
                'resume',
                'portfolio'
            ), 'manage'),
            'Code Examples' => Http::getInternalUrl('', array(
                'resume',
                'code-examples'
            ), 'manage')
        );
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $page_url = Http::getInternalUrl('', array('resume'));
        
        $this->page_links['Resume Admin Home'] = $page_url;
        
        session()->module_path = $this->page_links;
    }
    
    protected function constructRightContent() {
        $current_menu_content = new TemplateElement('resume/home.php');
    
        $this->body->addChild($current_menu_content, 'current_menu_content');
    }
}