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
        'list_chats' => array(
                'classname'   => 'chat_services',
                'methodname'  => 'list_chats',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Return a list of the available chats.',
                'type'        => 'read',
        ),
        'extra_service_forum_create_forum_discussion_post' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_create_forum_discussion_post',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Creates a post on a discussion.',
                'type'        => 'read',
        ),
        'extra_service_forum_update_forum_discussion_post' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_update_forum_discussion_post',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Modifies the post of a discussion.',
                'type'        => 'write',
        ),
        'extra_service_forum_delete_forum_discussion_post' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_delete_forum_discussion_post',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Deletes a post of a discussion.',
                'type'        => 'write',
        ),
        'extra_service_forum_like_forum_discussion_post' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_like_forum_discussion_post',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Likes a post on a discussion.',
                'type'        => 'read',
        ),
        'extra_service_forum_unlike_forum_discussion_post' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_unlike_forum_discussion_post',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Unlikes a previous liked post on a discussion.',
                'type'        => 'read',
        ),
        'extra_service_forum_get_forum_discussion_posts' => array(
                'classname'   => 'forum_services',
                'methodname'  => 'extra_service_forum_get_forum_discussion_posts',
                'classpath'   => 'local/extra_services/externallib.php',
                'description' => 'Returns a list of forum posts for a discussion..',
                'type'        => 'read',
        ),
);
// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Chat Services' => array(
                'functions' => array ('home','list_chats'),
                'restrictedusers' => 0,
                'enabled'=>1,
        ),
        'Forum Services' => array(
                'functions' => array (  'extra_service_forum_create_forum_discussion_post', 
                                        'extra_service_forum_update_forum_discussion_post', 
                                        'extra_service_forum_delete_forum_discussion_post', 
                                        'extra_service_forum_like_forum_discussion_post',
                                        'extra_service_forum_unlike_forum_discussion_post',
                                        'extra_service_forum_get_forum_discussion_posts'
                                        ),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);