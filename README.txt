Flow
========
Flow is a framework/CMS written in PHP. It is actively developed by the author, Tommy Bolger, to further his knowledge of PHP and gives him an outlet for developing ideas. It leverages several of the latest PHP5 features for fast and efficient operation.

The goal of this framework and CMS is the following:
    * Completely avoid writing spaghetti code.
    * Separate the presentation layer from the code.    
    * Create an ecosystem that makes building a web application significantly easier via helper objects.
    * Give the developer as much flexibility as possible as to how to build a web application and not completely confine him or her to a rigid model.
    * Provide a high-performance application through the use of caching, optimized PHP code, and optimized SQL.
    * Most importantly prevent having to reinvent the wheel on every page.    

Please keep in mind that Flow is still in development. Some parts aren't fully finished, styles might be off on some pages, and some code hasn't been developed fully yet.

Features
--------
    * Code Management:
        - All objects are namespaced.    
        - 100% availability of all framework objects when needed. No need for any include or require statements.
        - Advanced HTML objects that construct complex elements.
    * Design Management:
        - Dedicated, swappable styles for each module.
        - Templating system with caching.
    * Security: 
        - Advanced ecryption for sensitive information.
        - CSRF resistant for forms.
        - SQL injection-proof.
        - Clickjacking resistant.
        - XSS resistant.
    * Caching:
        - Caching backend is fully modular.
        - Framework-level caching to avoid costly operations.
        - Caching of a fully rendered page on the file system and via one of the caching modules.
    * Advanced CSS/JS Management:    
        - CSS is combined into a single gzipped file and cached to reduce the number of requests per page.
        - JS is combined into a single gzipped file and cached to reduce the number of requests per page.
        - Support for Closure and UglifyJS for JS minification.
    * Error Handling:
        - Global catch-all exception handler. If an uncaught exception is thrown anywhere it will be caught and properly handled.
        - Global error handler. If a non-fatal error or warning is triggered it will be caught and properly handled.
        - Global fatal error handler catches and handles several types of fatal errors.
    * Request Handling:
        - Dedicated request handler that automatically sanitizes $_GET and $_POST requests.
    * Session Handling:
        - Dedicated, modular session handler.
        - Includes a module to handle sessions in the database.
    * Database Abstraction:
        - Database abstraction layer built on top of PDO.
        - Can be run on several database platforms including MySQL, PostgreSQL, and SQLite 3.
        - All queries run through the layer are prepared and executed to help prevent SQL injection.
        - Several methods available that return result sets in frequently-used data structures.
    * Form Library::
        - An advanced form object that is easy to utilize.
        - Specialized form field objects that enforce submitted data integrity.
        - Form output that can either be default html or can be bound to a template.

Questions, comments, feedback, or requests for new features are always welcome and can be sent to: tommy.bolger@gmail.com

Installation Requirements
-------------------------
In order to run Flow PHP 5.3.3 or greater and PostgreSQL v8.4, MySQL v5.1, or SQLite v3.77 or higher are required.

The following PHP modules also need to be installed to be able to use the full feature set of Flow:
    * GD
    * Memcached (not Memcache)
    * APC
    * XSL (optional)
    * XML RPC (optional)
    * PGSQL, MySQL, or SQLite
    * PDO
    * Mcrypt
    * CLI (for installation script)
    * Tidy

Installation
------------
1. Download a copy of Flow from here: https://github.com/tommy-bolger/Flow
2. Extract the contents of the package to a preferred folder accessible by your web server.
    * This location will become your installation path.
3. Configure your virtual host for the website to point to the <installation_path>/public/ directory as the web root.    
4. The following directories need to be writable recursively for the web user and your user:
    * <installation_path>/cache/
    * <installation_path>/public/assets/
    * <installation_path>/protected/ (can be reset to default permissions when the installation is finished)
5. Make sure the database Flow will be installed to is empty and accessible.
6. On the command line navigate to the <installation_path>/scripts/install/ directory and execute the install.php script by using the command: php install.php
7. Follow the instructions of the install script.
8. After installation the install directory can be removed.
9. In the url bar enter the url to your site to enter the administration module. Login with the admin user you created during installation.

Credits
-------
Flow is written by Tommy Bolger (http://www.tommybolger.com). Many thanks go out to the awesome developers from Achieve! Data Solutions, Ibex Data, and Stack Overflow for the awesome ideas that inspired this framework.

Licensing Information
---------------------
Flow is provided via the BSD 3-Clause license.

Copyright (c) 2011, Tommy Bolger
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    - Neither the name of the author nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.