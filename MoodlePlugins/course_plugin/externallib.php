<?php
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
		return new external_function_parameters(
				array(
						'id' => new external_value(PARAM_INT, 'Id of the latest course'),
				)
		);
	}
	

	public static function get_current_course_and_status_by_user_parameters(){
		return new external_function_parameters(
			array(
				'userid'   => new external_value(PARAM_INT, 'User ID')
			)
		);
	}

	public static function get_current_course_and_status_by_user($userid){
		global $CFG;
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_current_course_and_status_by_user_parameters(), 
					array(
						'userid' => $userid
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
			$context = get_context_instance(CONTEXT_USER, $USER->id);
			self::validate_context($context);

			require_once($CFG->dirroot . "/user/profile/lib.php");
            $user = profile_user_record($userid);
            $courseid = $user->course;
		
			$sql = "SELECT firsttime
					FROM {user_resource_visited}
					WHERE resourceid = $courseid
					AND typeresource = 'course'
					AND userid = $userid";

			$response = $DB->get_record_sql($sql);

			$result = new stdclass();
			$result->courseid = $courseid;
			$result->firsttime = (!empty($response->firsttime)) ? $response->firsttime : 1;

			$sql = "CALL all_activities($courseid, $userid)";
					
			$response = $DB->get_records_sql($sql);

			$total_activities = count($response);
			$completed_activities = 0;

			foreach($response as $row){
				if($row->completionstate == "1"){
					$completed_activities = $completed_activities + 1;
				}
			}

			if($total_activities == 0){
				$result->percentage_completed = 0;	
			}else{
				$result->percentage_completed = intval($completed_activities*100/$total_activities);
			}

		} catch (Exception $e) {
			$result = $e;
		}

		return $result;
	}

	public static function get_current_course_and_status_by_user_returns(){
		return new external_single_structure(
			array(
				'courseid'	=> new external_value(PARAM_INT, 'Course ID'),
				'firsttime' => new external_value(PARAM_INT, 'Value if is the first time in course'),
				'percentage_completed' => new external_value(PARAM_TEXT, 'Percentage of progress of the user (completed/total activities)')
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
	
	public static function get_user_course_info_parameters(){
		return new external_function_parameters(
			array(
				'userid' => new external_value(PARAM_INT, 'User ID')
			)
		);
	}

	public static function get_user_course_info($userid){
		global $CFG;
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_user_course_info_parameters(), 
					array(
						'userid' => $userid
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
			$context = get_context_instance(CONTEXT_USER, $USER->id);
			self::validate_context($context);

			require_once($CFG->dirroot . "/user/profile/lib.php");
            $user = profile_user_record($userid);
            $courseid = $user->course;

			$sql = "SELECT 
					    c.coursemoduleid,
					    a.stageid,
					    a.section AS stagesection,
					    a.stage,
					    a.firsttime,
					    b.sectionid AS challengeid,
					    b.name AS challenge,
					    b.summary AS challenge_description,
					    c.completionstate,
					    c.instance AS activityid,
					    c.name AS activitytype,
					    c.timemodified
					FROM
					(SELECT cfo.sectionid AS stageid,
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
					AND cs.name <> '') a
					INNER JOIN 
						(SELECT cfo.sectionid, 
						        cs.name, 
						        cs.summary, 
						        cfo.value 
						 FROM {course_format_options} AS cfo 
						 INNER JOIN {course_sections} AS cs 
						 ON cfo.sectionid = cs.id 
						 WHERE cfo.courseid = $courseid 
						 AND cfo.name = 'parent') b
					ON a.section = b.value
					INNER JOIN(
						SELECT cm.id AS coursemoduleid, 
					           cm.section, 
					           IFNULL(cmc.completionstate,0) AS completionstate, 
					           instance, 
					           name, 
					           timemodified 
					    FROM ( 
				            SELECT coursemoduleid, 
				                   userid, 
				                   completionstate, 
				                   timemodified 
				            FROM {course_modules_completion} 
				            WHERE userid = $userid) cmc 
					 	RIGHT JOIN ( 
					     	SELECT cm.id, 
					               cm.course, 
					               cm.module, 
					               cm.instance, 
					               cm.section, 
					               m.name 
						    FROM {course_modules} AS cm 
						    INNER JOIN {modules} AS m 
					    	ON cm.module = m.id WHERE cm.course = $courseid) cm 
					 	ON cm.id = cmc.coursemoduleid) c
					ON b.sectionid = c.section
					ORDER BY a.stageid, b.sectionid";

			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}

		return $response;
	}

	public static function get_user_course_info_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'stageid' => new external_value(PARAM_INT, 'Stage ID'),
					'stagesection' => new external_value(PARAM_INT, 'Stage Section ID'),
					'stage' => new external_value(PARAM_TEXT, 'Stage Name'),
					'firsttime' => new external_value(PARAM_INT, 'Flag First Time in Stage'),
					'challengeid' => new external_value(PARAM_INT, 'Challenge ID'),
					'challenge' => new external_value(PARAM_TEXT, 'Challenge Name'),
					'challenge_description' => new external_value(PARAM_RAW, 'Challenge Description'),
					'coursemoduleid' => new external_value(PARAM_INT, 'Course_Module ID Activity'),
					'completionstate' => new external_value(PARAM_INT, 'Activity Completion State'),
					'activityid' => new external_value(PARAM_INT, 'Activity ID'),
					'activitytype' => new external_value(PARAM_TEXT, 'Activity Type'),
					'timemodified' => new external_value(PARAM_RAW, 'Activity Time Modified')
				)
			)
		);
	}


}

