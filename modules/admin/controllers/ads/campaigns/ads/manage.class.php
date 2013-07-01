<?php
/**
* The management page of the Ads section for the Admin module.
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

namespace Modules\Admin\Controllers\Ads\Campaigns\Ads;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;
use \Framework\Data\ResultSet\SQL;

class Manage
extends Home {
    protected $title = "Manage Ads";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getInternalUrl('', array('ads'), 'manage');
    }
    
    protected function getDataTable() {
        $module_id = $this->managed_module->getId();
        
        $page_filter = array('module_id');
        $campaign_options = array();
        
        if(!empty(request()->get->ad_campaign_id)) {
            $page_filter += array('ad_campaign_id');
        }
        else {
            $ad_campaigns = db()->getAll("
                SELECT
                    ad_campaign_name,
                    ad_campaign_id
                FROM cms_ad_campaigns
                WHERE module_id = ?
            ", array($module_id));
            
            if(!empty($ad_campaigns)) {
                foreach($ad_campaigns as $ad_campaign) {
                    $campaign_options[$ad_campaign['ad_campaign_name']] = "ad_campaign_id = '{$ad_campaign['ad_campaign_id']}'";
                }
            }
        }
    
        $resultset = new SQL('skills');
        
        $resultset->setBaseQuery("
            SELECT
                ac.ad_campaign_name,
                a.description AS ad_description,
                aca.is_active,
                aca.start_date,
                aca.end_date,
                aca.show_chance_percentage,
                aca.ad_campaign_affiliation_id
            FROM cms_ad_campaigns ac
            JOIN cms_ad_campaign_affiliation aca USING (ad_campaign_id)
            JOIN cms_ads a USING (ad_id, module_id)
            {{WHERE_CRITERIA}}
        ");
        
        $ads_campaigns_affiliation_table = new EditTable(
            'campaign_ads',
            'cms_ad_campaign_affiliation',
            'add',
            'ad_campaign_affiliation_id',
            NULL,
            $page_filter
        );

        $ads_campaigns_affiliation_table->setHeader(array(
            'Campaign',
            'Ad',
            'Active',
            'Start Date',
            'End Date',
            'Show Chance'
        ));
        
        $ads_campaigns_affiliation_table->setNumberOfColumns(6);
        
        if(!empty($campaign_options)) {
            $ads_campaigns_affiliation_table->addFilterDropdown('campaign', $campaign_options, 'Campaign');                    
        }
        
        $ads_campaigns_affiliation_table->setPrimaryDropdown('campaign');
        
        $ads_campaigns_affiliation_table->process($resultset, function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as &$query_row) {                
                    if(!empty($query_row['is_active'])) {
                        $query_row['is_active'] = 'Yes';
                    }
                    else {
                        $query_row['is_active'] = 'No';
                    }
                    
                    $query_row['start_date'] = date('m/d/Y', strtotime($query_row['start_date']));
                    
                    if(!empty($query_row['end_date'])) {
                        $query_row['end_date'] = date('m/d/Y', strtotime($query_row['end_date']));
                    }
                    
                    $query_row['show_chance_percentage'] = "{$query_row['show_chance_percentage']}%";
                }
            }

            return $query_rows;
        });
        
        return $ads_campaigns_affiliation_table;
    }
    
    protected function constructRightContent() {
        $this->page->body->addChild($this->getDataTable(), 'current_menu_content');
    }
}