<?php
/**
* The management page for the user resume printable files of the Online Resume module.
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

namespace Modules\Admin\Resume;

use \Framework\Utilities\File;
use \Framework\Utilities\Http;
use \Framework\Html\Form\Form;
use \Framework\Html\Misc\Div;

class PrintFileEdit
extends Home {
    protected $title = "Resume Print File Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Print File Edit'] = Http::getCurrentLevelPageUrl('print-file-edit');
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'), '<h2>Print File Edit</h2><br />');
        
        $print_file_data = db()->getRow("
            SELECT
                general_information_id,
                resume_pdf_name,
                resume_word_name
            FROM resume_general_information
            WHERE general_information_id = 1
        ");
        
        if(!empty($print_file_data)) {
            $files_path = $this->managed_module->getFilesPath();
        
            /* ----- The print PDF form -----*/
            $print_pdf_file_form = new Form('print_pdf_file_form');
            
            $print_pdf_file_form->addSingleFile('resume_pdf_name', 'PDF Resume', array('pdf'), 500);
            $print_pdf_file_form->addSubmit('save', 'Save');

            $print_pdf_file_form->setDefaultValues(array('resume_pdf_name' => $print_file_data['resume_pdf_name']));
    
            if($print_pdf_file_form->wasSubmitted() && $print_pdf_file_form->isValid()) {
                $form_data = $print_pdf_file_form->getData();
                
                $table_data = array();
                
                if(!empty($form_data['resume_pdf_name'])) {
                    $resume_pdf_name = $form_data['resume_pdf_name'];
                
                    $table_data['resume_pdf_name'] = $resume_pdf_name['name'];
                
                    File::moveUpload($resume_pdf_name, $files_path);
                }
                else {
                    $table_data['resume_pdf_name'] = NULL;
                }
            
                db()->update('resume_general_information', $table_data, array('general_information_id' => 1));
                
                $print_pdf_file_form->addError('Your PDF file has been successfully uploaded.');
            }
            
            $content->addChild($print_pdf_file_form);
            
            /* ----- The print Word form -----*/
            $print_word_file_form = new Form('print_word_file_form');
            
            $print_word_file_form->addSingleFile('resume_word_name', 'Microsoft Word Resume', array('doc'), 100);
            $print_word_file_form->addSubmit('save', 'Save');
    
            $print_word_file_form->setDefaultValues(array('resume_word_name' => $print_file_data['resume_word_name']));
    
            if($print_word_file_form->wasSubmitted() && $print_word_file_form->isValid()) {
                $form_data = $print_word_file_form->getData();
                
                $table_data = array();
                
                if(!empty($form_data['resume_word_name'])) {
                    $resume_word_name = $form_data['resume_word_name'];
                    
                    $table_data['resume_word_name'] = $resume_word_name['name'];
                
                    File::moveUpload($resume_word_name, $files_path);
                }
                else {
                    $table_data['resume_word_name'] = NULL;
                }
           
                db()->update('resume_general_information', $table_data, array('general_information_id' => 1));
                
                $print_word_file_form->addError('Your Word file has been successfully uploaded.');
            }
            
            $content->addChild($print_word_file_form);
        }
        else {
            $general_information_edit_url = Http::getCurrentLevelPageUrl('general-information-edit');
        
            $content->addChild("
                <p>
                    Your general information needs to be added before managing print files. Go <a href=\"{$general_information_edit_url}\">here</a> to manage your general information.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}