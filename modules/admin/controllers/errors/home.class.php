<?php
/**
* The home page of the errors section for the Admin module.
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

namespace Modules\Admin\Controllers\Errors;

use \Modules\Admin\Controllers\Home as AdminHome;
use \Framework\Utilities\Http;

class Home
extends AdminHome {
    protected $title = "Errors";
    
    protected $active_nav = 'errors';
    
    protected $active_sub_nav_section = 'Errors';
    
    protected $active_sub_nav_link = 'View All';

    public function __construct() {            
        parent::__construct();
    }
    
    protected function initializeModuleLinks() {
        if(!empty($this->managed_module)) {
            parent::getModuleSessionLinks();
        }
        else {
            parent::initializeModuleLinks();
        }
    }
    
    protected function setPageLinks() {
        $this->page_links = session()->module_path;     
                
        $query_string_parameters = array();
        
        if(!empty($this->managed_module)) {
            $query_string_parameters['module_id'] = $this->managed_module->getId();
        }
        
        $this->page_links['Errors'] = Http::getInternalUrl('', array(
            'errors',
            'view'
        ), 'all', $query_string_parameters);
    }
}