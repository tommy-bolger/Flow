<?php
/**
* Allows the rendering of a form file field and performing validation on its submitted data dynamically.
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
class FileField
extends Field {    
    /**
    * @var array A list of accepted file types.
    */
    private $accepted_file_extensions = array();
    
    /**
    * @var integer The file size limit in kilobytes.
    */
    private $file_size_limit_kilobytes;
    
    /**
    * @var integer The file size limit in bytes.
    */
    private $file_size_limit_bytes;

    /**
     * Instantiates a new instance of FileField.
     *      
     * @param string $file_name The file input name.
     * @param string $file_label The file input label.
     * @param array $accepted_files (optional) A list of accepted file extenions without the preceding dot.
     * @param int $file_size (optional) The size limit the file input will allow in kilobytes. Defaults to 0 for no limit.
     * @param array (optional) $css_classes A list of classes.
     * @return void
     */
	public function __construct($file_name, $file_label, $accepted_files = array(), $file_size_limit = 0, $css_classes = array()) {
		parent::__construct("file", $file_name, $file_label, $css_classes);
		
		$this->addAcceptedFiles($accepted_files);
		
		$this->setFileSizeLimit($file_size_limit);
	}
	
	/**
     * Adds an accepted file type.
     *      
     * @param string $file_extension The file extension without the preceding dot.
     * @return void
     */
	public function addAcceptedFile($file_extension) {
        $this->accepted_file_extensions[$file_extension] = $file_extension;
	}
	
	/**
     * Adds several accepted file types.
     *      
     * @param array $accepted_files The list of accepted file extensions without the preceding dot.
     * @return void
     */
	public function addAcceptedFiles($accepted_files) {
        assert('is_array($accepted_files)');
	
        if(!empty($accepted_files)) {
            foreach($accepted_files as $file_extension) {
                $this->addAcceptedFile($file_extension);
            }
        }
    }
    
    /**
     * Sets the file size limit in kilobytes.
     *      
     * @param integer $file_size_limit The size limit of the file input in kilobytes.
     * @return void
     */
    public function setFileSizeLimit($file_size_limit) {
        $this->file_size_limit_kilobytes = $file_size_limit;
    
        $this->file_size_limit_bytes = $file_size_limit * 1000;
    }
    
    /**
     * Sets the field's submitted value.
     *      
     * @return void
     */
	public function setValue($field_value) {
        if(isset($_FILES[$this->name]) && $_FILES[$this->name]['error'] == 0) {
            $this->value = $_FILES[$this->name];
        }
        else {
            $this->value = array();
        }
	}
    
    /**
     * Validates the file input's submitted value.
     *      
     * @return boolean
     */
	protected function validate() {
        if(!parent::validate()) {
            return false;
        }
        
        if(!empty($this->value)) {
            //Validate the file extension
            if(!empty($this->accepted_file_extensions)) {
                $file_information = pathinfo($this->value['name'], PATHINFO_EXTENSION);

                if(!isset($this->accepted_file_extensions[$file_information])) {
                    $this->setErrorMessage("Submitted file has an invalid file extension. Accepted file extensions are: " . implode(', ', $this->accepted_file_extensions) . '.');
                    
                    return false;
                }
            }
            
            //Validate the file size
            if($this->file_size_limit_kilobytes != 0 && $this->value['size'] > $this->file_size_limit_bytes) {
                $this->setErrorMessage("Submitted file is too big. Please submit a file {$this->file_size_limit_kilobytes}KB or smaller.");
                
                return false;
            }
        }
        
        return true;
	}
	
	/**
     * Retrieves the field's validity status.
     *      
     * @return boolean
     */
	public function isValid() {
        $this->valid = $this->validate();
	
        return $this->valid;
	}
}