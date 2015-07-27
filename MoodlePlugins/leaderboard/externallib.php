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

class leaderboard_services extends external_api{
	
	public static function get_leaderboard_parameters(){
		return new external_function_parameters(
			array(
					'amount' => new external_value(PARAM_INT, 'Quantity of top leaders')
			)
		);
	}
	
	public static function get_leaderboard($n_top){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_leaderboard_parameters(), 
					array(
						'amount' => $n_top
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
		
// 			$sql = "SELECT @r := @r+1 AS place, 
// 					z.* FROM( 
// 						SELECT u.id id, CONCAT(u.firstname, ' ',u.lastname) AS name, 
// 						IFNULL(result.stars, 0) AS stars 
// 						FROM {user} as u 
// 						LEFT JOIN ( 
// 							SELECT u.id as id, 
// 							(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS stars 
// 							FROM {user_info_data} AS uid 
// 							LEFT JOIN {user_info_field} AS uif 
// 							ON uid.fieldid = uif.id 
// 							RIGHT JOIN {user} AS u 
// 							ON uid.userid = u.id 
// 							WHERE uif.shortname = 'stars') AS result 
// 						ON u.id = result.id 
// 						ORDER BY CAST(stars AS UNSIGNED) DESC, name ASC 
// 						LIMIT $n_top)z, 
// 					(SELECT @r:=0)y";

			//HCG Added progress percentage to the users on the leaderboard
			$sql = "SELECT @r := @r+1 AS place, 
					(select round((sum(completed)/count(*))*100, 0) percentage_completed  from (select stage.courseid courseid, activities.completed,  activities.userid, stage.stageid stageid, stage.stage stage
			            from {course_format_options} ger, 
			            {course_sections} se,
					            ( select  gerStage.courseid courseid, gerStage.sectionid stageid, seStage.name stage, seStage.section section
					            from {course_format_options} gerStage, {course_sections} seStage 
					            where  seStage.id=gerStage.sectionid 
					            and gerStage.courseid=seStage.course 
					            and gerStage.name = 'parent'
					            and gerStage.value=0) stage,
					            ( select  mo.course courseid, compl.userid userid, IF(isnull(compl.timemodified) or compl.completionstate=0, 0, 1) completed, mo.section sectionid
					            from {course_modules mo left join {course_modules_completion compl on  mo.id=compl.coursemoduleid ) activities
					            where  se.id=ger.sectionid 
					            and ger.courseid=se.course 
					            and ger.name = 'parent'
					            and ger.value<>0
                      and activities.courseid=stage.courseid
					            and ger.value=stage.section
					            and ger.sectionid=activities.sectionid) progress
			            where (progress.userid=z.id  or isnull(progress.userid)) and progress.courseid=z.courseid
            ) percentage_completed,
					z.* FROM(SELECT u.id id, CONCAT(u.firstname, ' ',u.lastname) AS name, 
						IFNULL(result.stars, 0) AS stars , course.courseid 
						FROM {user as u 
						LEFT JOIN ( 
							SELECT u.id as id, 
							(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS stars 
							FROM {user_info_data AS uid 
							LEFT JOIN {user_info_field AS uif 
							ON uid.fieldid = uif.id 
							RIGHT JOIN {user AS u 
							ON uid.userid = u.id 
							WHERE uif.shortname = 'stars') AS result 
						ON u.id = result.id 
             LEFT JOIN ( 
							SELECT u.id as id, 
							(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS courseid 
							FROM {user_info_data AS uid 
							LEFT JOIN {user_info_field AS uif 
							ON uid.fieldid = uif.id 
							RIGHT JOIN {user AS u 
							ON uid.userid = u.id 
							WHERE uif.shortname = 'course') AS course 
              ON u.id = course.id
              where courseid=$courseid
						ORDER BY CAST(stars AS UNSIGNED) DESC, name ASC 
						LIMIT $n_top)z,
            (SELECT @r:=0)y";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_leaderboard_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'id' => new external_value(PARAM_INT, 'Id of the user'),
					'place' => new external_value(PARAM_INT, 'Ranking'),
					'name' => new external_value(PARAM_TEXT, 'Full name of user'),
					'stars' => new external_value(PARAM_INT, 'Quantity of stars'),
					'percentage_completed' => new external_value(PARAM_INT, 'Percentage of completed activities')
				)
			)
		);
	}
	
	public static function get_user_rank_parameters(){
		return new external_function_parameters(
			array(
					'userid' => new external_value(PARAM_INT, 'User ID')
			)
		);
	}
	
	public static function get_user_rank($id_user){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
				self::get_user_rank_parameters(),
				array(
						'userid' => $id_user
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
		
// 				$sql = "SELECT place
// 						FROM (
// 							SELECT @r := @r+1 AS place, 
// 							z.* FROM( 
// 								SELECT u.id, 
// 								CONCAT(u.firstname, ' ',u.lastname) AS name, 
// 								IFNULL(result.stars, 0) as stars 
// 								FROM {user} as u 
// 								LEFT JOIN ( 
// 									SELECT u.id as id, 
// 									(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS stars 
// 									FROM {user_info_data} AS uid 
// 									LEFT JOIN {user_info_field} AS uif 
// 									ON uid.fieldid = uif.id 
// 									RIGHT JOIN {user} AS u 
// 									ON uid.userid = u.id 
// 									WHERE uif.shortname = 'stars') AS result 
// 								ON u.id = result.id 
// 								ORDER BY CAST(stars AS UNSIGNED) DESC, name ASC)z, 
// 							(SELECT @r:=0)y) AS ranking 
// 						WHERE id =$id_user";

			$sql = "SELECT place
					FROM (
						SELECT @r := @r+1 AS place, 
						z.* FROM( 
							SELECT u.id, 
							CONCAT(u.firstname, ' ',u.lastname) AS name, 
							IFNULL(result.stars, 0) as stars ,  course.courseid
							FROM {user} as u 
							LEFT JOIN ( 
								SELECT u.id as id, 
								(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS stars 
								FROM {user_info_data} AS uid 
								LEFT JOIN {user_info_field} AS uif 
								ON uid.fieldid = uif.id 
								RIGHT JOIN {user} AS u 
								ON uid.userid = u.id 
								WHERE uif.shortname = 'stars') AS result 
							ON u.id = result.id 
              LEFT JOIN ( 
							SELECT u.id as id, 
							(CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS courseid 
							FROM {user_info_data} AS uid 
							LEFT JOIN {user_info_field} AS uif 
							ON uid.fieldid = uif.id 
							RIGHT JOIN {user} AS u 
							ON uid.userid = u.id 
							WHERE uif.shortname = 'course') AS course 
              ON u.id = course.id
              where courseid=( 
							SELECT (CASE WHEN uid.data = '' THEN '0' ELSE uid.data END) AS courseid 
							FROM {user_info_data} AS uid 
							LEFT JOIN {user_info_field} AS uif 
							ON uid.fieldid = uif.id 
							RIGHT JOIN {user} AS u 
							ON uid.userid = u.id 
							WHERE uif.shortname = 'course'
              and userid =$userid)
							ORDER BY CAST(stars AS UNSIGNED) DESC, name ASC)z, 
						(SELECT @r:=0)y) AS ranking 
					WHERE id =$userid";
				
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_user_rank_returns(){
			return new external_multiple_structure(
			new external_single_structure(
				array(
					'place' => new external_value(PARAM_INT, 'Ranking'),
					
				)
			)
		);
	}
}

