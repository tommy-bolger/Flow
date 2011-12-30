<?php
namespace Framework\Html\Custom;

use \Framework\Html\Element;

class PagePath 
extends Element {
    private $pages = array();

    public function __construct($name, $pages = array(), $attributes = array()) {
        assert('!empty($name)');
    
        $attributes['id'] = $name;
        
        parent::__construct(NULL, $attributes);
        
        if(!empty($pages)) {
            $this->addPages($pages);
        }
    }
    
    public function addPage($page_name, $page_url) {
        $this->pages[$page_name] = $page_url;
    }
    
    public function addPages($pages) {
        foreach($pages as $page_name => $page_url) {
            $this->pages[$page_name] = $page_url;
        }
    }
    
    public function toHtml() {
        $element_html = "<div{$this->renderAttributes()}>";
        
        $page_links = array();
        
        $page_number = 1;
        $page_count = count($this->pages);
        
        foreach($this->pages as $page_name => $page_url) {
            if($page_number < $page_count) {
                $page_links[] = "<a href=\"{$page_url}\">{$page_name}</a>";
            }
            else {
                $page_links[] = $page_name;
            }
            
            $page_number++;
        }
        
        $element_html .= implode(' -> ', $page_links);
        
        $element_html .= "</div>";
        
        return $element_html;
    }
}