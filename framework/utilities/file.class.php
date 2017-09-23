<?php
/**
* Provides functionality to perform file operations.
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
namespace Framework\Utilities;

use \Exception;
use \ZipArchive;

class File {
    /**
    * Removes illegal characters from a path segment.
    * 
    * @param string $path_segment The segment to sanitize.    
    * @return string
    */
    public static function sanitizePathSegment($path_segment) {
        return str_replace(array(
            '~',
            '\\',
            '..'
        ), '', $path_segment);
    }

    /**
    * Moves an uploaded file to a specified directory path.
    * 
    * @param array $upload_file_data The data from the file's position in $_FILES.
    * @param string $file_directory_path The path of the directory to save the uploaded file to.    
    * @return void
    */
    public static function moveUpload(array $upload_file_data, $file_directory_path) {    
        if(is_writable($file_directory_path)) {
            $file_name = $upload_file_data['name'];
            
            $upload_file_path = rtrim($file_directory_path, '/') . "/{$file_name}";

            $success = move_uploaded_file($upload_file_data['tmp_name'], $upload_file_path);
            
            if(!$success) {
                throw new Exception("Moving file '{$file_name}' to '{$file_directory_path}' failed.");
            }
        }
        else {
            throw new Exception("Directory '{$file_directory_path}' is not writable.");
        }
    }
    
    /**
    * Archives a file into zip format to a specified destination.
    * 
    * @param string $original_file_path The path to the file that will be zipped.
    * @param string $save_path (optional) The directory path to save the archive file to. Defaults to an empty string which is considered the original file's directory path.
    * @return string The name of the created archive file.
    */
    public static function zipFile($original_file_path, $save_path = '') {
        $original_file = pathinfo($original_file_path);
    
        if(!is_readable($original_file_path)) {
            throw new Exception("File '{$original_file_path}' does not exist or is not readable.");
        }
        
        $original_file_name = $original_file['filename'];
        
        $archive_file_name = "{$original_file_name}.zip";
        
        $archive_directory_path = NULL;
        
        if(!empty($save_path)) {
            $archive_directory_path = rtrim($save_path, '/');
        }
        else {
            $archive_directory_path = $original_file['dirname'];
        }
        
        $archive_file_path = "{$archive_directory_path}/{$archive_file_name}";

        //Zip the file
        $archive_file = new ZipArchive();
        
        if($archive_file->open($archive_file_path, ZipArchive::CREATE) === true) {
            $archive_file->addFile($original_file_path, $original_file['basename']);
            $archive_file->close();
        }
        else {
            throw new Exception("Archive file '{$archive_file_path}' could not be created.");
        }
        
        return $archive_file_name;
    }
    
    /**
    * Recursively deletes a directory.
    * 
    * @param string $directory_path The path to the directory to delete.
    * @return void
    */
    public static function deleteDirectoryRecursive($directory_path) { 
        if(is_dir($directory_path)) { 
            $objects = scandir($directory_path); 
            
            if(!empty($objects)) {
                foreach($objects as $object) { 
                    if($object != "." && $object != "..") { 
                        $file_path = "{$directory_path}/{$object}";
                    
                        if(is_dir($file_path)) {
                            static::deleteDirectoryRecursive($file_path);
                        }
                        else {
                            unlink($file_path); 
                        }
                    } 
                }
            }
            
            rmdir($directory_path); 
        } 
    }
}