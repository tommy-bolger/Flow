<?php
/**
* The management page for the user education of the Online Resume module.
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

namespace Modules\Resume\Admin\Controllers\Education;

use \Framework\Html\Table\EditTable;
use \Framework\Utilities\Http;

class Manage
extends Home {
    protected $title = "Manage Education";
    
    protected $active_sub_nav_link = 'Manage';

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Manage'] = Http::getCurrentLevelPageUrl('manage', array(), 'resume');
    }
    
    protected function constructRightContent() {            
        //The education history table
        $education_history_edit_table = new EditTable(
            'education_history',
            'resume_education',
            'add',
            'education_id',
            'sort_order'
        );

        $education_history_edit_table->addHeader(array(
            'institution_name' => 'Institution',
            'institution_city' => 'City',
            'state_id' => 'State',
            'degree_level_id' => 'Degree Level',
            'degree_name' => 'Degree Name',
            'date_graduated' => 'Graduated',
            'cumulative_gpa' => 'GPA'
        ));
        
        $education_history_edit_table->setNumberOfColumns(7);
        
        $education_history_edit_table->useQuery("
            SELECT
                institution_name,
                institution_city,
                NULL AS state_id,
                NULL AS degree_level_id,
                degree_name,
                date_graduated,
                cumulative_gpa,
                education_id,
                us.abbreviation AS state_abbreviation,
                us.state_name AS state_name,
                dl.abbreviation AS degree_abbreviation,
                dl.degree_level_name
            FROM resume_education
            JOIN resume_degree_levels dl USING (degree_level_id)
            JOIN cms_us_states us USING (state_id)
        ", array(), function($query_rows) {
            if(!empty($query_rows)) {
                foreach($query_rows as $row_index => $query_row) {
                    $query_row['state_id'] = "{$query_row['state_abbreviation']} - {$query_row['state_name']}";
                    $query_row['degree_level_id'] = "{$query_row['degree_abbreviation']} - {$query_row['degree_level_name']}";
                    $query_row['date_graduated'] = date('m/Y', strtotime($query_row['date_graduated']));
                    
                    $query_rows[$row_index] = $query_row;
                }
            }

            return $query_rows;
        });
        
        $this->body->addChild($education_history_edit_table, 'current_menu_content');
    }
}