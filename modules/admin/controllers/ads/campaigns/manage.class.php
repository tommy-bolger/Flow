<?php
/**
* The management page for campaigns of the Ads section in the Admin module.
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

namespace Modules\Admin\Controllers\Ads\Campaigns;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Ad Campaigns";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage');
    }
    
    protected function constructRightContent() {
        $ad_campaigns_table = new EditTable(
            'ad_campaigns',
            'cms_ad_campaigns',
            'edit',
            'ad_campaign_id'
        );
        
        $ad_campaigns_table->disableAddRecord();
        $ad_campaigns_table->disableMoveRecord();
        $ad_campaigns_table->disableDeleteRecord();

        $ad_campaigns_table->addHeader(array(
            'ad_campaign_name' => 'Name',
            'Description' => 'Description',
            'is_active' => 'Active'
        ));
        
        $ad_campaigns_table->setNumberOfColumns(4);
        
        $ad_campaigns_table->useQuery("
            SELECT
                ad_campaign_name,
                description,
                is_active,
                ad_campaign_id
            FROM cms_ad_campaigns
            WHERE module_id = ?
        ", array($this->managed_module->getId()), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as &$query_row) {
                    if(!empty($query_row['is_active'])) {
                        $query_row['is_active'] = 'Yes';
                    }
                    else {
                        $query_row['is_active'] = 'No';
                    }
                }
            }

            return $query_rows;
        });
        
        $this->page->body->addChild($ad_campaigns_table, 'current_menu_content');
    }
}