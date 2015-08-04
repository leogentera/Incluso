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
require_once($CFG->dirroot . '/badges/lib/awardlib.php');

class badge_services extends external_api {   



    public static function get_earned_badges_parameters() {
        return new external_function_parameters(
                array('id' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false),
        		'moodleurl' => new external_value(PARAM_TEXT, 'Moodle URL', VALUE_REQUIRED, null, false))
        );
    }
    public static function get_earned_badges($id, $moodleurl) {
        global $USER;
        global $DB;
        $response = array();

        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::get_earned_badges_parameters(), array('id' => $id, 'moodleurl'=>$moodleurl));

            //Context validation
            //OPTIONAL but in most web service it should present
//             $context = get_context_instance(CONTEXT_USER, $USER->id);
//             self::validate_context($context);

            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $sql = 'select bi.id id, bi.badgeid badgeid, ba.name name, ba.description description, bp.points points, (select count(*) from {badge_issued} bi_count where bi_count.badgeid = bi.badgeid  ) earned_times, bi.dateissued dateissued, 
					concat("/pluginfile.php/",contextid, "/badges/badgeimage/", ba.id, "/f1") pictureroute '.
    				'from {badge} ba, {badge_issued} bi, {badge_points} bp , (select distinct contextid, itemid badgeid from mdl_files where filearea="badgeimage") context '.
    				'where bp.badgeid=bi.badgeid and ba.id=bi.badgeid and bi.userid=:id and context.badgeid=bi.badgeid';
            //$params = array('fieldname' => $catalogname);
            $response = $DB->get_records_sql($sql, array('id' => $id ));
            
            $newresponse=array();
            foreach($response as $badge){
            	$file = file_get_contents("$moodleurl/$badge->pictureroute");
            	$file = base64_encode($file);
            	$badge->badgeimage=$file;
            	array_push($newresponse, $badge);
            }
           
            $response=$newresponse;
            //return array("sql"=>$sql);
        } catch (Exception $e) {
            $response = $e;
        }
        return $response;
    }
    public static function get_earned_badges_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
    							'id' => new external_value(PARAM_TEXT, 'id of the earned badge'),
    							'badgeid' => new external_value(PARAM_TEXT, 'id of the badge'),
                				'badgeimage' => new external_value(PARAM_TEXT, 'picture of the badge'),
    							'name' => new external_value(PARAM_TEXT, 'Badge\'s name'),
    							'description' => new external_value(PARAM_TEXT, 'Badge\'s description'),
                				'earned_times' => new external_value(PARAM_TEXT, 'Times that this badge has been earned'),
                				'points' => new external_value(PARAM_TEXT, 'Points for getting a this badge'),
                				'dateissued' => new external_value(PARAM_TEXT, 'Time (in millis) indicating the date when the badge was earned'),
    					)
            )
        );
    }
    
    public static function get_posible_badges_to_earn_parameters() {
    	return new external_function_parameters(
    			array('id' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false),
    			'courseid' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false),
    			'moodleurl' => new external_value(PARAM_TEXT, 'Moodle URL', VALUE_REQUIRED, null, false))
    	);
    }
    public static function get_posible_badges_to_earn($id, $courseid, $moodleurl) {
    	global $USER;
    	global $DB;
    	$response = array();
    
    	try {
    		//Parameter validation
    		//REQUIRED
    		$params = self::validate_parameters(self::get_posible_badges_to_earn_parameters(), array('id' => $id,'courseid' => $courseid,'moodleurl' => $moodleurl ));
    
    		//Context validation
    		//OPTIONAL but in most web service it should present
    		//             $context = get_context_instance(CONTEXT_USER, $USER->id);
    		//             self::validate_context($context);
    
    		//Capability checking
    		//OPTIONAL but in most web service it should present
    		// if (!has_capability('moodle/user:viewdetails', $context)) {
    		//     throw new moodle_exception('cannotviewprofile');
    		// }
    
    		$sql = "select ba.id id, ba.name name, ba.description description, bp.points points, (select count(*) from {badge_issued} bi_count where bi_count.badgeid = ba.id  ) earned_times  , 
					concat('/pluginfile.php/',contextid, '/badges/badgeimage/', ba.id, '/f1') pictureroute
					from {badge} ba,  {badge_points} bp, (select distinct contextid, itemid badgeid from {files} where filearea='badgeimage') context
					where  ba.courseid=$courseid and ba.id=bp.badgeid and ba.id not in
					(select bi.id id 
					from {badge} ba, {badge_issued} bi,{badge_points} bp
					where bp.badgeid=bi.badgeid and ba.id=bi.badgeid and bi.userid=:id) and context.badgeid=ba.id";
    		
    		
    		//$params = array('fieldname' => $catalogname);
    		$response = $DB->get_records_sql($sql, array('id' => $id ));
    		
    		$newresponse=array();
    		
    		foreach($response as $badge){
    			//var_dump("$moodleurl/$badge->pictureroute");
    			$file = file_get_contents("$moodleurl/$badge->pictureroute");
    			$file = base64_encode($file);
    			$badge->badgeimage=$file;
    			array_push($newresponse, $badge);
    		}
    		$response=$newresponse;
    
    	} catch (Exception $e) {
    		$response = $e;
    	}
    	return $response;
    }
    public static function get_posible_badges_to_earn_returns() {
    	return new external_multiple_structure(
    			new external_single_structure(
    					array(
    							'id' => new external_value(PARAM_TEXT, 'id of the badge'),
    							'badgeimage' => new external_value(PARAM_TEXT, 'picture of the badge'),
    							'name' => new external_value(PARAM_TEXT, 'Badge\'s name'),
    							'description' => new external_value(PARAM_TEXT, 'Badge\'s description'),
                				'earned_times' => new external_value(PARAM_TEXT, 'Times that this badge has been earned'),
                				'points' => new external_value(PARAM_TEXT, 'Points for getting a this badge'),
    					)
    			)
    	);
    }
    
    
//     if (process_manual_award($user->id, $USER->id, $issuerrole->roleid, $badgeid)) {
//     	// If badge was successfully awarded, review manual badge criteria.
//     	$data = new stdClass();
//     	$data->crit = $badge->criteria[BADGE_CRITERIA_TYPE_MANUAL];
//     	$data->userid = $user->id;
//     	badges_award_handle_manual_criteria_review($data);
//     } else {
//     	echo $OUTPUT->error_text(get_string('error:cannotawardbadge', 'badges'));
//     }
   
    public static function grant_a_badge_parameters() {
    	return new external_function_parameters(
    			array('userid' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false),
    					'badgeid' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false))
    	);
    }
    public static function grant_a_badge($userid, $badgeid) {
    	global $USER;
    	global $DB;
    	$response = array();
    
    		//Parameter validation
    		//REQUIRED
    		$params = self::validate_parameters(self::grant_a_badge_parameters(), array('userid' => $userid,'badgeid' => $badgeid ));
    
    		
    		$sql="select value
					from mdl_config
					where name='siteadmins'";
    		
    		$admins = $DB->get_record_sql($sql);
    		$admin=explode(",", $admins->value)[0];
    		$badge = new badge($badgeid);
    		    if (process_manual_award($userid, $USER->id, 1, $badgeid)) {
    		    	// If badge was successfully awarded, review manual badge criteria.
    		    	$data = new stdClass();
    		    	$data->crit = $badge->criteria[BADGE_CRITERIA_TYPE_MANUAL];
    		    	$data->userid = $userid;
    		    	badges_award_handle_manual_criteria_review($data);
    		    } else {
    		    	array_push($response, array('message'=>'badge cannot be awarded'));
    		    }
    
    		
    		return $response;
    	}
    
    	public static function grant_a_badge_returns() {
    	return new external_multiple_structure(
    	new external_single_structure(
    			array(
    					'message' => new external_value(PARAM_TEXT, 'id of the badge'),
    				)
    			)
    		);
    	}
}

