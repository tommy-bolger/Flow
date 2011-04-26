<?php
/**
* The management page for a source file of a code example in the Online Resume module.
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
class OnlineResumeCodeExampleFileEdit
extends OnlineResumeAdmin {
    protected $name = __CLASS__;

    protected $title = "Online Resume Code Example File Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'));
        
        request()->get->setRequired(array('code_example_id'));
        
        $code_example_id = request()->get->code_example_id;
        
        $code_examples_edit_page_url = Http::getPageBaseUrl() . "OnlineResumeCodeExamplesEdit";
        
        $source_file_data = db()->getRow("
            SELECT 
                code_example_id,
                source_file_name,
                code_example_name
            FROM online_resume.code_examples
            WHERE code_example_id = ?
        ", array($code_example_id));
        
        if(!empty($source_file_data)) {
            /* ----- The source file form -----*/
            $source_file_form = new Form('source_file_form');
            
            $source_file_path = "{$this->files_path}/code_examples";
            
            $source_file_field = new SingleFileField('source_file', 'Source File', config('online_resume')->getArrayParameter('code_example_file_extensions'), 50);
            $source_file_form->addField($source_file_field);

            $source_file_form->addSubmit('save', 'Save');
    
            $source_file_form->setDefaultValues(array('source_file' => $source_file_data['source_file_name']));
            $source_file_form->setRequiredFields(array('source_file'));
            
            if($source_file_form->wasSubmitted() && $source_file_form->isValid()) {
                $form_data = $source_file_form->getData();
                
                if(!empty($form_data['source_file'])) {
                    $source_file = $form_data['source_file'];
                    
                    $source_file_name = $source_file['name'];
                    
                    $source_files_path = "{$this->files_path}/code_examples";
                    
                    //Move the uncompressed source file to the code examples directory
                    File::moveUpload($source_file, $source_files_path);
                    
                    $uploaded_file_path = "{$source_files_path}/{$source_file_name}";
                    
                    //Create a zip file of the uploaded source file in the same directory
                    $zip_file_name = File::zipFile($uploaded_file_path);
                    
                    //Delete the original uploaded file
                    unlink($uploaded_file_path);
                    
                    db()->update('online_resume.code_examples', array('source_file_name' => $zip_file_name), array('code_example_id' => $code_example_id));
                    
                    $source_file_field->setValue(array('name' => $zip_file_name));
            
                    $source_file_form->addError('Your source file has been successfully uploaded.');
                }
            }
            
            $content->setText("
                <h2>Changing Source File for Code Example {$source_file_data['code_example_name']}</h2><br />
                <a href=\"{$code_examples_edit_page_url}\"><- Return to Code Examples</a><br /><br />
            ");
            
            $content->addChild($source_file_form);
        }
        else {        
            $content->addParagraph("
                The specified code_example_id is not associated with a valid code example. Go <a href=\"{$code_examples_edit_page_url}\">here</a> to manage your code examples.
            ");
        }
        
        $this->body->addChild($content);
    }
}