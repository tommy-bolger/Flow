<?php
/**
* The management page of the banned ip addresses section for the Admin module.
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

namespace Modules\Admin\Controllers\Bans\IPAddresses;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Banned IP Addresses";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getInternalUrl('', array(
            'bans',
            'ip-addresses'
        ), 'manage');
    }
    
    protected function constructRightContent() {            
        $ip_addresses_table = new EditTable(
            'ip_addresses',
            'cms_banned_ip_addresses',
            'add',
            'banned_ip_address_id'
        );

        $ip_addresses_table->addHeader(array(
            'ip_address' => 'IP Address',
            'expiration_date' => 'Expiration Date'
        ));
        
        $ip_addresses_table->setNumberOfColumns(2);
        
        $ip_addresses_table->useQuery("
            SELECT
                ip_address,
                expiration_date,
                banned_ip_address_id
            FROM cms_banned_ip_addresses
        ", array(), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as &$query_row) {
                    if(!empty($query_row['expiration_date'])) {
                        $query_row['expiration_date'] = date('m/d/Y', strtotime($query_row['expiration_date']));
                    }
                }
            }

            return $query_rows;
        });
        
        $this->page->body->addChild($ip_addresses_table, 'current_menu_content');
    }
}