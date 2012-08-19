<?php
/**
* The add/edit page of the Ads section for the Admin module.
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

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Add
extends Home {
    protected $title = "Add/Edit Campaign Ad";
    
    protected $active_sub_nav_link = 'Add/Edit';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Add/Edit'] = Http::getCurrentLevelPageUrl('manage');
    }
    
    protected function constructRightContent() {
        $module_id = $this->managed_module->getId();
        
        //The education history table
        $campaign_ads_form = new EditTableForm('campaign_ads', 'cms_ad_campaign_affiliation', 'ad_campaign_affiliation_id');
        
        $campaign_ads_form->setTitle('Add a New Campaign Ad');
        
        $campaigns = db()->getMappedColumn("
            SELECT
                ad_campaign_id,
                ad_campaign_name
            FROM cms_ad_campaigns
            WHERE module_id = ?
        ", array($module_id));
        
        $ads = db()->getMappedColumn("
            SELECT
                ad_id,
                description
            FROM cms_ads
            WHERE module_id = ?
        ", array($module_id));
        
        $campaign_ads_form->addDropdown('ad_campaign_id', 'Campaign', $campaigns)->addBlankOption();
        $campaign_ads_form->addDropdown('ad_id', 'Ad', $ads)->addBlankOption();
        $campaign_ads_form->addBooleanCheckbox('is_active', 'Active'); 
        $campaign_ads_form->addDate('start_date', 'Start Date'); 
        $campaign_ads_form->addDate('end_date', 'End Date'); 
        $campaign_ads_form->addIntField('show_chance_percentage', 'Show Chance Percent')->setMaxDigits(3);         
        $campaign_ads_form->addSubmit('save', 'Save');
        
        $campaign_ads_form->setRequiredFields(array(
            'ad_campaign_id',
            'ad_id',
            'start_date',
            'show_chance_percentage'
        ));
        
        if($campaign_ads_form->wasSubmitted() && $campaign_ads_form->isValid()) {
            $form_data = $campaign_ads_form->getData();
            
            $show_chance_percentage = $form_data['show_chance_percentage'];
        
            $campaign_ad_precentages = db()->getColumn("
                SELECT show_chance_percentage
                FROM cms_ad_campaign_affiliation
                WHERE ad_campaign_id = ?
            ", array($form_data['ad_campaign_id']));
            
            $total_percentage_used = array_sum($campaign_ad_precentages);
            
            if(($show_chance_percentage + $total_percentage_used) > 100) {
                $remaining_percentage = 100 - $total_percentage_used;

                $campaign_ads_form->addError("Show Chance Percent cannot exceed {$remaining_percentage}%.");
            }
            else {
                $campaign_ads_form->processForm();
            }
        }
        else {
            $campaign_ads_form->processForm();
        }
        
        $this->body->addChild($campaign_ads_form, 'current_menu_content');
    }
}