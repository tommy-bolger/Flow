Flow
========
Flow is a framework/CMS written in PHP. It is actively developed by the author to further his knowledge of PHP and gives him an outlet for developing ideas. It leverages several of the latest PHP5 features for fast and efficient operation.

The goal of this framework and CMS is the following:
    * Completely avoid writing spaghetti code.
    * Separate the presentation layer from the code.    
    * Create an ecosystem that makes building a web application significantly easier via helper objects.
    * Give the developer as much flexibility as possible as to how to build a web application and not completely confine him or her to a rigid model.
    * Provide a high-performance application through the use of caching, optimized PHP code, and optimized SQL.
    * Most importantly prevent havign to reinvent the wheel on every page.    

Please keep in mind that Flow is still in development. Some parts aren't fully finished, styles might be off on some pages, and some code hasn't been developed fully yet.

Features
--------
    * Code Management:
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
        - Page caching on the file system and via one of the caching modules.
    * Advanced CSS/JS Management:    
        - CSS is combined into a single gzipped file and cached to reduce the number of requests per page.
        - JS is combined into a single gzipped file and cached to reduce the number of requests per page.
    * Error Handling:
        - Global catch-all exception handler. If an uncaught exception is thrown anywhere it will be caught and properly handled.
        - Global error handler. If a non-fatal error or warning is triggered it will be caught and properly handled.
    * Request Handling
        - Dedicated request handler that automatically sanitizes $_GET and $_POST requests.
    * Session Handling
        - Dedicated, modular session handler.
    * Database Abstraction
        - Database abstraction layer built on top of PDO.
        - All queries run through the layer are prepared and executed to help prevent SQL injection.
        - Several methods available that return result sets in frequently-used data structures.
    * Form Library:
        - An advanced form object that is easy to utilize.
        - Specialized form field objects that enforce submitted data integrity.
        - Form output that can either be default html or can be bound to a template.

Questions, comments, feedback, or requests for new features are always welcome and can be sent to: tommy.bolger@gmail.com

Installation Requirements
-------------------------
In order to run Flow PHP 5.3.2 or greater and PostgreSQL 8.4 or higher are required.

The following PHP modules also need to be installed to be able to use the full feature set of Flow:
    * GD
    * Memcached (not Memcache)
    * APC
    * XSL (optional)
    * XML RPC (optional)
    * PGSQL
    * PDO
    * Mcrypt
    * CLI (for installation script)

Installation
------------
1. Download a copy of Flow from here: https://github.com/tommy-bolger/Flow
2. Extract the contents of the package to a preferred folder accessible by your web server.
    * The folder must be the root of your website.
3. The following directory paths relative to the site root need to be writable recursively for the web user and your user:
    * cache/
    * assets/
    * framework/core/ (can be reset to default permissions when the installation is finished)
4. Make sure the database Flow will be installed to is empty and accessible.
5. On the command line navigate to the install/ directory relative to the site root and execute the install.php script by using the command: php install.php
6. Follow the instructions of the install script.
7. After installation the install directory can be removed.
8. In the url bar enter the url to your site with '?page=AdminLogin' (without quotes) to enter the administration module. Login with the admin user you created during installation.

Credits
-------
Flow is written by Tommy Bolger (http://www.tommybolger.com). Many thanks go out to the awesome developers from Achieve! Data Solutions, Ibex Data, and Stack Overflow for the awesome ideas that inspired this framework.