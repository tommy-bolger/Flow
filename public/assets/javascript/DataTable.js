/**
* An object that handles the dynamic refresh of the framework DataTable.
* Copyright (c) 2012, Tommy Bolger
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
function DataTable(dom_element) {
    var instance = this;

    this.data_table = dom_element;
    this.form = $('form', this.data_table);
    
    $(this.form).bind('submit', {
        instance: this
    }, this.submitForm);  
        
    this.filters = $('[name^="f["]', this.form);
    this.rows_per_page = $('select[name="r"]', this.form);
        
    this.table_header_container = $('.columns', this.data_table);
    this.table_header = null;
    this.initializeHeader();
    
    this.pagination_container = $('.pagination', this.data_table);
    this.page_numbers = null;
    this.initializePagination();
        
    this.body = $('tbody', this.data_table);
}

DataTable.prototype.initializeHeader = function() {
    this.table_header = $('.table_header', this.table_header_container);
    
    $('a', this.table_header).bind('click', {
        instance: this
    }, this.sortColumn);   
}

DataTable.prototype.initializePagination = function() {
    this.page_numbers = $('.page_number', this.data_table)
    
    $('a', this.page_numbers).bind('click', {
        instance: this
    }, this.changePage);
}

DataTable.prototype.setState = function(state) {
    if(state.hasOwnProperty('body')) {
        this.body.html(state.body);
    }
    
    if(state.hasOwnProperty('pagination')) {
        this.pagination_container.html(state.pagination);
        
        this.initializePagination();
    }
    
    if(state.hasOwnProperty('header')) {
        this.table_header_container.html(state.header);
        this.initializeHeader();
    }
};

DataTable.prototype.convertUrl = function(url) {
    //Add ajax/ into the action url
    var converted_url = url.replace(window.location.host, window.location.host + '/ajax');

    //Add the method to call
    if(converted_url.indexOf('?') != -1) {
        converted_url += '&';
    }
    else {
        converted_url += '?';
    }
    
    converted_url += 'method=updateTableState';
    
    return converted_url;
}

DataTable.prototype.changePage = function(event) {
    event.preventDefault();

    var instance = event.data.instance;
    
    var url = instance.convertUrl($(this).attr('href'));
    
    Request.get(url, {}, {
        context: instance,
        method: 'changePageSuccess'
    }, instance.data_table);
    
    return false;
}

DataTable.prototype.changePageSuccess = function(request, response) {
    this.setState(response);
    
    $('input[name="p"]', this.form).val(response.page_number);
}

DataTable.prototype.sortColumn = function(event) {
    event.preventDefault();

    var instance = event.data.instance;
    
    var url = instance.convertUrl($(this).attr('href'));
    
    Request.get(url, {}, {
        context: instance,
        method: 'sortColumnSuccess'
    }, instance.data_table);
    
    return false;
}

DataTable.prototype.sortColumnSuccess = function(request, response) {    
    this.setState(response);
    
    $('input[name="s"]', this.form).val(response.sort_column);
    $('input[name="d"]', this.form).val(response.sort_direction);
}

DataTable.prototype.submitForm = function(event) {
    event.preventDefault();
    
    var instance = event.data.instance;
    
    var url = instance.convertUrl($(this).attr('action'));
    
    var request_data = {
        r: instance.rows_per_page.val()
    };
    
    $.each($('input[type="hidden"]', this.form), function(field_index, field) {
        field = $(field);
        
        request_data[field.attr('name')] = field.val();
    });
    
    var filter_values = {};
                        
    $.each(instance.filters, function(filter_index, filter) {
        filter = $(filter);
        
        var bracket_filter_name = filter.attr('name');
            
        //Start at the index right after the opening bracket.
        var name_start_index = bracket_filter_name.indexOf('[') + 1;
        var name_end_index = bracket_filter_name.indexOf(']');
            
        var filter_name = bracket_filter_name.substr(name_start_index, (name_end_index - name_start_index));

        filter_values[filter_name] = filter.val();
    });
    
    request_data['f'] = filter_values;

    Request.post(url, request_data, {
        context: instance,
        method: 'submitFormSuccess'
    }, instance.data_table);
    
    return false;
}

DataTable.prototype.submitFormSuccess = function(request, response) {
    this.setState(response);
}

$(document).ready(function() {
    $('.data_table').each(function() {
        var element = $(this);
        
        var data_table = new DataTable(element);
    });
});