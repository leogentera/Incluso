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

class course_plugin extends external_api{
	
	public static function get_latest_course_parameters(){
		return new external_function_parameters(
			array(
			)
		);
	}
	
	public static function get_latest_course(){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_latest_course_parameters(), 
					array(
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
// 			$context = get_context_instance(CONTEXT_USER, $USER->id);
// 			self::validate_context($context);
		
			//Capability checking
			//OPTIONAL but in most web service it should present
			// if (!has_capability('moodle/user:viewdetails', $context)) {
			//     throw new moodle_exception('cannotviewprofile');
			// }
		


			//HCG Added progress percentage to the users on the leaderboard
			$sql = "select ifnull(max(course.id), -1) id 
					from {course} course,
					{course_type} courseType
					where course.id = courseType.courseid
					and courseType.coursetype='Incluso'";
								
			$response = $DB->get_record_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_latest_course_returns(){
// 		return new external_multiple_structure(
// 			new external_single_structure(
// 				array(
// 					'id' => new external_value(PARAM_INT, 'Id of the user'),
// 					'place' => new external_value(PARAM_INT, 'Ranking'),
// 					'name' => new external_value(PARAM_TEXT, 'Full name of user'),
// 					'stars' => new external_value(PARAM_INT, 'Quantity of stars'),
// 					'percentage_completed' => new external_value(PARAM_INT, 'Percentage of completed activities')
// 				)
// 			)
// 		);
		return new external_function_parameters(
				array(
						'id' => new external_value(PARAM_INT, 'Id of the latest course'),
				)
		);
	}
	

	public static function is_first_time_in_course_parameters(){
		return new external_function_parameters(
			array(
				'userid'   => new external_value(PARAM_INT, 'User ID'),
				'courseid' => new external_value(PARAM_INT, 'Course ID')
			)
		);
	}

	public static function is_first_time_in_course($userid, $courseid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::is_first_time_in_course_parameters(), 
					array(
						'userid' => $userid,
						'courseid' => $courseid
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
			$context = get_context_instance(CONTEXT_USER, $USER->id);
			self::validate_context($context);
		
			//Capability checking
			//OPTIONAL but in most web service it should present
			// if (!has_capability('moodle/user:viewdetails', $context)) {
			//     throw new moodle_exception('cannotviewprofile');
			// }
		
			$sql = "SELECT firsttime
					FROM {user_resource_visited}
					WHERE resourceid = $courseid
					AND typeresource = 'course'
					AND userid = $userid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		
		return $response;
	}

	public static function is_first_time_in_course_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'firsttime' => new external_value(PARAM_INT, 'Value if is the first time in course'),
				)
			)
		);
	}

	public static function update_first_time_in_resource_parameters(){
		return new external_function_parameters(
			array(
				'userid'         => new external_value(PARAM_INT, 'User ID'),
				'resourceid'     => new external_value(PARAM_INT, 'Resource ID'),
				'typeofresource' => new external_value(PARAM_TEXT, 'Type of Resource')
			)
		);
	}

	public static function update_first_time_in_resource($userid, $resourceid, $typeofresource){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::update_first_time_in_resource_parameters(), 
					array(
						'userid'         => $userid,
						'resourceid'     => $resourceid,
						'typeofresource' => $typeofresource
					));
		
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
			$record->resourceid 	= $resourceid;
			$record->typeofresource = $typeofresource;
			$record->userid 		= $userid;
			$record->firsttime 		= 0;
			
			$response = array();

			$response["id"] = $DB->insert_record("user_resource_visited", $record, false);

		} catch (Exception $e) {
			$response = $e;
		}
		
		return $response;
	}

	public static function update_first_time_in_resource_returns(){
		return new external_single_structure(
			array(
				'id' => new external_value(PARAM_INT, 'Id of the latest insert'),
			)
		);
	}
	
}

