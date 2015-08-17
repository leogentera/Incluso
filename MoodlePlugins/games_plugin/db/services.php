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
        'get_avatar_configuration' => array(
                'classname'   => 'games_services',
                'methodname'  => 'get_avatar_configuration',
                'classpath'   => 'local/games_plugin/externallib.php',
                'description' => 'Return the user avatar configuration',
                'type'        => 'read',
        ),
        'create_avatar_configuration' => array(
                'classname'   => 'games_services',
                'methodname'  => 'add_avatar_configuration',
                'classpath'   => 'local/games_plugin/externallib.php',
                'description' => 'Creates an avatar configuration log',
                'type'        => 'write',
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Games Services' => array(
                'functions' => array (
                        'get_avatar_configuration',
                        'create_avatar_configuration'
                        ),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);