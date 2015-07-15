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
		
			$sql = "SELECT @r := @r+1 AS place, 
					z.* FROM( 
						SELECT CONCAT(u.firstname, ' ',u.lastname) AS name, 
						IFNULL(result.stars, 0) AS stars 
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
					'place' => new external_value(PARAM_INT, 'Ranking'),
					'name' => new external_value(PARAM_TEXT, 'Full name of user'),
					'stars' => new external_value(PARAM_INT, 'Quantity of stars')
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
		
			$sql = "SELECT place 
					FROM (
						SELECT @r := @r+1 AS place, 
						z.* FROM( 
							SELECT u.id, 
							CONCAT(u.firstname, ' ',u.lastname) AS name, 
							IFNULL(result.stars, 0) as stars 
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
							ORDER BY CAST(stars AS UNSIGNED) DESC, name ASC)z, 
						(SELECT @r:=0)y) AS ranking 
					WHERE id =$id_user";
				
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

