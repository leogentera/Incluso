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

        'get_activity_id_and_name' => array(
                        'classname'   => 'activitiesSummary_plugin',
                        'methodname'  => 'get_activity_id_and_name',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the id and type of activity',
                        'type'        => 'read',
        ),

        'get_all_activities_by_course' => array(
                        'classname'   => 'activitiesSummary_plugin',
                        'methodname'  => 'get_all_activities_by_course',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns all the activities in a course',
                        'type'        => 'read',
        ),

        'get_assignment' => array(
                        'classname'   => 'assignment_plugin',
                        'methodname'  => 'get_assignment',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the assignment data',
                        'type'        => 'read',
        ),

        'get_label' => array(
                        'classname'   => 'label_plugin',
                        'methodname'  => 'get_label',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the label data',
                        'type'        => 'read',
        ),

        'get_page' => array(
                        'classname'   => 'page_plugin',
                        'methodname'  => 'get_page',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the page data',
                        'type'        => 'read',
        ),

        'get_url' => array(
                        'classname'   => 'url_plugin',
                        'methodname'  => 'get_url',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns the url data',
                        'type'        => 'read',
        ),

        'get_available_chats' => array(
                        'classname'   => 'chat_plugin',
                        'methodname'  => 'get_available_chats',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Return a list of the available chats.',
                        'type'        => 'read',
        ),

        'get_forum_discussion_posts' => array(
                        'classname'   => 'forum_plugin',
                        'methodname'  => 'get_forum_discussion_posts',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Returns a list of forum posts for a discussion.',
                        'type'        => 'read',
        ),        

        'create_forum_discussion_post' => array(
                        'classname'   => 'forum_plugin',
                        'methodname'  => 'create_forum_discussion_post',
                        'classpath'   => 'local/activities_plugin/externallib.php',
                        'description' => 'Save a new post on db',
                        'type'        => 'write',
        ),
        
        'log_stars' => array(
        		'classname'   => 'stars_log_plugin',
        		'methodname'  => 'log_stars',
        		'classpath'   => 'local/activities_plugin/externallib.php',
        		'description' => 'Logs earned stars.',
        		'type'        => 'write',
        ),
        
        'get_stars_log' => array(
        		'classname'   => 'stars_log_plugin',
        		'methodname'  => 'get_stars_log',
        		'classpath'   => 'local/activities_plugin/externallib.php',
        		'description' => 'Returns the earned stars',
        		'type'        => 'read',
        ),
        
        'get_stars_per_module' => array(
        		'classname'   => 'stars_log_plugin',
        		'methodname'  => 'get_stars_per_module',
        		'classpath'   => 'local/activities_plugin/externallib.php',
        		'description' => 'Returns the earned stars',
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
                'functions' => array(
                        'get_activity_summary',
                        'get_activity_id_and_name',
                        'get_all_activities_by_course'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Assignment service' => array(
                'functions' => array(
                        'get_assignment'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Label service' => array(
                'functions' => array(
                        'get_label'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Page service' => array(
                'functions' => array(
                        'get_page'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Url service' => array(
                'functions' => array(
                        'get_url'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Chat service' => array(
                'functions' => array(
                        'get_available_chats'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),

        'Forum service' => array(
                'functions' => array(
                        'get_forum_discussion_posts',
                        'create_forum_discussion_post'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),
		'Stars service' => array (
				'functions' => array (
						'log_stars',
						'get_stars_log',
						'get_stars_per_module' 
				),
				'restrictedusers' => 0,
				'enabled' => 1,
		)
);