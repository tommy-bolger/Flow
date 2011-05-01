<?php
/**
* Displays a user's online resume including education, skills, experience, and portfolio.
* Copyright (C) 2011  Tommy Bolger
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class OnlineResume
extends Module {
    protected $name = __CLASS__;
    
    protected $cache_page = true;
    
    protected $load_analytics = true;
    
    private $general_information = array();

    public function __construct() {
        parent::__construct('online_resume');
        
        $this->constructHeader();
        
        $this->constructContentHeader();
        
        $this->constructContent();
        
        $this->constructFooter();
    }
    
    private function constructHeader() {
        $this->setDocType('xhtml_1.0', 'transitional');
        
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
            FROM online_resume.general_information gi
            JOIN us_states us USING (state_id)
            WHERE gi.general_information_id = 1
        ");
        
        $first_name = $this->general_information['first_name'];
        $first_name_lower_case = strtolower($this->general_information['first_name']);
        
        $last_name = $this->general_information['last_name'];
        $last_name_lower_case = strtolower($this->general_information['last_name']);
        
        $full_name = "{$first_name} {$last_name}";
        
        
        //Set the page title
        $this->title = "{$full_name} - Resume and Portfolio";
        
        //Set meta tags
        $this->addMetaTag('encoding', 'http-equiv', 'content-type', 'text/html; charset=UTF-8');
        $this->addMetaTag('page_author', 'name', 'author', 'Tommy Bolger');
        $this->addMetaTag('page_keywords', 'name', 'keywords', "{$first_name_lower_case} {$last_name_lower_case}, {$first_name_lower_case} {$last_name_lower_case} resume, {$first_name_lower_case} {$last_name_lower_case} portfolio, {$first_name_lower_case}, {$last_name_lower_case}, education, skills, experience, resume, online, portfolio, ca, php, mysql, postgre, postgres");
        $this->addMetaTag('page_description', 'name', 'description', "{$full_name} - online resume, trade skills, work experience, and portfolio.");
        
        //Setup css
        $this->addCssFile("{$this->assets_path}/css/reset.css", false);
        $this->addCssFile('main.css');
        
        //Setup the javascript        
        $this->addJavascriptFile("{$this->assets_path}/javascript/jquery-1.4.4.min.js", false);
        
        //Set the template for this page
        $this->body->setTemplate("index_body.html");
    }
    
    private function constructContentHeader() {        
        $this->body->addImageElement("{$this->module_images_path}/{$this->general_information['photo']}", array('id' => 'photo'));
    
        $this->body->addLiteralElement('name', "{$this->general_information['first_name']} {$this->general_information['last_name']}");
        $this->body->addLiteralElement('specialty', $this->general_information['specialty']);
        
        $phone_number = '';
        
        if(!empty($this->general_information['phone_number'])) {
            $phone_number = "<h4>{$this->general_information['phone_number']}</h4>";
        }
        
        $this->body->addLiteralElement('phone_number', $phone_number);
        $this->body->addLiteralElement('email_address', $this->general_information['email_address']);
        
        //Compile the address
        $address = '';
        
        if(!empty($this->general_information['address'])) {
            $address .= $this->general_information['address'];
        }
        
        $address .= "{$this->general_information['city']}, {$this->general_information['state']}";
        
        $this->body->addLiteralElement('address', $address);
        
        $this->body->addDiv(array('id' => 'description'), $this->general_information['summary']);
        
        $this->body->addHyperlink("{$this->module_assets_path}/files/{$this->general_information['resume_pdf_name']}", 'Print PDF', array(
            'id' => 'print_pdf_link',
            'target' => '_blank'
        ));
        
        $this->body->addHyperlink("{$this->module_assets_path}/files/{$this->general_information['resume_word_name']}", 'Print Word', array(
            'id' => 'print_word_link',
            'target' => '_blank'
        ));
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
                to_char(e.date_graduated, 'MM/YYYY') AS date_graduated,
                e.cumulative_gpa
            FROM online_resume.education e
            JOIN us_states us USING (state_id)
            JOIN online_resume.degree_levels dl USING (degree_level_id)
            ORDER BY sort_order ASC
        ");
        
        $education_institutions = new Div(array('id' => 'education_institutions'));
        
        if(!empty($education_items)) {
            foreach($education_items as $education_item) {            
                $education_institution = new Div(array('class' => 'education_institution'), "
                    <span class=\"bold\">{$education_item['degree_level_name']} in {$education_item['degree_name']}</span>
                    <p>{$education_item['institution_name']}</p>
                    <p>{$education_item['institution_city']}, {$education_item['state']}</p>
                    <p><span class=\"italic\">Graduation Date: </span> {$education_item['date_graduated']}</p>
                    <p><span class=\"italic\">Cumulative GPA: </span> {$education_item['cumulative_gpa']}</p>
                ");
                
                $education_institutions->addChild($education_institution);
            }
        }
        
        $this->body->addChild($education_institutions);
    }
    
    private function constructSkills() {
        $skill_categories = db()->getAll("
            SELECT
                skill_category_id,
                skill_category_name
            FROM online_resume.skill_categories
            ORDER BY sort_order ASC
        ");
        
        $skills = db()->getAssoc("
            SELECT
                s.skill_category_id,
                s.skill_name,
                s.years_proficient,
                pl.proficiency_level_name
            FROM online_resume.skills s
            JOIN online_resume.proficiency_levels pl USING (proficiency_level_id)
            ORDER BY sort_order ASC
        ");
        
        $skills_list = new Table('skills_list');
        
        if(!empty($skill_categories)) {
            foreach($skill_categories as $skill_category) {
                $skills_list_row = array("<span class=\"bold skill_category\">{$skill_category['skill_category_name']}:</span>");
                
                $skill_category_id = $skill_category['skill_category_id'];
                
                if(isset($skills[$skill_category_id])) {                
                    $category_skills = $skills[$skill_category_id];
                    
                    if(!empty($category_skills)) {
                        $category_skill_names = '';
                    
                        foreach($category_skills as $category_skill) {
                            if(!empty($category_skill_names)) {
                                $category_skill_names .= ', ';
                            }
                            
                            $category_skill_names .= $category_skill['skill_name'];
                        }
                        
                        $skills_list_row[] = $category_skill_names;
                    }
                }
                
                $skills_list->addRow($skills_list_row);
            }
        }
        
        $this->body->addChild($skills_list);
    }
    
    private function constructWorkHistory() {
        $work_history_organizations = db()->getAll("
            SELECT
                work_history_id,
                organization_name,
                job_title
            FROM online_resume.work_history
            ORDER BY sort_order ASC
        ");
        
        $work_history_html = new Div(array('id' => 'work_history'));
        
        if(!empty($work_history_organizations)) {
            $work_history_durations = db()->getAssoc("
                SELECT
                    work_history_id,
                    to_char(start_date, 'MM/YYYY') AS start_date,
                    CASE
                        WHEN end_date IS NOT NULL THEN to_char(end_date, 'MM/YYYY')
                        ELSE 'Present'
                    END AS end_date
                FROM online_resume.work_history_durations
                ORDER BY sort_order ASC
            ");
            
            $work_history_tasks = db()->getGroupedColumn("
                SELECT
                    work_history_id,
                    description
                FROM online_resume.work_history_tasks
                ORDER BY sort_order ASC
            ");
            
            foreach($work_history_organizations as $work_history_organization) {                
                $work_history_id = $work_history_organization['work_history_id'];
                
                //The main div for this organization
                $position_duration_html = new Div(array('class' => 'work_history_organization'));
            
                //Add the job title and organization name div
                $position_duration_html->addDiv(array('class' => 'organization_position'), "
                    <p><span class=\"bold\">{$work_history_organization['job_title']}</span></p>
                    <p>{$work_history_organization['organization_name']}</p>
                ");
                
                //Compile the durations html
                $durations_html = '';
                
                if(isset($work_history_durations[$work_history_id])) {
                    $organization_durations = $work_history_durations[$work_history_id];
                    
                    if(!empty($organization_durations)) {
                        foreach($organization_durations as $organization_duration) {
                            if(!empty($durations_html)) {
                                $durations_html .= "<br />\n";
                            }
                            
                            $durations_html .= "{$organization_duration['start_date']} - {$organization_duration['end_date']}";
                        }
                    }
                }
                
                //Add the durations div
                $position_duration_html->addDiv(array('class' => 'organization_duration'), $durations_html);
                
                //Add the float clearing div
                $position_duration_html->addDiv(array('class' => 'clear'));
                
                $work_history_html->addChild($position_duration_html);
                
                //If tasks have been specified for this work history organization then render them
                if(isset($work_history_tasks[$work_history_id])) {
                    $work_history_html->addUnorderedList($work_history_tasks[$work_history_id], array('class' => 'organization_tasks'));
                }
            }
        }
        
        $this->body->addChild($work_history_html);
    }
    
    private function constructPortfolio() {
        $portfolio_html = new Div(array('id' => 'portfolio_projects'));
        
        $portfolio_projects = db()->getAll("
            SELECT
                pp.portfolio_project_id,
                pp.project_name,
                wh.organization_name,
                pp.site_url,
                description,
                involvement_description
            FROM online_resume.portfolio_projects pp
            LEFT JOIN online_resume.work_history wh USING (work_history_id)
            ORDER BY pp.sort_order ASC
        ");
        
        if(!empty($portfolio_projects)) {
            $portfolio_screenshots_path = "{$this->module_images_path}/portfolio_images";
            
            $portfolio_project_images = db()->getAssoc("
                SELECT
                    portfolio_project_id,
                    image_name,
                    thumbnail_name,
                    title,
                    description
                FROM online_resume.portfolio_project_images
                ORDER BY portfolio_project_id, sort_order ASC
            ");
        
            $portfolio_project_skills = db()->getGroupedColumn("
                SELECT
                    pps.portfolio_project_id,
                    s.skill_name
                FROM online_resume.portfolio_project_skills pps
                JOIN online_resume.skills s USING (skill_id)
                ORDER BY pps.sort_order ASC
            ");
            
            foreach($portfolio_projects as $portfolio_project) {
                $portfolio_project_html = new Div(array('class' => 'portfolio_project'));
            
                $portfolio_project_id = $portfolio_project['portfolio_project_id'];
                
                $portfolio_project_html->addLiteralElement(NULL, "
                    <h4>{$portfolio_project['project_name']}</h4>
                ");

                if(isset($portfolio_project_images[$portfolio_project_id])) {
                    $current_project_images = $portfolio_project_images[$portfolio_project_id];
                    
                    if(!empty($current_project_images)) {
                        $this->addCssFile("colorbox.css");
                        $this->addJavascriptFile("./assets/javascript/jquery.colorbox-min.js", false);
                        
                        $project_images_html = new Div(array('class' => 'portfolio_project_images'));
                    
                        foreach($current_project_images as $current_project_image) {                        
                            $project_images_html->addHyperLink(
                                "{$portfolio_screenshots_path}/{$current_project_image['image_name']}",
                                "<img src=\"{$portfolio_screenshots_path}/{$current_project_image['thumbnail_name']}\" class=\"portfolio_project_image\" />", 
                                array('title' => $current_project_image['title'], 'rel' => $portfolio_project_id, 'target' => '_blank')
                            );
                        }

                        $portfolio_project_html->addChild($project_images_html);
                    }
                }
                
                if(!empty($portfolio_project['organization_name'])) {
                    $portfolio_project_html->addParagraph("
                        <span class=\"bold\">Organization:&nbsp;</span>{$portfolio_project['organization_name']}
                    ");
                }
                
                if(!empty($portfolio_project['site_url'])) {
                    $portfolio_project_html->addHyperlink($portfolio_project['site_url'], "
                        <span class=\"bold\">URL</span>:&nbsp;{$portfolio_project['site_url']}
                    ", array('target' => '_blank'));
                }
                
                if(isset($portfolio_project_skills[$portfolio_project_id])) {
                    $portfolio_project_html->addParagraph('<span class="bold">Skills Used:&nbsp;</span>' . implode(', ', $portfolio_project_skills[$portfolio_project_id]));
                }
                
                $portfolio_project_html->addParagraph($portfolio_project['description'], array('class' => 'portfolio_description'));
                
                if(!empty($portfolio_project['involvement_description'])) {
                    $portfolio_project_html->addParagraph($portfolio_project['involvement_description'], array('class' => 'portfolio_description'));
                }
                
                $portfolio_project_html->addDiv(array('class' => 'clear'));
                
                $portfolio_html->addChild($portfolio_project_html);
            }
        }
        
        $this->body->addChild($portfolio_html);
    }
    
    private function constructCodeExamples() {
        $code_examples_html = new Div(array('id' => 'code_examples'));
    
        $code_examples = db()->getAll("
            SELECT
                ce.code_example_id,
                ce.code_example_name,
                ce.source_file_name,
                wh.organization_name,
                pp.project_name,
                ce.purpose,
                ce.description
            FROM online_resume.code_examples ce
            LEFT JOIN online_resume.work_history wh ON wh.work_history_id = ce.work_history_id
            LEFT JOIN online_resume.portfolio_projects pp ON pp.portfolio_project_id = ce.portfolio_project_id
            ORDER BY ce.sort_order ASC
        ");
        
        if(!empty($code_examples)) {
            $code_examples_path = "{$this->module_files_path}/code_examples";
        
            $code_example_skills = db()->getGroupedColumn("
                SELECT
                    ces.code_example_id,
                    s.skill_name
                FROM online_resume.code_example_skills ces
                JOIN online_resume.skills s USING (skill_id)
                ORDER BY ces.sort_order ASC
            ");
            
            foreach($code_examples as $code_example) {
                $code_example_html = new Div(array('class' => 'code_example'));
            
                $code_example_id = $code_example['code_example_id'];
                
                $code_example_html ->addLiteralElement(NULL, "
                    <h4>{$code_example['code_example_name']}</h4>
                ");
                
                $code_example_source = new Div(array('class' => 'code_example_source'));
                
                $code_example_source->addHyperLink(
                    "{$code_examples_path}/{$code_example['source_file_name']}",
                    "Download Source", 
                    array('target' => '_blank')
                );
                
                $code_example_html->addChild($code_example_source);
                
                if(!empty($code_example['organization_name'])) {
                    $code_example_html->addParagraph("
                        <span class=\"bold\">Organization:&nbsp;</span>{$code_example['organization_name']}
                    ");
                }
                
                if(!empty($code_example['project_name'])) {
                    $code_example_html->addParagraph("
                        <span class=\"bold\">Portfolio Project:&nbsp;</span>{$code_example['project_name']}
                    ");
                }
                
                if(isset($code_example_skills[$code_example_id])) {
                    $code_example_html->addParagraph('<span class="bold">Skills Used:&nbsp;</span>' . implode(', ', $code_example_skills[$code_example_id]));
                }
                
                $code_example_html->addParagraph($code_example['purpose'], array('class' => 'code_example_description'));
                
                $code_example_html->addParagraph($code_example['description'], array('class' => 'code_example_description'));
                
                $code_examples_html->addChild($code_example_html);
            }
        }
        
        $this->body->addChild($code_examples_html);
    }
    
    private function constructFooter() {}
}