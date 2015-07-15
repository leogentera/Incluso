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
        'get_top_users' => array(
                'classname'   => 'leaderboard_services',
                'methodname'  => 'get_top_users',
                'classpath'   => 'local/leaderboard/externallib.php',
                'description' => 'Return a list of the N top users',
                'type'        => 'read',
        ),
        'get_actual_position' => array(
                'classname'   => 'leaderboard_services',
                'methodname'  => 'get_actual_position',
                'classpath'   => 'local/leaderboard/externallib.php',
                'description' => 'Return the actual position of a user',
                'type'        => 'read',
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Leaderboard Services' => array(
                'functions' => array (	'get_top_users',
                						'get_actual_position'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);