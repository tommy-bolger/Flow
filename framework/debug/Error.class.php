<?php
class Error {
    /**
     * The exception handler of the framework.
     *
     * @return void
     */
    public static function handleException(Exception $exception) {
        self::display(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }

    /**
     * Displays framework/applications errors and exceptions and stops execution of the framework.
     *
     * @param integer $error_code The code specified when the error or exception was thrown.
     * @param string $error_message The error message.
     * @param string $error_file (optional) The file that the error or exception occurred at.
     * @param integer $error_line (optional) The line that the error occurred at.
     * @param string|array $error_trace (optional) The stack trace of the application execution from beginning to when the error was encountered. Can either be a string for exceptions or arrays for errors.                          
     * @return void
     */
    public static function display($error_code, $error_message, $error_file = '', $error_line = '', $error_trace = '') {
        $error_output = "
            <h1>An Error has Occurred</h1>
            <strong>Message:</strong><br />
            <hr>
            <pre>{$error_message}</pre><br />
            <strong>Code:</strong><br />
            <hr>
            <pre>{$error_code}</pre><br />
            <strong>File:</strong><br />
            <hr>
            <pre>{$error_file}</pre><br />
            <strong>Line:</strong><br />
            <hr>
            <pre>{$error_line}</pre>
        ";
        
        if(!empty($error_trace)) {
            if(is_array($error_trace)) {
                $error_trace = print_r($error_trace, true);
            }
        
            $error_output .= "
                <br />
                <strong>Trace:</strong><br />
                <hr>
                <pre>{$error_trace}</pre>
            ";
        }
        
        error_log($error_output);
    
        if(Framework::getEnvironment() != 'production') {
            print($error_output);
        }
        
        exit;
    }
}