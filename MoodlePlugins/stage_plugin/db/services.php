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
        'get_challenges_stage' => array(
                'classname'   => 'stage_services',
                'methodname'  => 'get_challenges_stage',
                'classpath'   => 'local/stage_plugin/externallib.php',
                'description' => 'Return a list with the name and summary of principal activities in sections',
                'type'        => 'read',
        ),
        'get_activities_status_by_challenge' => array(
                'classname'   => 'stage_services',
                'methodname'  => 'get_activities_status_by_challenge',
                'classpath'   => 'local/stage_plugin/externallib.php',
                'description' => 'Return a list of actitivies with status in a challenge',
                'type'        => 'read',
        ),
        'get_stages_by_course' => array(
                'classname'   => 'stage_services',
                'methodname'  => 'get_stages_by_course',
                'classpath'   => 'local/stage_plugin/externallib.php',
                'description' => 'Return a list of stages in a course',
                'type'        => 'read',
        ),
        'get_activities_by_challenge' => array(
                'classname'   => 'stage_services',
                'methodname'  => 'get_activities_by_challenge',
                'classpath'   => 'local/stage_plugin/externallib.php',
                'description' => 'Return a list of activities in a challenge',
                'type'        => 'read',
        ),
        'get_stages_by_user_and_course' => array(
                'classname'   => 'stage_services',
                'methodname'  => 'get_stages_by_user_and_course',
                'classpath'   => 'local/stage_plugin/externallib.php',
                'description' => 'Return a list of stages in a course with the status of first time',
                'type'        => 'read',
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Stage Services' => array(
                'functions' => array (
                        'get_challenges_stage',
                        'get_activities_status_by_challenge',
                        'get_stages_by_course',
                        'get_activities_by_challenge',
                        'get_stages_by_user_and_course'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);