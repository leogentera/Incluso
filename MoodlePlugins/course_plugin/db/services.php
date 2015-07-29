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

        'is_first_time_in_course' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'is_first_time_in_course',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Return 1 or 0 if is the first time in a course',
                'type'        => 'read',
        ),

        'update_first_time_in_resource' => array(
                'classname'   => 'course_plugin',
                'methodname'  => 'update_first_time_in_resource',
                'classpath'   => 'local/course_plugin/externallib.php',
                'description' => 'Update the first time in a resource',
                'type'        => 'write',
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Course Plugin' => array(
                'functions' => array (
                        'get_latest_course',
                        'is_first_time_in_course',
                        'update_first_time_in_resource'
                        ),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);