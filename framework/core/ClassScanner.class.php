<?php
/**
* Utilizes the SPL classes to scan the framework for class files.
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
class ClassScanner {	
	/**
	 * Refresh the list of classes in the application and write their locations to the classes file.
	 *
	 * @param string $classes_file_location The path to the classes file.
	 * @return array
	 */
	public static function refreshClassesFile($classes_file_location) {	
		$classes_file_directory = dirname($classes_file_location);

		if(!is_writeable($classes_file_directory)) {
			trigger_error("The classes file cannot be written to.");
		}
	
		$available_classes = self::getAvailableClasses();
		
		$pages_file = fopen($classes_file_location, 'w');
		
		foreach($available_classes as $class => $path) {
			$classes_ini_line = $class . " = " . $path . "\n";
			
			fwrite($pages_file, $classes_ini_line);
		}
		
		fclose($pages_file);
		
		return $available_classes;
	}

	/**
	 * Retrieves a list of available classes within the application.
	 *
	 * @return array
	 */
	private static function getAvailableClasses() {
		$available_classes = array();

		$class_search_directories = config('framework')->getArrayParameter("classes_path");

		foreach($class_search_directories as $class_search_directory) {
			$class_search_directory = realpath($class_search_directory);

			$directory_iterator = new RecursiveDirectoryIterator($class_search_directory);
		
			$filtered_iterator = new ClassDirectoryFilterIterator($directory_iterator);
		
			$iterator_iterator = new RecursiveIteratorIterator($filtered_iterator, RecursiveIteratorIterator::SELF_FIRST);
		
			$filtered_iterator_iterator = new ClassExtensionFilter($iterator_iterator);
		
			foreach($filtered_iterator_iterator as $file) {
				$available_classes[$file->getBasename(".class.php")] = $file->getPathname();
			}
		}

		return $available_classes;
	}
}

final class ClassDirectoryFilterIterator extends RecursiveFilterIterator {
    /**
    * @var array A list of directories to ignore when scanning for class files.
    */
	private $class_exclude_directories;
	
	/**
	 * Initializes a new instance of ClassDirectoryFilterIterator.
	 *
	 * @return void
	 */
	public function __construct($iterator) {
		parent::__construct($iterator);
	
		$exclude = config('framework')->getArrayParameter("classes_exclude");
			
		$this->class_exclude_directories = array_combine($exclude, $exclude);
	}

    /**
	 * Determines whether the current directory is allowed to be scanned.
	 *
	 * @return boolean
	 */
	public function accept() {
		if(!isset($this->class_exclude_directories[$this->current()->getFilename()])) {
			return true;
		}
		
		return false;
	}
}

final class ClassExtensionFilter extends FilterIterator {
    /**
	 * Initializes a new instance of ClassExtensionFilter.
	 *
	 * @return void
	 */
	public function __construct($iterator) {
		parent::__construct($iterator);
	}
	
	/**
	 * Determines whether the current file is to be included in the results of the scan.
	 *
	 * @return boolean
	 */
	public function accept() {
		if(strpos($this->current()->getFilename(), '.class.php') !== false) {
			return true;
		}
		
		return false;
	}
}