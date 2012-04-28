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

namespace Modules\Resume\Admin\CodeExamples;

use \Framework\Html\Form\TableForm;
use \Framework\Html\Form\Fields\SingleFile;
use \Framework\Utilities\Http;
use \Framework\Utilities\File;
use \Framework\Html\Misc\TemplateElement;

class ChangeSourceCode
extends Home {
    protected $title = "Change Source Code";
    
    protected $active_sub_nav_link = "Change Source Code";

    public function __construct() {        
        parent::__construct();
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $this->page_links['Change Source Code'] = Http::getCurrentLevelPageUrl('change-source-code', array(), 'resume');
    }
    
    protected function constructRightContent() {
        $code_examples = db()->getMappedColumn("
            SELECT
                code_example_id,
                code_example_name
            FROM resume_code_examples
            ORDER BY sort_order
        ");
        
        if(!empty($code_examples)) {
            $code_example_id = request()->get->code_example_id;
            $source_file_name = '';
        
            if(!empty($code_example_id)) {
                $source_file_name = db()->getOne("
                    SELECT source_file_name
                    FROM resume_code_examples
                    WHERE code_example_id = ?
                ", array($code_example_id));
            }
        
            /* ----- The source file form -----*/
            $source_file_form = new TableForm('source_file_form');
            
            $source_file_form->setTitle('Change the Source File for this Code Example');
            
            $source_file_form->addDropdown('code_example_id', 'Code Example', $code_examples)->addBlankOption();
            
            $source_file_path = "{$this->managed_module->getFilesPath()}/code_examples";

            $source_file_field = new SingleFile('source_file', 'Source File', $this->managed_module->configuration->code_example_file_extensions, 50);
            $source_file_form->addField($source_file_field);

            $source_file_form->addSubmit('save', 'Save');
    
            $source_file_form->setDefaultValues(array(
                'code_example_id' => $code_example_id,
                'source_file' => $source_file_name
            ));
            
            $source_file_form->setRequiredFields(array(
                'code_example_id',
                'source_file'
            ));
            
            if($source_file_form->wasSubmitted() && $source_file_form->isValid()) {
                $form_data = $source_file_form->getData();
                
                if(!empty($form_data['source_file'])) {
                    $code_example_id = $form_data['code_example_id'];
                    $source_file = $form_data['source_file'];
                    
                    $source_file_name = $source_file['name'];
                    
                    $source_files_path = "{$this->managed_module->getFilesPath()}/code_examples";
                    
                    //Move the uncompressed source file to the code examples directory
                    File::moveUpload($source_file, $source_files_path);
                    
                    $uploaded_file_path = "{$source_files_path}/{$source_file_name}";
                    
                    //Create a zip file of the uploaded source file in the same directory
                    $zip_file_name = File::zipFile($uploaded_file_path);
                    
                    //Delete the original uploaded file
                    unlink($uploaded_file_path);
                    
                    db()->update('resume_code_examples', array('source_file_name' => $zip_file_name), array('code_example_id' => $code_example_id));
                    
                    $source_file_field->setValue(array('name' => $zip_file_name));
            
                    $source_file_form->addConfirmation('Your source file has been successfully uploaded.');
                }
            }
            
            $this->body->addChild($source_file_form, 'current_menu_content');
        }
        else {
            $code_examples_edit_page_url = Http::getCurrentLevelPageUrl("manage", array(), 'resume');
            
            $required_template = new TemplateElement('required_records_warning.php');
            
            $required_template->addChild('Code Examples', 'prerequisite');
            $required_template->addChild('Source Files', 'context');
            $required_template->addChild($code_examples_edit_page_url, 'prerequisite_url');
            
            $this->body->addChild($required_template, 'current_menu_content');
        }
    }
}