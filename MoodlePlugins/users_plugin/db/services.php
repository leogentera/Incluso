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
        'generate_user' => array(
                'classname'   => 'user_services',
                'methodname'  => 'generate_user',
                'classpath'   => 'local/users_plugin/externallib.php',
                'description' => 'Returns a generated user name',
                'type'        => 'read',
        ),
        'get_user_by_facebookid' => array(
        		'classname'   => 'user_services',
        		'methodname'  => 'get_user_by_facebookid',
        		'classpath'   => 'local/users_plugin/externallib.php',
        		'description' => 'Returns a generated user name',
        		'type'        => 'read',
        ),

        'set_token_valid_time' => array(
                        'classname'   => 'user_services',
                        'methodname'  => 'set_token_valid_time',
                        'classpath'   => 'local/users_plugin/externallib.php',
                        'description' => 'Update token valid time for use.',
                        'type'        => 'write',
        ),
        
);
// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Users Services' => array(
                'functions' => array (
                        'generate_user', 
                        'get_user_by_facebookid',
                        'set_token_valid_time'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),
);