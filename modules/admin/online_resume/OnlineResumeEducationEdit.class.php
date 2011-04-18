<?php
/**
* The management page for the user education of the Online Resume module.
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
class OnlineResumeEducationEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Education Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {    
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Education Edit</h2><br />');
        
        //The education history table
        $education_history_edit_table = new EditTableForm(
            'education_history',
            'online_resume.education',
            'education_id',
            'sort_order'
        );
        
        $education_history_edit_table->addHeader(array(
            'institution_name' => 'Institution Name',
            'institution_city' => 'City',
            'state_id' => 'State',
            'degree_level_id' => 'Degree Level',
            'degree_name' => 'Degree Name',
            'date_graduated' => 'Graduated',
            'cumulative_gpa' => 'Cumulative GPA'
        ));
        
        $education_history_edit_table->setNumberOfColumns(7);
        
        if($education_history_edit_table->getFormVisibility()) {
            $degree_levels = db()->getAll("
                SELECT degree_level_id, abbreviation || ' - ' || degree_level_name AS degree_level_name
                FROM online_resume.degree_levels
            ");
            
            $options = array();
            
            foreach($degree_levels AS $degree_level) {
                $options[$degree_level['degree_level_id']] = $degree_level['degree_level_name'];
            }
            
            $education_history_edit_table->addTextbox('institution_name', 'Institution Name');     
            $education_history_edit_table->addTextbox('institution_city', 'City');        
            $education_history_edit_table->addStateSelect('state_id', 'State')->addBlankOption(); 
            $education_history_edit_table->addDropdown('degree_level_id', 'Degree Level', $options)->addBlankOption();
            $education_history_edit_table->addTextbox('degree_name', 'Degree Name');
            $education_history_edit_table->addDateField('date_graduated', 'Graduation Date');        
            $education_history_edit_table->addFloatField('cumulative_gpa', 'Cumulative GPA')->setPrecision(1, 2);
            $education_history_edit_table->addSubmit('save', 'Save');
            
            $education_history_edit_table->setRequiredFields(array(
                'institution_name',
                'institution_city',
                'state_id',
                'degree_level_id',
                'degree_name',
                'date_graduated',
                'cumulative_gpa'
            ));
            
            $education_history_edit_table->processForm();
        }
        
        $education_history_edit_table->useQuery("
            SELECT
                education_id,
                institution_name,
                institution_city,
                us.abbreviation || ' - ' || us.name AS state_id,
                dl.abbreviation || ' - ' || dl.degree_level_name AS degree_level_id,
                degree_name,
                to_char(date_graduated, 'MM/YYYY') AS date_graduated,
                cumulative_gpa
            FROM online_resume.education
            JOIN online_resume.degree_levels dl USING (degree_level_id)
            JOIN us_states us USING (state_id)
        ");
        
        $content->addChild($education_history_edit_table);
        
        $this->body->addChild($content);
	}
}