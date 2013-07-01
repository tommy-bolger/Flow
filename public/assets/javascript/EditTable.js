/**
* An object that handles the dynamic refresh of the framework DataTable.
* Copyright (c) 2013, Tommy Bolger
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
function EditTable(dom_element) {
    var instance = this;
    
    this.edit_table = dom_element;
    this.add_link = $('.add_link', this.edit_table);
    
    this.move_links = null;
    this.initializeMoveLinks();
        
    this.delete_links = null;
    this.initializeDeleteLinks();

    DataTable.call(this, $('.data_table', this.edit_table));
};

EditTable.prototype = Object.create(DataTable.prototype);

EditTable.prototype.constructor = EditTable;

EditTable.prototype.initializeMoveLinks = function() {
    this.move_links = $('.move', this.edit_table);

    $(this.move_links).bind('click', {
        instance: this
    }, this.moveRecord);
};

EditTable.prototype.initializeDeleteLinks = function() {
    this.delete_links = $('.delete', this.edit_table);

    $(this.delete_links).bind('click', {
        instance: this
    }, this.deleteRecord);
};

EditTable.prototype.setState = function(state) {
    DataTable.prototype.setState.call(this, state);

    if(state.hasOwnProperty('body')) {
        this.initializeMoveLinks();
        
        this.initializeDeleteLinks();
    }
    
    if(state.hasOwnProperty('add_link')) {
        this.add_link.html(state.add_link);
    }
};

EditTable.prototype.moveRecord = function(event) {
    event.preventDefault();

    var instance = event.data.instance;
    
    var url = instance.convertUrl($(this).attr('href'));

    Request.get(url, {}, {
        context: instance,
        method: 'moveRecordSuccess'
    }, instance.edit_table);
    
    return false;
};

EditTable.prototype.moveRecordSuccess = function(request, response) {
    this.setState(response);
};

EditTable.prototype.deleteRecord = function(event) {
    event.preventDefault();
    
    var delete_record = confirm('Press OK to delete this record.');

    if(delete_record) {
        var instance = event.data.instance;
        
        var url = instance.convertUrl($(this).attr('href'));
        
        Request.get(url, {}, {
            context: instance,
            method: 'deleteRecordSuccess'
        }, instance.edit_table);
    }
    
    return false;
};

EditTable.prototype.deleteRecordSuccess = function(request, response) {
    this.setState(response);
};