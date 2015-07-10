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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "../../config.php"); 

class chat_services extends external_api {   



    public static function list_chats_parameters() {
        return new external_function_parameters(
                array('catalogname' => new external_value(PARAM_TEXT, 'The name of the catalog. By default it is "securityquestions"', VALUE_DEFAULT, 'securityquestions'))
        );
    }
    public static function list_chats() {
        global $USER;
        global $DB;
        $response = array();

        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::list_chats_parameters(), array('catalogname' => $catalogname));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);

            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $sql = 'select * from {chat}';
            //$params = array('fieldname' => $catalogname);
            $response = $DB->get_records_sql($sql);

        } catch (Exception $e) {
            $response = $e;
        }
        return $response;
    }
    public static function list_chats_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                        'id' => new external_value(PARAM_TEXT, 'id of chat'),
                        'course' => new external_value(PARAM_TEXT, 'id of course'),
                        'name' => new external_value(PARAM_TEXT, 'Name of course'),
                        'intro' => new external_value(PARAM_RAW, 'Name of course'),
                        'introformat' => new external_value(PARAM_TEXT, 'id of chat'),
                        'keepdays' => new external_value(PARAM_TEXT, 'Days to keep chat'),
                        'studentlogs' => new external_value(PARAM_TEXT, 'Logs of students'),
                        'chattime' => new external_value(PARAM_TEXT, 'Time of chat'),
                        'schedule' => new external_value(PARAM_TEXT, ''),
                        'timemodified' => new external_value(PARAM_TEXT, '')
                )
            )
        );
    }
}

class forum_services extends external_api {


    /*  ______________________________________
        _______CREATE POST_________________
    */
    public static function extra_service_forum_create_forum_discussion_post_parameters() {
        return new external_function_parameters(
                array(  'discussionid' => new external_value(PARAM_INTEGER, 'The id of the discussion of the forum.'),
                        'parentid' => new external_value(PARAM_TEXT, 'The id of the parent post. If it is a discussion post, defualt is 0.'),
                        'message' => new external_value(PARAM_TEXT, 'The content mmesage of the post.'))
        );
    }
    public static function extra_service_forum_create_forum_discussion_post($discussionid, $parentid, $message) {
        global $USER;
        global $DB;
        //$response = array();
        $response = true;
        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::extra_service_forum_create_forum_discussion_post_parameters(), array('discussionid' => $discussionid, 'parentid' => $parentid, 'message' => $message));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);


            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $record = new stdClass();
            $record->discussion     = $discussionid;
            $record->parent = $parentid;
            $record->userid = $USER->id;
            $subject = $DB->get_record_sql('SELECT subject FROM {forum_posts} WHERE id = :parentid', array('parentid' => $parentid ));
            $record->subject = 'Re: '.$subject->subject;
            $record->message = $message;
            $record->created = time();
            $record->modified = time();
            $record->messageformat = 1;
            $lastinsert = $DB->insert_record('forum_posts', $record, false);
            $response = $record;
        } catch (Exception $e) {
            $response = $e;
        }


        return $response;
    }
    public static function extra_service_forum_create_forum_discussion_post_returns() {
        //return new external_value(PARAM_BOOL, 'The name of the catalog. By default it is "securityquestions"');
        
        return  new external_single_structure(
                        array(
                            'discussion' => new external_value(PARAM_TEXT, 'id of the discussion.'),
                            'parent' => new external_value(PARAM_TEXT, 'id of the parent post.'),
                            'userid' => new external_value(PARAM_TEXT, 'id of the posting user.'),
                            'subject' => new external_value(PARAM_RAW, 'Subject of the post.'),
                            'message' => new external_value(PARAM_TEXT, 'The message of the post.'),
                            'created' => new external_value(PARAM_TEXT, 'Datetime of post creation.'),
                            'modified' => new external_value(PARAM_INTEGER, 'Datetime of post last modification.')
                        )
                    );
    }





    /*  ______________________________________
        _______UPDATE POST_________________
    */
    public static function extra_service_forum_update_forum_discussion_post_parameters() {
        return new external_function_parameters(
                array(  'postid' => new external_value(PARAM_INTEGER, 'The id of the post.'),
                        'message' => new external_value(PARAM_TEXT, 'The content mmesage of the post.'))
        );
    }
    public static function extra_service_forum_update_forum_discussion_post($postid, $message) {
        global $USER;
        global $DB;
        $response = new stdClass();
        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::extra_service_forum_update_forum_discussion_post_parameters(), array('postid' => $postid, 'message' => $message));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);


            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $record = new stdClass();
            $record->id     = required_param('postid', PARAM_INTEGER);
            $record->message = required_param('message', PARAM_TEXT);
            $record->modified = time();
                


            if ($DB->record_exists('forum_posts', array('id' => $record->id))) {
                $response->result = $DB->update_record('forum_posts', $record, false);
                $response->message = "OK.";
            }else{
                $response->result = false;
                $response->message = "The id doesn't exists.";
            }


        } catch (Exception $e) {
            $response->result = false;
            $response->message = $e->message;
        }
        return $response;
    }
    public static function extra_service_forum_update_forum_discussion_post_returns() {
        //        return new external_value(PARAM_BOOL, 'The result of the update.');
        return new external_single_structure(
                        array(
                            'result' => new external_value(PARAM_BOOL, 'Boolean result of the like action.'),
                            'message' => new external_value(PARAM_TEXT, 'Message result of the like action.')
                        )
                    );
    }





    /*  ______________________________________
        _______DELETE POST_________________
    */
    public static function extra_service_forum_delete_forum_discussion_post_parameters() {
        return new external_function_parameters(
                array(  'postid' => new external_value(PARAM_INTEGER, 'The id of the post.'))
        );
    }
    public static function extra_service_forum_delete_forum_discussion_post($postid) {
        global $USER;
        global $DB;
        $response = new stdClass();
        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::extra_service_forum_delete_forum_discussion_post_parameters(), array('postid' => $postid));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);


            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $record = new stdClass();
            $record->id     = required_param('postid', PARAM_INTEGER);

            if ($DB->record_exists('forum_posts', array('id' => $record->id))) {
                try {
                    $transaction = $DB->start_delegated_transaction();
                    // Delete a recrods from forum_posts and loca_forum_post_like
                    $DB->delete_records('forum_posts', array('id' => $record->id));
                    $DB->delete_records('local_forum_post_like', array('forumpostid' => $record->id, 'userid' => $USER->id));

                    // Assuming the both inserts work, we get to the following line.
                    $transaction->allow_commit();
                } catch(Exception $e) {
                    $transaction->rollback($e);
                    $response->result = false;
                    $response->message = $e->message;
                }
                $response->result = $DB->delete_records('forum_posts', array('id' => $record->id));
                $response->message = "OK";
            }else{
                $response->result = false;
                $response->message = "Post doesn't exists.";
            }

        } catch (Exception $e) {
            $response->result = false;
            $response->message = $e->message;
        }
        return $response;
    }
    public static function extra_service_forum_delete_forum_discussion_post_returns() {
        //        return new external_value(PARAM_BOOL, 'The result of the delete action.');
        return new external_single_structure(
                        array(
                            'result' => new external_value(PARAM_BOOL, 'Boolean result of the like action.'),
                            'message' => new external_value(PARAM_TEXT, 'Message result of the like action.')
                        )
                    );
    }





    /*  ______________________________________
        _______LIKE METHOD_________________
    */
    public static function extra_service_forum_like_forum_discussion_post_parameters() {
        return new external_function_parameters(
                array(  'postid' => new external_value(PARAM_INTEGER, 'The id of the post.'))
        );
    }
    public static function extra_service_forum_like_forum_discussion_post($postid) {
        global $USER;
        global $DB;
        $response = new stdClass();
        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::extra_service_forum_like_forum_discussion_post_parameters(), array('postid' => $postid));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);


            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $record = new stdClass();
            $record->forumpostid     = $postid;
            $record->userid = $USER->id;
            if ($DB->record_exists('forum_posts', array('id' => $record->forumpostid))) {
                if (!$DB->record_exists('local_forum_post_like', array('forumpostid' => $record->forumpostid, 'userid' => $record->userid))) {
                    $DB->insert_record('local_forum_post_like', $record, false);
                    $response->result = true;
                    $response->message = "OK";
                }else{
                    $response->result = false;
                    $response->message = "Already liked";
                }
            }else{
                $response->result = false;
                $response->message = "The Post doesn't exist";
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }
    public static function extra_service_forum_like_forum_discussion_post_returns() {
        // return new external_value(PARAM_BOOL, 'The result of the like action.');
        return new external_single_structure(
                        array(
                            'result' => new external_value(PARAM_BOOL, 'Boolean result of the like action.'),
                            'message' => new external_value(PARAM_TEXT, 'Message result of the like action.')
                        )
                    );
    }



    /*  ______________________________________
        _______UNLIKE METHOD_________________
    */
    public static function extra_service_forum_unlike_forum_discussion_post_parameters() {
        return new external_function_parameters(
                array(  'postid' => new external_value(PARAM_INTEGER, 'The id of the post.'))
        );
    }
    public static function extra_service_forum_unlike_forum_discussion_post($postid) {
        global $USER;
        global $DB;
        $response = new stdClass();
        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::extra_service_forum_unlike_forum_discussion_post_parameters(), array('postid' => $postid));

            //Context validation
            //OPTIONAL but in most web service it should present
            $context = get_context_instance(CONTEXT_USER, $USER->id);
            self::validate_context($context);


            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $record = new stdClass();
            $record->userid     = $USER->id;
            $record->forumpostid = required_param('postid', PARAM_INTEGER);

            
            if ($DB->record_exists('local_forum_post_like', array('forumpostid' => $record->forumpostid, 'userid' => $record->userid))) {
                $response->result = $DB->delete_records('local_forum_post_like', array('forumpostid' => $record->forumpostid, 'userid' => $record->userid));
                $response->message = 'OK';
            }else{
                $response->result = false;
                $response->message = "Like doesn't exist";
            }

        } catch (Exception $e) {
            throw $e;
        }
        return $response;
    }
    public static function extra_service_forum_unlike_forum_discussion_post_returns() {
        return new external_single_structure(
                        array(
                            'result' => new external_value(PARAM_BOOL, 'Boolean result of the unlike action.'),
                            'message' => new external_value(PARAM_TEXT, 'Message result of the unlike action.')
                        )
                    );
    }



    /*  ______________________________________
        _______UNLIKE METHOD_________________
    */
    public static function extra_service_forum_get_forum_discussion_posts_parameters() {
        return new external_function_parameters(
            array(
                'discussionid' => new external_value(PARAM_INT, 'discussion ID', VALUE_REQUIRED),
                'sortby' => new external_value(PARAM_ALPHA, 'sort by this element: id, created or modified', VALUE_DEFAULT, 'created'),
                'sortdirection' => new external_value(PARAM_ALPHA, 'sort direction: ASC or DESC', VALUE_DEFAULT, 'DESC')
            )
        );
    }
    public static function extra_service_forum_get_forum_discussion_posts($discussionid, $sortby = "created", $sortdirection = "DESC") {
        global $CFG, $DB, $USER;

        $warnings = array();
            //var_dump($_POST["wstoken"]);
            //var_dump($CFG->wwwroot);
            
            $sortallowedvalues = array('id', 'created', 'modified');
            if (!in_array($sortby, $sortallowedvalues)) {
                throw new invalid_parameter_exception('Invalid value for sortby parameter (value: ' . $sortby . '),' .
                    'allowed values are: ' . implode(',', $sortallowedvalues));
            }

            $sortdirection = strtoupper($sortdirection);
            $directionallowedvalues = array('ASC', 'DESC');
            if (!in_array($sortdirection, $directionallowedvalues)) {
                throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $sortdirection . '),' .
                    'allowed values are: ' . implode(',', $directionallowedvalues));
            }

            $discussion = $DB->get_record('forum_discussions', array('id' => $discussionid), '*', MUST_EXIST);
            $forum = $DB->get_record('forum', array('id' => $discussion->forum), '*', MUST_EXIST);            
            $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
            require_once($CFG->dirroot . "/mod/forum/lib.php");
            

            // Validate the module context. It checks everything that affects the module visibility (including groupings, etc..).
            $modcontext = context_module::instance($cm->id);
            self::validate_context($modcontext);

            // This require must be here, see mod/forum/discuss.php.
            require_once($CFG->dirroot . "/mod/forum/lib.php");

            // Check they have the view forum capability.
            require_capability('mod/forum:viewdiscussion', $modcontext, null, true, 'noviewdiscussionspermission', 'forum');

            if (! $post = forum_get_post_full($discussion->firstpost)) {
                throw new moodle_exception('notexists', 'forum');
            }

            // This function check groups, qanda, timed discussions, etc.
            if (!forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
                throw new moodle_exception('noviewdiscussionspermission', 'forum');
            }

            $canviewfullname = has_capability('moodle/site:viewfullnames', $modcontext);

            // We will add this field in the response.
            $canreply = forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext);

            $forumtracked = forum_tp_is_tracked($forum);

            $sort = 'p.' . $sortby . ' ' . $sortdirection;
            $posts = forum_get_all_discussion_posts($discussion->id, $sort, $forumtracked);

            foreach ($posts as $pid => $post) {

                if (!forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
                    $warning = array();
                    $warning['item'] = 'post';
                    $warning['itemid'] = $post->id;
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'You can\'t see this post';
                    $warnings[] = $warning;
                    continue;
                }

                // Function forum_get_all_discussion_posts adds postread field.
                // Note that the value returned can be a boolean or an integer. The WS expects a boolean.
                if (empty($post->postread)) {
                    $posts[$pid]->postread = false;
                } else {
                    $posts[$pid]->postread = true;
                }

                $posts[$pid]->canreply = $canreply;
                if (!empty($posts[$pid]->children)) {
                    $posts[$pid]->children = array_keys($posts[$pid]->children);
                } else {
                    $posts[$pid]->children = array();
                }

                $user = new stdclass();
                $user->id = $post->userid;
                $user = username_load_fields_from_object($user, $post);
                $post->userfullname = fullname($user, $canviewfullname);

                // We can have post written by users that are deleted. In this case, those users don't have a valid context.
                $usercontext = context_user::instance($user->id, IGNORE_MISSING);
                if ($usercontext) {
                    $post->userpictureurl = moodle_url::make_webservice_pluginfile_url(
                            $usercontext->id, 'user', 'icon', null, '/', 'f1')->out(false);
                } else {
                    $post->userpictureurl = '';
                }

                // Rewrite embedded images URLs.
                list($post->message, $post->messageformat) =
                    external_format_text($post->message, $post->messageformat, $modcontext->id, 'mod_forum', 'post', $post->id);

                // List attachments.
                if (!empty($post->attachment)) {
                    $post->attachments = array();

                    $fs = get_file_storage();
                    if ($files = $fs->get_area_files($modcontext->id, 'mod_forum', 'attachment', $post->id, "filename", false)) {
                        foreach ($files as $file) {
                            $filename = $file->get_filename();
                            $fileurl = moodle_url::make_webservice_pluginfile_url(
                                            $modcontext->id, 'mod_forum', 'attachment', $post->id, '/', $filename);

                            $post->attachments[] = array(
                                'filename' => $filename,
                                'mimetype' => $file->get_mimetype(),
                                'fileurl'  => $fileurl->out(false)
                            );
                        }
                    }
                }
                // LIKES
                $post->likes = $DB->count_records('local_forum_post_like', array('forumpostid' => $post->id));
                $post->liked_already = $DB->record_exists('local_forum_post_like', array('forumpostid' => $post->id, "userid" => $USER->id));

                $posts[$pid] = (array) $post;
            }

            $result = array();
            $result['posts'] = $posts;
            $result['warnings'] = $warnings;
            return $result;
    }
    public static function extra_service_forum_get_forum_discussion_posts_returns() {
        return new external_single_structure(
            array(
                'posts' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'Post id'),
                                'discussion' => new external_value(PARAM_INT, 'Discussion id'),
                                'parent' => new external_value(PARAM_INT, 'Parent id'),
                                'userid' => new external_value(PARAM_INT, 'User id'),
                                'created' => new external_value(PARAM_INT, 'Creation time'),
                                'modified' => new external_value(PARAM_INT, 'Time modified'),
                                'mailed' => new external_value(PARAM_INT, 'Mailed?'),
                                'subject' => new external_value(PARAM_TEXT, 'The post subject'),
                                'message' => new external_value(PARAM_RAW, 'The post message'),
                                'messageformat' => new external_format_value('message'),
                                'messagetrust' => new external_value(PARAM_INT, 'Can we trust?'),
                                'attachment' => new external_value(PARAM_RAW, 'Has attachments?'),
                                'attachments' => new external_multiple_structure(
                                    new external_single_structure(
                                        array (
                                            'filename' => new external_value(PARAM_FILE, 'file name'),
                                            'mimetype' => new external_value(PARAM_RAW, 'mime type'),
                                            'fileurl'  => new external_value(PARAM_URL, 'file download url')
                                        )
                                    ), 'attachments', VALUE_OPTIONAL
                                ),
                                'totalscore' => new external_value(PARAM_INT, 'The post message total score'),
                                'mailnow' => new external_value(PARAM_INT, 'Mail now?'),
                                'children' => new external_multiple_structure(new external_value(PARAM_INT, 'children post id')),
                                'canreply' => new external_value(PARAM_BOOL, 'The user can reply to posts?'),
                                'postread' => new external_value(PARAM_BOOL, 'The post was read'),
                                'userfullname' => new external_value(PARAM_TEXT, 'Post author full name'),
                                'userpictureurl' => new external_value(PARAM_URL, 'Post author picture.', VALUE_OPTIONAL),
                                'likes' => new external_value(PARAM_INT, 'Number of post likes'),
                                'liked_already' => new external_value(PARAM_BOOL, 'If the user has already liked the post.')
                            ), 'post'
                        )
                    ),
                'warnings' => new external_warnings()
            )
        );
        // return new external_single_structure(
        //                 array(
        //                     'result' => new external_value(PARAM_BOOL, 'Boolean result of the unlike action.'),
        //                     'message' => new external_value(PARAM_TEXT, 'Message result of the unlike action.')
        //                 )
        //             );
    }


    
}