<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// We defined the web service functions to install.
$functions = array(
        'get_latest_course' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'get_latest_course',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Return the latest course',
                'type'        => 'read',
        ),

        'get_current_course_and_status_by_user' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'get_current_course_and_status_by_user',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Return the current course of user and if is the first time in the course',
                'type'        => 'read',
        ),

        'update_first_time_in_resource' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'update_first_time_in_resource',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Update the first time in a resource',
                'type'        => 'write',
        ),

        'get_user_course_info' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'get_user_course_info',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Get course information about a user',
                'type'        => 'read',
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Course Plugin' => array(
                'functions' => array (
                        'get_latest_course',
                        'get_current_course_and_status_by_user',
                        'update_first_time_in_resource',
                        'get_user_course_info'
                        ),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);