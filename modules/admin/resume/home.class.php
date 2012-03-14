<?php
/**
* The home page of the Online Resume section of the Admin module.
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

use \Modules\Admin as Admin;
use \Framework\Html\Misc\Div;
use \Framework\Html\Lists\LinkList;
use \Framework\Utilities\Http;
use \Framework\Html\Misc\TemplateElement;

class Home
extends Admin\Home {
    protected $title = "Resume Admin Home";
    
    protected $active_top_link = 'Resume';

    public function __construct() {
        $this->loadManagedModule('resume');
    
        parent::__construct();
    }
    
    protected function initializeModuleLinks() {
        //General Information
        $general_information_path = array(
            'resume',
            'general-information'
        );
        
        //Education
        $education_path = array(
            'resume',
            'education'
        );
        
        //Skills
        $skills_path = array(
            'resume',
            'skills'
        );
        
        $skill_categories_path = $skills_path;
        $skill_categories_path[] = 'categories';
        
        //Work History
        $work_history_path = array(
            'resume',
            'work-history'
        );
        
        $work_history_durations_path = $work_history_path;
        $work_history_durations_path[] = 'durations';
        
        $work_history_tasks_path = $work_history_path;
        $work_history_tasks_path[] = 'tasks';
        
        //Portfolio
        $portfolio_path = array(
            'resume',
            'portfolio'
        );
        
        $portfolio_skills_path = $portfolio_path;
        $portfolio_skills_path[] = 'skills';
        
        $portfolio_images_path = $portfolio_path;
        $portfolio_images_path[] = 'images';
        
        //Code Examples
        $code_examples_path = array(
            'resume',
            'code-examples'
        );
        
        $code_examples_skills_path = $code_examples_path;
        $code_examples_skills_path[] = 'skills';
        
        $this->module_links = array(
            'resume' => array(
                'top_nav' => array(
                    'Resume' => Http::getInternalUrl('', array('resume'))
                )
            ),
            'general_information' => array(
                'top_nav' => array(
                    'General Information' => Http::getInternalUrl('', array(
                        'resume',
                        'general-information'
                    ), 'edit')
                ),
                'sub_nav' => array(
                    'General Information' => array(
                        'Edit' => Http::getInternalUrl('', $general_information_path, 'edit'),
                        'Change Photo' => Http::getInternalUrl('', $general_information_path, 'change-photo'),
                        'Change Print Files' => Http::getInternalUrl('', $general_information_path, 'change-print-files')
                    )
                )
            ),
            'education' => array(
                'top_nav' => array(
                    'Education' => Http::getInternalUrl('', array(
                        'resume',
                        'education'
                    ), 'manage')
                ),
                'sub_nav' => array(
                    'Education' => array(
                        'Manage' => Http::getInternalUrl('', $education_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $education_path, 'add')
                    )
                )
            ),
            'skills' => array(
                'top_nav' => array(
                    'Skills' => Http::getInternalUrl('', array(
                        'resume',
                        'skills'
                    ), 'manage')
                ),
                'sub_nav' => array(
                    'Skills' => array(
                        'Manage' => Http::getInternalUrl('', $skills_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $skills_path, 'add'),
                    ),
                    'Skill Categories' => array(
                        'Manage' => Http::getInternalUrl('', $skill_categories_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $skill_categories_path, 'add')
                    )
                )
            ),
            'work_history' => array(
                'top_nav' => array(
                    'Work History' => Http::getInternalUrl('', array(
                        'resume',
                        'work-history'
                    ), 'manage')
                ),
                'sub_nav' => array(
                    'Work History' => array(
                        'Manage' => Http::getInternalUrl('', $work_history_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $work_history_path, 'add'),
                    ),
                    'Durations' => array(
                        'Manage' => Http::getInternalUrl('', $work_history_durations_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $work_history_durations_path, 'add'),
                    ),
                    'Tasks' => array(
                        'Manage' => Http::getInternalUrl('', $work_history_tasks_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $work_history_tasks_path, 'add')
                    )
                )
            ),
            'portfolio' => array(
                'top_nav' => array(
                    'Portfolio' => Http::getInternalUrl('', array(
                        'resume',
                        'portfolio'
                    ), 'manage')
                ),
                'sub_nav' => array(
                    'Projects' => array(
                        'Manage' => Http::getInternalUrl('', $portfolio_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $portfolio_path, 'add')
                    ),
                    'Project Skills' => array(
                        'Manage' => Http::getInternalUrl('', $portfolio_skills_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $portfolio_skills_path, 'add')
                    ),
                    'Project Images' => array(
                        'Manage' => Http::getInternalUrl('', $portfolio_images_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $portfolio_images_path, 'add'),
                        'Change Image File' => Http::getInternalUrl('', $portfolio_images_path, 'change-image-file')
                    )
                )
            ),
            'code_examples' => array(
                'top_nav' => array(
                    'Code Examples' => Http::getInternalUrl('', array(
                        'resume',
                        'code-examples'
                    ), 'manage')
                ),
                'sub_nav' => array(
                    'Code Examples' => array(
                        'Manage' => Http::getInternalUrl('', $code_examples_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $code_examples_path, 'add'),
                        'Change Source Code' => Http::getInternalUrl('', $code_examples_path, 'change-source-code')
                    ),
                    'Code Example Skills' => array(
                        'Manage' => Http::getInternalUrl('', $code_examples_skills_path, 'manage'),
                        'Add/Edit' => Http::getInternalUrl('', $code_examples_skills_path, 'add')
                    )
                )
            )
        );
    }
    
    protected function setPageLinks() {
        parent::setPageLinks();
        
        $page_url = Http::getInternalUrl('', array('resume'));
        
        $this->page_links['Resume Admin Home'] = $page_url;
        
        session()->module_path = $this->page_links;
    }
    
    protected function constructRightContent() {
        $current_menu_content = new TemplateElement('resume/home.php');
    
        $this->body->addChild($current_menu_content, 'current_menu_content');
    }
}