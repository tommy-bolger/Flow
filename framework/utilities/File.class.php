<?php
/**
* Provides functionality to perform file operations.
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
class File {
    /**
    * Moves an uploaded file to a specified directory path.
    * 
    * @param array $upload_file_data The data from the file's position in $_FILES.
    * @param string $file_directory_path The path of the directory to save the uploaded file to.    
    * @return void
    */
    public static function moveUpload($upload_file_data, $file_directory_path) {
        assert('is_array($upload_file_data) && !empty($upload_file_data)');
    
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
        
        if(!is_writeable($archive_directory_path)) {
            throw new Exception("Directory '{$archive_directory_path}' does not exist or is not writeable.");
        }

        //Zip the file
        $archive_file = new ZipArchive();
        
        if($archive_file->open($archive_file_path, ZIPARCHIVE::CREATE) === true) {
            $archive_file->addFile($original_file_path, $original_file['basename']);
            $archive_file->close();
        }
        else {
            throw new Exception("Archive file '{$archive_file_path}' could not be created.");
        }
        
        return $archive_file_name;
    }
}