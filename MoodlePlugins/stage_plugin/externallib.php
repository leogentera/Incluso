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
	
	public static function get_activities_status_by_stage_parameters(){
		return new external_function_parameters(
			array(
				'userid' 	 => new external_value(PARAM_INT, 'User ID'),
				'mainActivities' => new external_multiple_structure(
					new external_value(PARAM_INT, 'Principal Activity ID'),
					"List of Principal Activities of the Stage"
					)
				)
		);
	}

	public static function get_activities_status_by_stage($userid, $mainActivities){
		global $USER;
		global $DB;
		$response = array();

		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_activities_status_by_stage_parameters(), 
					array(
						'userid' => $userid,
						'mainActivities' => $mainActivities
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
		
			$sql = "SELECT cm.section, cs.name, MIN(IFNULL(cmc.completionstate,0)) AS completionstate 
					FROM {course_modules} AS cm 
					INNER JOIN {course_sections} AS cs 
					ON cs.id = cm.section 
					LEFT JOIN ( 
						SELECT coursemoduleid, userid, completionstate 
						FROM {course_modules_completion} 
						WHERE userid=$userid) AS cmc 
					ON cm.id = cmc.coursemoduleid 
					WHERE cm.section IN (". 
					join(",", $mainActivities) 
					.") GROUP BY cm.section";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_activities_status_by_stage_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'section' 			=> new external_value(PARAM_INT, 'Stage ID'),
					'name' 				=> new external_value(PARAM_TEXT, 'Name of Principal Activity'),
					'completionstate'	=> new external_value(PARAM_INT, 'State of the Principal Activity.')
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
					FROM {course_format_options} cfo, 
					     {course_sections} cs 
					WHERE  cs.id = cfo.sectionid 
					AND cfo.courseid = cs.course
					AND cfo.courseid = $courseid
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
		
			$sql = "SELECT cm.id, cm.instance, m.name 
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
					'instance' 	=> new external_value(PARAM_INT, 'Instance ID'),
					'name' 	=> new external_value(PARAM_TEXT, 'Name of instance')
				)
			)
		);
	}
}