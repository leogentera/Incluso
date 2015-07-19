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

class progress_plugin extends external_api{
	
	public static function get_stage_progress_parameters(){
		return new external_function_parameters(
			array(
					'userid' => new external_value(PARAM_INT, 'Moodle\'s user id ', VALUE_REQUIRED, null, false),
					'stageid' => new external_value(PARAM_INT, 'Id of the stage ', VALUE_REQUIRED, null, false)
			)
		);
	}
	
	public static function get_stage_progress($userid, $stageid){
		global $USER;
		global $DB;
		$response = array();
		$sql="";
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_stage_progress_parameters(), 
					array(
						'userid' => $userid,
						'stageid' => $stageid
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
			
			
		
			$sql = "select count(*) total_activities, sum(activities.completed) activities_completed, truncate((sum(activities.completed)/count(*))*100, 0) percentage_completed 
					from {course_format_options} ger, 
					{course_sections} se,
					( select gerStage.courseid courseid, gerStage.sectionid stageid, seStage.name stage, seStage.section section
					from {course_format_options} gerStage, {course_sections} seStage 
					where  seStage.id=gerStage.sectionid 
					and gerStage.courseid=seStage.course 
					and gerStage.name = 'parent'
					and gerStage.value=0) stage,
					( select  compl.userid userid, IF(isnull(compl.timemodified)  or compl.completionstate=0, 0, 1) completed, mo.section sectionid
					from {course_modules} mo left join {course_modules_completion} compl on  mo.id=compl.coursemoduleid and (compl.userid=$userid or isnull(compl.userid)) ) activities
					where  se.id=ger.sectionid 
					and ger.courseid=se.course 
					and ger.name = 'parent'
					and ger.value<>0
					and ger.value=stage.section
					and ger.sectionid=activities.sectionid
					and stage.stageid=$stageid";
			
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_stage_progress_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'total_activities' => new external_value(PARAM_INT, 'Count of all the activities on the stage'),
					'activities_completed' => new external_value(PARAM_INT, 'Activities that the user had completed'),
					'percentage_completed' => new external_value(PARAM_TEXT, 'Percentage of progress of the user (completed/total activities)')
				)
			)
		);
	}
	
	
	public static function get_global_progress_parameters(){
		return new external_function_parameters(
				array(
						'userid' => new external_value(PARAM_INT, 'Moodle\'s user id ', VALUE_REQUIRED, null, false),
						//'courseid' => new external_value(PARAM_INT, 'Id of the course ', VALUE_OPTIONAL)
				)
		);
	}
	
	public static function get_global_progress($userid){
		global $USER;
		global $DB;
		$response = array();
	
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_global_progress_parameters(), 
					array(
						'userid' => $userid,
						//'courseid' => $courseid
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
	
			$sql = "select count(*) total_activities, sum(activities.completed) activities_completed, truncate((sum(activities.completed)/count(*))*100, 0) percentage_completed
			from {course_format_options} ger,
			{course_sections} se,
			( select gerStage.courseid courseid, gerStage.sectionid stageid, seStage.name stage, seStage.section section
			from {course_format_options} gerStage, {course_sections} seStage
			where  seStage.id=gerStage.sectionid
			and gerStage.courseid=seStage.course
			and gerStage.name = 'parent'
			and gerStage.value=0) stage,
			( select  compl.userid userid, IF(isnull(compl.timemodified)  or compl.completionstate=0, 0, 1) completed, mo.section sectionid
			from {course_modules} mo left join {course_modules_completion} compl on  mo.id=compl.coursemoduleid and (compl.userid=$userid or isnull(compl.userid)) ) activities
			where  se.id=ger.sectionid
			and ger.courseid=se.course
			and ger.name = 'parent'
			and ger.value<>0
			and ger.value=stage.section
			and ger.sectionid=activities.sectionid";
			//and stage.courseid=$courseid";
				
			$response = $DB->get_records_sql($sql);
	
		} catch (Exception $e) {
		$response = $e;
		}
			return $response;
	}
	
			public static function get_global_progress_returns(){
			return new external_multiple_structure(
					new external_single_structure(
							array(
							'total_activities' => new external_value(PARAM_INT, 'Count of all the activities on the stage'),
							'activities_completed' => new external_value(PARAM_INT, 'Activities that the user had completed'),
							'percentage_completed' => new external_value(PARAM_TEXT, 'Percentage of progress of the user (completed/total activities)')
				)
			)
		);
	}
	
	public static function get_finished_stages_parameters(){
		return new external_function_parameters(
				array(
						'userid' => new external_value(PARAM_INT, 'Moodle\'s user id ', VALUE_REQUIRED, null, false),
						//'courseid' => new external_value(PARAM_INT, 'Id of the course ', VALUE_OPTIONAL)
				)
		);
	}
	
	public static function get_finished_stages($userid){
		global $USER;
		global $DB;
		$response = array();
	
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_finished_stages_parameters(),
					array(
							'userid' => $userid,
							//'courseid' => $courseid
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
	
			$sql = "select stageid, stage from(
					select stage.stageid stageid, stage.stage stage, count(*) total_activities, sum(activities.completed) activities_completed, round((sum(activities.completed)/count(*))*100, 0) percentage_completed 
					from {course_format_options} ger, 
					{course_sections} se,
					( select gerStage.courseid courseid, gerStage.sectionid stageid, seStage.name stage, seStage.section section
					from {course_format_options} gerStage, {course_sections} seStage 
					where  seStage.id=gerStage.sectionid 
					and gerStage.courseid=seStage.course 
					and gerStage.name = 'parent'
					and gerStage.value=0) stage,
					( select  compl.userid userid, IF(isnull(compl.timemodified) or compl.completionstate=0, 0, 1) completed, mo.section sectionid
					from {course_modules} mo left join {course_modules_completion} compl on  mo.id=compl.coursemoduleid and (compl.userid=$userid or isnull(compl.userid)) ) activities
					where  se.id=ger.sectionid 
					and ger.courseid=se.course 
					and ger.name = 'parent'
					and ger.value<>0
					and ger.value=stage.section
					and ger.sectionid=activities.sectionid
					group by stage.stageid) stages
					where percentage_completed=100";
	
			$response = $DB->get_records_sql($sql);
	
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_finished_stages_returns(){
		return new external_multiple_structure(
				new external_single_structure(
						array(
								'stageid' => new external_value(PARAM_INT, 'Count of all the activities on the stage'),
								'stage' => new external_value(PARAM_TEXT, 'Activities that the user had completed'),
						)
				)
		);
	}
	
	public static function get_all_stages_parameters(){
		return new external_function_parameters(
				array(
						//'userid' => new external_value(PARAM_INT, 'Moodle\'s user id ', VALUE_REQUIRED, null, false),
						//'courseid' => new external_value(PARAM_INT, 'Id of the course ', VALUE_OPTIONAL)
				)
		);
	}
	
	public static function get_all_stages(){
		global $USER;
		global $DB;
		$response = array();
	
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_all_stages_parameters(),
					array(
							//'userid' => $userid,
							//'courseid' => $courseid
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
	
			$sql = "select gerStage.sectionid stageid, seStage.name stage
					from {course_format_options} gerStage, {course_sections} seStage 
					where  seStage.id=gerStage.sectionid 
					and gerStage.courseid=seStage.course 
					and gerStage.name = 'parent'
					and gerStage.value=0";
	
			$response = $DB->get_records_sql($sql);
			//var_dump($response);
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_all_stages_returns(){
		return new external_multiple_structure(
				new external_single_structure(
						array(
								'stageid' => new external_value(PARAM_INT, 'Count of all the activities on the stage'),
								'stage' => new external_value(PARAM_TEXT, 'Activities that the user had completed'),
						)
				)
		);
	}
	
}

