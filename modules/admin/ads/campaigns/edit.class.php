<?php
/**
* The add/edit page for campaigns of the Ads section in the Admin module.
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

namespace Modules\Admin\Ads\Campaigns;

use \Framework\Html\Form\EditTableForm;
use \Framework\Utilities\Http;

class Edit
extends Manage {
    protected $title = "Edit this Ad Campaign";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Edit'] = Http::getCurrentLevelPageUrl('add');
    }
    
    protected function constructRightContent() {
        $ad_campaign_id = request()->get->ad_campaign_id;
        
        //The education history table
        $ad_campaign_form = new EditTableForm('ad_campaigns', 'cms_ad_campaigns', 'ad_campaign_id');
        
        $ad_campaign_form->setTitle('Edit this Ad Campaign');
        
        $ad_campaign_data = db()->getRow("
            SELECT
                ad_campaign_name,
                description
            FROM cms_ad_campaigns ac
            WHERE ad_campaign_id = ?
        ", array($ad_campaign_id));
        
        $ad_campaign_form->addReadOnly('ad_campaign_name', 'Name', $ad_campaign_data['ad_campaign_name']);        
        $ad_campaign_form->addReadOnly('description', 'Description', $ad_campaign_data['description']);
        $ad_campaign_form->addBooleanCheckbox('is_active', 'Active');
        $ad_campaign_form->addSubmit('save', 'Save');
        
        $ad_campaign_form->processForm();
        
        $this->body->addChild($ad_campaign_form, 'current_menu_content');
    }
}