<?php
/**
* The management page for a source file of a code example in the Online Resume module.
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

use \Framework\Html\Misc\Div;
use \Framework\Html\Form\Form;
use \Framework\Html\Form\Fields\SingleFile;
use \Framework\Utilities\Http;
use \Framework\Utilities\File;

class CodeExampleFileEdit
extends CodeExamplesEdit {
    protected $title = "Resume Code Example File Edit";

    public function __construct() {
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Code Example File Edit'] = Http::getCurrentBaseUrl() . 'code-example-file-edit';
    }
    
    protected function constructRightContent() {
        $content = new Div(array('id' => 'current_menu_content'));
        
        request()->get->setRequired(array('code_example_id'));
        
        $code_example_id = request()->get->code_example_id;
        
        $code_examples_edit_page_url = Http::getCurrentBaseUrl() . "code-examples-edit";
        
        $source_file_data = db()->getRow("
            SELECT 
                code_example_id,
                source_file_name,
                code_example_name
            FROM resume_code_examples
            WHERE code_example_id = ?
        ", array($code_example_id));
        
        if(!empty($source_file_data)) {
            /* ----- The source file form -----*/
            $source_file_form = new Form('source_file_form');
            
            $source_file_path = "{$this->managed_module->getFilesPath()}/code_examples";

            $source_file_field = new SingleFile('source_file', 'Source File', $this->managed_module->configuration->code_example_file_extensions, 50);
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
                    
                    db()->update('resume_code_examples', array('source_file_name' => $zip_file_name), array('code_example_id' => $code_example_id));
                    
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
            $content->addChild("
                <p>
                    The specified code_example_id is not associated with a valid code example. Go <a href=\"{$code_examples_edit_page_url}\">here</a> to manage your code examples.
                </p>
            ");
        }
        
        $this->body->addChild($content);
    }
}