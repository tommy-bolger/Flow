<?php
/**
* Displays a user's online resume including education, skills, experience, and portfolio.
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

namespace Modules\Resume\Controllers;

use \Framework\Core\Controller;
use \Framework\Modules\ModulePage;
use \Framework\Html\Table\Table;
use \Framework\Html\Lists\UnorderedList;
use \Framework\Html\Misc\TemplateElement;

class Home
extends Controller {        
    private $general_information = array();
    
    public function setup() {
        $this->page = new ModulePage('resume', 'resume_page', true);
        
        $this->page->enableAnalytics();
    
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructContent();
    }
    
    private function constructHeader() {        
        //Load all general information
        $this->general_information = db()->getRow("
            SELECT
                gi.first_name,
                gi.last_name,
                gi.address,
                gi.city,
                us.abbreviation AS state,
                gi.phone_number,
                gi.photo,
                gi.email_address,
                gi.resume_pdf_name,
                gi.resume_word_name,
                gi.summary,
                gi.specialty
            FROM resume_general_information gi
            JOIN cms_us_states us USING (state_id)
            WHERE gi.general_information_id = 1
        ");
        
        $first_name = $this->general_information['first_name'];
        $first_name_lower_case = strtolower($this->general_information['first_name']);
        
        $last_name = $this->general_information['last_name'];
        $last_name_lower_case = strtolower($this->general_information['last_name']);
        
        $full_name = "{$first_name} {$last_name}";
        
        //Set the page title
        $this->page->setTitle("{$full_name} - Resume and Portfolio");
        
        //Set meta tags
        $this->page->addMetaTag('encoding', 'http-equiv', 'content-type', 'text/html; charset=UTF-8');
        $this->page->addMetaTag('page_author', 'name', 'author', 'Tommy Bolger');
        $this->page->addMetaTag('page_keywords', 'name', 'keywords', "{$first_name_lower_case} {$last_name_lower_case}, {$first_name_lower_case} {$last_name_lower_case} resume, {$first_name_lower_case} {$last_name_lower_case} portfolio, {$first_name_lower_case}, {$last_name_lower_case}, education, skills, experience, resume, online, portfolio, ca, php, mysql, postgre, postgres");
        $this->page->addMetaTag('page_description', 'name', 'description', "{$full_name} - online resume, trade skills, work experience, and portfolio.");
        
        //Setup css
        $this->page->addCssFiles(array(
            'reset.css',
            'main.css'
        ));
        
        //Setup the javascript        
        $this->page->addJavascriptFiles(array(
            "jquery.min.js",
            'home.js'
        ));
        
        //Set the template for this page
        $this->page->setTemplate("page.php");
    }
    
    private function constructContentHeader() {
        $this->page->body->addChild("{$this->page->getImagesHttpPath()}/{$this->general_information['photo']}", 'photo_url');
        $this->page->body->addChild("{$this->general_information['first_name']} {$this->general_information['last_name']}", 'name');
        $this->page->body->addChild($this->general_information['specialty'], 'specialty');
        
        if(!empty($this->general_information['phone_number'])) {
            $this->page->body->addChild($this->general_information['phone_number'], 'phone_number');
        }
        
        $this->page->body->addChild($this->general_information['email_address'], 'email_address');
        
        //Compile the address
        $address = '';
        
        if(!empty($this->general_information['address'])) {
            $address .= $this->general_information['address'];
        }
        
        $address .= "{$this->general_information['city']}, {$this->general_information['state']}";
        
        $this->page->body->addChild($address, 'address'); 
        $this->page->body->addChild($this->general_information['summary'], 'description');
        
        $assets_path = $this->page->getFilesHttpPath();
        
        $this->page->body->addChild("{$assets_path}/{$this->general_information['resume_pdf_name']}", 'print_pdf_url');
        $this->page->body->addChild("{$assets_path}/{$this->general_information['resume_word_name']}", 'print_word_url');
    }
    
    private function constructContent() {        
        $this->constructEducation();
        
        $this->constructSkills();
        
        $this->constructWorkHistory();
        
        $this->constructPortfolio();
        
        $this->constructCodeExamples();
    }
    
    private function constructEducation() {
        $education_items = db()->getAll("
            SELECT
                e.institution_name,
                e.institution_city,
                us.abbreviation AS state,
                dl.degree_level_name,
                e.degree_name,
                e.date_graduated,
                e.cumulative_gpa
            FROM resume_education e
            JOIN cms_us_states us USING (state_id)
            JOIN resume_degree_levels dl USING (degree_level_id)
            ORDER BY sort_order ASC
        ");
        
        if(!empty($education_items)) {
            foreach($education_items as $education_item) {
                $date_graduated = NULL;
                
                if(!empty($education_item['date_graduated'])) {
                    $date_graduated = $education_item['date_graduated'];
                }
            
                $education_institution = new TemplateElement('education/institution.php');
                $education_institution->addChild($education_item['degree_level_name'], 'degree_level_name');
                $education_institution->addChild($education_item['degree_name'], 'degree_name');
                $education_institution->addChild($education_item['institution_name'], 'institution_name');
                $education_institution->addChild($education_item['institution_city'], 'institution_city');
                $education_institution->addChild($education_item['state'], 'state');
                $education_institution->addChild($date_graduated, 'date_graduated');
                $education_institution->addChild($education_item['cumulative_gpa'], 'cumulative_gpa');
                
                $this->page->body->addChild($education_institution, 'education_institutions', true);
            }
        }
    }
    
    private function constructSkills() {
        $skill_categories = db()->getAll("
            SELECT
                skill_category_id,
                skill_category_name
            FROM resume_skill_categories
            ORDER BY sort_order ASC
        ");
        
        $skills = db()->getGroupedColumn("
            SELECT
                skill_category_id,
                skill_name
            FROM resume_skills
            ORDER BY sort_order ASC
        ");
        
        $skills_list = new Table('skills_list');
        
        if(!empty($skill_categories)) {
            foreach($skill_categories as $skill_category) {
                $skills_list_row = array($skill_category['skill_category_name']);
                
                $skill_category_id = $skill_category['skill_category_id'];
                
                if(isset($skills[$skill_category_id])) {                                    
                    $skills_list_row[] = implode(', ', $skills[$skill_category_id]);
                }
                
                $skills_list->addRow($skills_list_row);
            }
        }
        
        $this->page->body->addChild($skills_list);
    }
    
    private function constructWorkHistory() {
        $work_history_organizations = db()->getAll("
            SELECT
                work_history_id,
                organization_name,
                job_title
            FROM resume_work_history
            ORDER BY sort_order ASC
        ");
        
        if(!empty($work_history_organizations)) {
            $work_history_durations = db()->getAssoc("
                SELECT
                    work_history_id,
                    start_date,
                    end_date
                FROM resume_work_history_durations
                ORDER BY sort_order ASC
            ");
            
            $work_history_tasks = db()->getGroupedColumn("
                SELECT
                    work_history_id,
                    description
                FROM resume_work_history_tasks
                ORDER BY sort_order ASC
            ");
            
            foreach($work_history_organizations as $work_history_organization) {                
                $work_history_id = $work_history_organization['work_history_id'];

                $position_duration_html = new TemplateElement('work_history/organization.php');

                $position_duration_html->addChild($work_history_organization['job_title'], 'job_title');
                $position_duration_html->addChild($work_history_organization['organization_name'], 'organization_name');
                
                //Compile the durations html
                $durations_html = '';
                
                if(isset($work_history_durations[$work_history_id])) {
                    $organization_durations = $work_history_durations[$work_history_id];
                    
                    if(!empty($organization_durations)) {
                        foreach($organization_durations as $organization_duration) {
                            if(!empty($durations_html)) {
                                $durations_html .= "<br />";
                            }
                            
                            $start_date = date(date('m/Y', strtotime($organization_duration['start_date'])));
                                                        
                            $end_date = 'Present';
                            
                            if(!empty($organization_duration['end_date'])) {
                                $end_date = date('m/Y', strtotime($organization_duration['end_date']));
                            }
                            
                            $durations_html .= "{$start_date} - {$end_date}";
                        }
                    }
                }

                $position_duration_html->addChild($durations_html, 'organization_duration');
                
                $this->page->body->addChild($position_duration_html, 'work_history', true);
                
                //Work history tasks
                if(isset($work_history_tasks[$work_history_id])) {
                    $organization_tasks = new UnorderedList($work_history_tasks[$work_history_id], array('class' => 'organization_tasks'));
                
                    $position_duration_html->addChild($organization_tasks, 'organization_tasks');
                }
            }
        }
    }
    
    private function constructPortfolio() {        
        $portfolio_projects = db()->getAll("
            SELECT
                pp.portfolio_project_id,
                pp.project_name,
                wh.organization_name,
                pp.site_url,
                description,
                involvement_description
            FROM resume_portfolio_projects pp
            LEFT JOIN resume_work_history wh USING (work_history_id)
            ORDER BY pp.sort_order ASC
        ");
        
        if(!empty($portfolio_projects)) {
            $portfolio_screenshots_path = "{$this->page->getImagesHttpPath()}/portfolio_images";
            
            $portfolio_project_images = db()->getAssoc("
                SELECT
                    portfolio_project_id,
                    image_name,
                    thumbnail_name,
                    title,
                    description
                FROM resume_portfolio_project_images
                ORDER BY portfolio_project_id, sort_order ASC
            ");
        
            $portfolio_project_skills = db()->getGroupedColumn("
                SELECT
                    pps.portfolio_project_id,
                    s.skill_name
                FROM resume_portfolio_project_skills pps
                JOIN resume_skills s USING (skill_id)
                ORDER BY pps.sort_order ASC
            ");
            
            foreach($portfolio_projects as $portfolio_project) {
                $portfolio_project_html = new TemplateElement('portfolio/project.php');
                $portfolio_project_html->addChild($portfolio_project['project_name'], 'project_name');
            
                $portfolio_project_id = $portfolio_project['portfolio_project_id'];

                //Project images
                if(isset($portfolio_project_images[$portfolio_project_id])) {
                    $current_project_images = $portfolio_project_images[$portfolio_project_id];
                    
                    if(!empty($current_project_images)) {
                        $this->page->addCssFile("colorbox.css");
                        $this->page->addJavascriptFile("jquery.colorbox-min.js");
                    
                        foreach($current_project_images as $current_project_image) {   
                            $portfolio_image_thumbnail = new TemplateElement('portfolio/project_image.php');
                            $portfolio_image_thumbnail->addChild("{$portfolio_screenshots_path}/{$current_project_image['image_name']}", 'href');
                            $portfolio_image_thumbnail->addChild("{$portfolio_screenshots_path}/{$current_project_image['thumbnail_name']}", 'image_src');
                            $portfolio_image_thumbnail->addChild($current_project_image['title'], 'title');
                            $portfolio_image_thumbnail->addChild($portfolio_project_id, 'rel');
                            
                            $portfolio_project_html->addChild($portfolio_image_thumbnail, 'portfolio_project_images', true);
                        }
                    }
                }

                $portfolio_project_html->addChild($portfolio_project['organization_name'], 'organization_name');
                $portfolio_project_html->addChild($portfolio_project['site_url'], 'site_url');
                
                //Project skills used
                if(isset($portfolio_project_skills[$portfolio_project_id])) {
                    $skills = implode(', ', $portfolio_project_skills[$portfolio_project_id]);
                    
                    $portfolio_project_html->addChild($skills, 'skills_used');
                }

                $portfolio_project_html->addChild($portfolio_project['description'], 'portfolio_description');
                $portfolio_project_html->addChild($portfolio_project['involvement_description'], 'involvment_description');
                
                $this->page->body->addChild($portfolio_project_html, 'portfolio_projects', true);
            }
        }
    }
    
    private function constructCodeExamples() {    
        $code_examples = db()->getAll("
            SELECT
                ce.code_example_id,
                ce.code_example_name,
                ce.source_file_name,
                wh.organization_name,
                pp.project_name,
                ce.purpose,
                ce.description
            FROM resume_code_examples ce
            LEFT JOIN resume_work_history wh ON wh.work_history_id = ce.work_history_id
            LEFT JOIN resume_portfolio_projects pp ON pp.portfolio_project_id = ce.portfolio_project_id
            ORDER BY ce.sort_order ASC
        ");
        
        if(!empty($code_examples)) {
            $code_examples_path = "{$this->page->getFilesHttpPath()}/code_examples";
        
            $code_example_skills = db()->getGroupedColumn("
                SELECT
                    ces.code_example_id,
                    s.skill_name
                FROM resume_code_example_skills ces
                JOIN resume_skills s USING (skill_id)
                ORDER BY ces.sort_order ASC
            ");
            
            foreach($code_examples as $code_example) {
                $code_example_html = new TemplateElement('code_example.php');
            
                $code_example_id = $code_example['code_example_id'];
                
                $code_example_html->addChild($code_example['code_example_name'], 'code_example_name');
                $code_example_html->addChild("{$code_examples_path}/{$code_example['source_file_name']}", 'source_url');
                $code_example_html->addChild($code_example['organization_name'], 'organization_name');
                $code_example_html->addChild($code_example['project_name'], 'project_name');
                
                if(isset($code_example_skills[$code_example_id])) {
                    $code_example_html->addChild(implode(', ', $code_example_skills[$code_example_id]), 'skills_used');
                }
                
                $code_example_html->addChild($code_example['purpose'], 'purpose');
                $code_example_html->addChild($code_example['description'], 'description');
                
                $this->page->body->addChild($code_example_html, 'code_examples', true);
            }
        }
    }
}