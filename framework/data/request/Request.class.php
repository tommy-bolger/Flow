<?php
/**
* The framework $_GET and $_POST data abstraction layer.
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
final class Request {
    private static $_request;

    public $get;
    
    public $post;
    
    /**
     * Returns the current instance of the object or instantiates it if it is not set.
     *
     * @return object
     */
    public static function getRequest() {
        if(!isset(self::$_request)) {
            self::$_request = new Request();
        }
        
        return self::$_request;
    }
    
    /**
     * Instantiates a new instance of Request.
     *
     * @return void
     */
    public function __construct() {
        $this->get = new GetData();
        
        $this->post = new PostData();
    }
    
    /**
     * Retrieves a request variable value in either get or post.
     *
     * @param string $variable_name The name of the request variable to retrieve.
     * @return mixed
     */
    public function __get($variable_name) {
        $variable_value = null;
    
        if(!empty($this->get->$variable_name)) {
            $variable_value = $this->get->$variable_name;
        }
        elseif(!empty($this->post->$variable_name)) {
            $variable_value = $this->post->$variable_name;
        }
        
        return $variable_value;
    }
}