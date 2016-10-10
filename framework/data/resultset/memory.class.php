<?php
/**
* Configures, retrieves, and stores data from a result set based on cached data in memory.
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

namespace Framework\Data\ResultSet;

class Memory 
extends ResultSet {
    /**
    * @var object The framework cache object to retrieve values from memory.
    */
    protected $cache;
    
    /**
     * Initializes a new instance of a ResultSet.
     *
     * @param string $name The name of the resultset.  
     * @param string (optional) $cache_object The instance of the cache object to pull data from. Defaults to NULL, which will load the default cache instance in the cache_connections configuration.
     * @return void
     */
    public function __construct($name, $cache_object = NULL) {
        parent::__construct($name);
        
        if(empty($cache_object)) {
            $cache_object = cache();
        }
        
        $this->cache = $cache_object;
    }
    
    /**
     * Retrieves the unprocessed result set.
     * 
     * @return array
     */
    protected function getRawData() {
        if($this->has_total_record_count) {
            $this->total_number_of_records = $this->cache->get('total_record_count', $this->name);
            
            $this->rows_per_page = $this->cache->get('rows_per_page', $this->name);
        }
        
        if(empty($this->page_number)) {
            throw new \Exception("Page number is required.");
        }

        return $this->cache->get($this->page_number, $this->name);
    }
}