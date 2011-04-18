<?php
/**
* The management page for the user resume printable files of the Online Resume module.
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
class OnlineResumePrintFileEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Print File Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Print File Edit</h2><br />');
        
        $print_file_data = db()->getRow("
            SELECT
                general_information_id,
                resume_pdf_name,
                resume_word_name
            FROM online_resume.general_information
            WHERE general_information_id = 1
        ");
        
        if(!empty($print_file_data)) {        
            /* ----- The print PDF form -----*/
            $print_pdf_file_form = new Form('print_pdf_file_form');
            
            $print_pdf_file_form->addSingleFileField('resume_pdf_name', 'PDF Resume', array('pdf'), 500);
            $print_pdf_file_form->addSubmit('save', 'Save');
    
            $print_pdf_file_form->setDefaultValues(array('resume_pdf_name' => $print_file_data['resume_pdf_name']));
    
            if($print_pdf_file_form->wasSubmitted() && $print_pdf_file_form->isValid()) {
                $form_data = $print_pdf_file_form->getData();
                
                $table_data = array();
                
                if(!empty($form_data['resume_pdf_name'])) {
                    $resume_pdf_name = $form_data['resume_pdf_name'];
                
                    $table_data['resume_pdf_name'] = $resume_pdf_name['name'];
                
                    File::moveUpload($resume_pdf_name, $this->files_path);
                }
                else {
                    $table_data['resume_pdf_name'] = NULL;
                }
            
                db()->update('online_resume.general_information', $table_data, array('general_information_id' => 1));
                
                $print_pdf_file_form->addError('Your PDF file has been successfully uploaded.');
            }
            
            $content->addChild($print_pdf_file_form);
            
            /* ----- The print Word form -----*/
            $print_word_file_form = new Form('print_word_file_form');
            
            $print_word_file_form->addSingleFileField('resume_word_name', 'Microsoft Word Resume', array('doc'), 100);
            $print_word_file_form->addSubmit('save', 'Save');
    
            $print_word_file_form->setDefaultValues(array('resume_word_name' => $print_file_data['resume_word_name']));
    
            if($print_word_file_form->wasSubmitted() && $print_word_file_form->isValid()) {
                $form_data = $print_word_file_form->getData();
                
                $table_data = array();
                
                if(!empty($form_data['resume_word_name'])) {
                    $resume_word_name = $form_data['resume_word_name'];
                    
                    $table_data['resume_word_name'] = $resume_word_name['name'];
                
                    File::moveUpload($resume_word_name, $this->files_path);
                }
                else {
                    $table_data['resume_word_name'] = NULL;
                }
           
                db()->update('online_resume.general_information', $table_data, array('general_information_id' => 1));
                
                $print_word_file_form->addError('Your Word file has been successfully uploaded.');
            }
            
            $content->addChild($print_word_file_form);
        }
        else {
            $general_information_edit_url = Http::getPageBaseUrl() . 'OnlineResumeGeneralInformationEdit';
        
            $content->addParagraph("
                Your general information needs to be added before managing print files. Go <a href=\"{$general_information_edit_url}\">here</a> to manage your general information.
            ");
        }
        
        $this->body->addChild($content);
	}
}