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


class stage_services extends external_api{
	
	public static function get_activities_by_challenge_parameters(){
		return new external_function_parameters(
			array(
				'sectionid' => new external_value(PARAM_INT, 'Section ID')
			)
		);	
	}

	public static function get_activities_by_challenge($sectionid){	
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_activities_by_challenge_parameters(), 
					array(
						'sectionid' => $sectionid,
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
		
			$sql = "SELECT cm.id AS coursemoduleid, cm.instance, m.name 
					FROM {course_modules} AS cm
					INNER JOIN {modules} AS m
					ON cm.module = m.id
					WHERE section = $sectionid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_activities_by_challenge_returns(){	
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'coursemoduleid' => new external_value(PARAM_INT, 'ID of table course_modules'),
					'instance' 		 => new external_value(PARAM_INT, 'Instance ID'),
					'name' 			 => new external_value(PARAM_TEXT, 'Name of instance')
				)
			)
		);
	}

	public static function get_activities_status_by_challenge_parameters(){
		return new external_function_parameters(
			array(
				'userid' 	 => new external_value(PARAM_INT, 'User ID'),
				'mainActivity' 	 => new external_value(PARAM_INT, 'Section ID'),
				'courseid' 	 => new external_value(PARAM_INT, 'Course ID')
				)
		);
	}

	public static function get_activities_status_by_challenge($userid, $mainActivity, $courseid){
		global $USER;
		global $DB;
		$response = array();

		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_activities_status_by_challenge_parameters(), 
					array(
						'userid' => $userid,
						'mainActivity' => $mainActivity,
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
		
			$sql = "SELECT cm.id AS coursemoduleid,
						   IFNULL(cmc.completionstate,0) AS completionstate,
						   instance, 
						   name, 
						   timemodified
					FROM (
						SELECT 	coursemoduleid, 
								userid, 
								completionstate, 
								timemodified 
						FROM {course_modules_completion} 
						WHERE userid = $userid) cmc
					RIGHT JOIN (
						SELECT 	cm.id, 
								cm.course, 
								cm.module, 
								cm.instance, 
								cm.section, 
								m.name
          				FROM {course_modules} AS cm
          				INNER JOIN {modules} AS m
						ON cm.module = m.id
           				WHERE cm.course = $courseid
          				AND cm.section = $mainActivity) cm
					ON cm.id = cmc.coursemoduleid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_activities_status_by_challenge_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'coursemoduleid'    => new external_value(PARAM_INT, 'ID of table course_modules'),
					'instance' 			=> new external_value(PARAM_INT, 'Instance ID'),
					'name' 				=> new external_value(PARAM_TEXT, 'Type of Activity'),
					'timemodified' 		=> new external_value(PARAM_RAW, 'Time Modified of Activity'),
					'completionstate'	=> new external_value(PARAM_INT, 'State of Activity.')
				)
			)
		);
	}

	public static function get_challenges_stage_parameters(){
		return new external_function_parameters(
			array(
					'courseid' => new external_value(PARAM_INT, 'Course ID'),
					'stageid'  => new external_value(PARAM_INT, 'Stage ID of course')
			)
		);
	}
	
	public static function get_challenges_stage($courseid, $stageid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_challenges_stage_parameters(), 
					array(
						'courseid' => $courseid,
						'stageid' => $stageid
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
		
			$sql = "SELECT cfo.sectionid, cs.name, cs.summary
					FROM {course_format_options} AS cfo
					INNER JOIN {course_sections} AS cs
					ON cfo.sectionid = cs.id
					WHERE cfo.courseid = $courseid
					AND cfo.name = 'parent'
					AND cfo.value = $stageid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_challenges_stage_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'sectionid' => new external_value(PARAM_INT, 'Principal Activities of the stage'),
					'name' => new external_value(PARAM_TEXT, 'Name of the principal activity'),
					'summary' => new external_value(PARAM_RAW, 'Challenge of the principal activity')
				)
			)
		);
	}

	public static function get_stages_by_course_parameters(){
		return new external_function_parameters(
			array(
					'courseid' => new external_value(PARAM_INT, 'Course ID')
			)
		);
	}

	public static function get_stages_by_course($courseid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_stages_by_course_parameters(), 
					array(
						'courseid' => $courseid,
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
		
			$sql = "SELECT cfo.sectionid AS stageid,
					       cs.section AS section, 
					       cs.name AS stage
					FROM {course_format_options} AS cfo

					INNER JOIN {course_sections} AS cs 
					ON (cs.id = cfo.sectionid AND cfo.courseid = cs.course)

					WHERE cfo.courseid = $courseid
					AND cfo.name = 'parent'
					AND cfo.value = 0
					AND cs.name <> ''";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_stages_by_course_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'stageid' 	=> new external_value(PARAM_INT, 'Stage ID'),
					'section'	=> new external_value(PARAM_INT, 'Section ID'),
					'stage' 	=> new external_value(PARAM_TEXT, 'Name of Stage')
				)
			)
		);		
	}

	public static function get_stages_by_user_and_course_parameters(){
		return new external_function_parameters(
			array(
				'courseid' => new external_value(PARAM_INT, 'Course ID'),
				'userid'   => new external_value(PARAM_INT, 'User ID')
			)
		);
	}

	public static function get_stages_by_user_and_course($courseid, $userid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_stages_by_user_and_course_parameters(), 
					array(
						'courseid' => $courseid,
						'userid'   => $userid
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
		
			$sql = "SELECT cfo.sectionid AS stageid,
					       cs.section AS section, 
					       cs.name AS stage,
					       IFNULL(urv.firsttime,1) AS firsttime
					FROM {course_format_options} AS cfo

					INNER JOIN {course_sections} AS cs 
					ON (cs.id = cfo.sectionid AND cfo.courseid = cs.course)

					LEFT JOIN (
						SELECT * FROM {user_resource_visited}
						WHERE userid = $userid
                        AND typeresource = 'stage') AS urv
					ON urv.resourceid = cfo.sectionid

					WHERE cfo.courseid = $courseid
					AND cfo.name = 'parent'
					AND cfo.value = 0
					AND cs.name <> ''";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_stages_by_user_and_course_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'stageid' 	=> new external_value(PARAM_INT,  'Stage ID'),
					'section'	=> new external_value(PARAM_INT,  'Section ID'),
					'stage' 	=> new external_value(PARAM_TEXT, 'Name of Stage'),
					'firsttime' => new external_value(PARAM_INT,  'Indicates if is the first time in stage')
				)
			)
		);		
	}

}