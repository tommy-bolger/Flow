<?php
/**
* The assertion handler of the framework.
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
final class Assert {
    /**
     * Enables assertions within the framework.
     *
     * @return void
     */
    public static function enable() {
        assert_options(ASSERT_ACTIVE, true);
        assert_options(ASSERT_WARNING, false);
        assert_options(ASSERT_CALLBACK, array('Assert', 'throwException'));        
    }
    
    /**
     * Disables assertions within the framework.
     *
     * @return void
     */
    public static function disable() {
        assert_options(ASSERT_ACTIVE, false);
    }
    
    /**
     * A callback function for failed assertions that throws an exception to product a stack trace.
     *
     * @return void
     */
    public static function throwException($file, $line, $message) {
        throw new Exception("Assertion '{$message}' has failed.");
    }
}