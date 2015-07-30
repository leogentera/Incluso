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
        'get_quiz' => array(
                'classname'   => 'quiz_plugin',
                'methodname'  => 'get_quiz',
                'classpath'   => 'local/activities_plugin/externallib.php',
                'description' => 'Returns the quiz given a quizid',
                'type'        => 'read',
        ),
        
        'get_quiz_result' => array(
        		'classname'   => 'quiz_plugin',
        		'methodname'  => 'get_quiz_result',
        		'classpath'   => 'local/activities_plugin/externallib.php',
        		'description' => 'Returns the quiz results',
        		'type'        => 'read',
        ),
        
        'save_quiz' => array(
        		'classname'   => 'quiz_plugin',
        		'methodname'  => 'save_quiz',
        		'classpath'   => 'local/activities_plugin/externallib.php',
        		'description' => 'Save answers on the db',
        		'type'        => 'write',
        ),

        'get_activity_summary' => array(
                        'classname'   => 'activitiesSummary_plugin',
                        'methodname'  => 'get_activity_summary',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the summary of activity',
                        'type'        => 'read',
        ),

);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Quiz service' => array(
                'functions' => array (	'get_quiz','get_quiz_result'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'ActivitySummary service' => array(
                'functions' => array ('get_activity_summary'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);