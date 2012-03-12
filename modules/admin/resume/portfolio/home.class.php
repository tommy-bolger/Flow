<?php
/**
* The home page for user portfolio of the Online Resume module.
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

namespace Modules\Admin\Resume\Portfolio;

use \Modules\Admin\Resume\Home as ResumeHome;
use \Framework\Utilities\Http;

class Home
extends ResumeHome {
    protected $title = "Portfolio";
    
    protected $active_top_link = 'Portfolio';
    
    protected $active_sub_nav_section = 'Projects';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Portfolio'] = Http::getInternalUrl('', array(
            'resume',
            'portfolio'
        ), 'manage');
    }
    
    protected function getSubNavLinks() {
        $subdirectory_path = array(
            'resume',
            'portfolio'
        );
        
        $skills_path = $subdirectory_path;
        $skills_path[] = 'skills';
        
        $images_path = $subdirectory_path;
        $images_path[] = 'images';
    
        return array(
            'Projects' => array(
                'Manage' => Http::getInternalUrl('', $subdirectory_path, 'manage'),
                'Add/Edit' => Http::getInternalUrl('', $subdirectory_path, 'add')
            ),
            'Project Skills' => array(
                'Manage' => Http::getInternalUrl('', $skills_path, 'manage'),
                'Add/Edit' => Http::getInternalUrl('', $skills_path, 'add')
            ),
            'Project Images' => array(
                'Manage' => Http::getInternalUrl('', $images_path, 'manage'),
                'Add/Edit' => Http::getInternalUrl('', $images_path, 'add'),
                'Change Image File' => Http::getInternalUrl('', $images_path, 'change-image-file')
            )
        );
    }
    
    protected function constructRightContent() {}
}