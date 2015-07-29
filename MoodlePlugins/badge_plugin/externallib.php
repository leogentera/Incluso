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

class badge_services extends external_api {   



    public static function get_earned_badges_parameters() {
        return new external_function_parameters(
                array('id' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false))
        );
    }
    public static function get_earned_badges($id) {
        global $USER;
        global $DB;
        $response = array();

        try {
            //Parameter validation
            //REQUIRED
            $params = self::validate_parameters(self::get_earned_badges_parameters(), array('id' => $id));

            //Context validation
            //OPTIONAL but in most web service it should present
//             $context = get_context_instance(CONTEXT_USER, $USER->id);
//             self::validate_context($context);

            //Capability checking
            //OPTIONAL but in most web service it should present
            // if (!has_capability('moodle/user:viewdetails', $context)) {
            //     throw new moodle_exception('cannotviewprofile');
            // }

            $sql = 'select bi.id id, bi.badgeid badgeid, ba.name name, ba.description description, bp.points points, (select count(*) from {badge_issued} bi_count where bi_count.badgeid = bi.badgeid  ) earned_times, bi.dateissued dateissued  '.
    				'from {badge} ba, {badge_issued} bi, {badge_points} bp '.
    				'where bp.badgeid=bi.badgeid and ba.id=bi.badgeid and bi.userid=:id';
            //$params = array('fieldname' => $catalogname);
            $response = $DB->get_records_sql($sql, array('id' => $id ));
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
    			array('id' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false)),
    			array('courseid' => new external_value(PARAM_TEXT, 'Moodle User ID from whom you want to know the earned badges', VALUE_REQUIRED, null, false))
    	);
    }
    public static function get_posible_badges_to_earn($id, $courseid) {
    	global $USER;
    	global $DB;
    	$response = array();
    
    	try {
    		//Parameter validation
    		//REQUIRED
    		$params = self::validate_parameters(self::get_posible_badges_to_earn_parameters(), array('id' => $id,'courseid' => $courseid ));
    
    		//Context validation
    		//OPTIONAL but in most web service it should present
    		//             $context = get_context_instance(CONTEXT_USER, $USER->id);
    		//             self::validate_context($context);
    
    		//Capability checking
    		//OPTIONAL but in most web service it should present
    		// if (!has_capability('moodle/user:viewdetails', $context)) {
    		//     throw new moodle_exception('cannotviewprofile');
    		// }
    
    		$sql = "select ba.id id, ba.name name, ba.description description, bp.points points, (select count(*) from {badge_issued} bi_count where bi_count.badgeid = ba.id  ) earned_times  
					from {badge} ba,  {badge_points} bp 
					where  ba.courseid=$courseid and ba.id=bp.badgeid and ba.id not in
					(select bi.id id 
					from {badge} ba, {badge_issued} bi,{badge_points} bp
					where bp.badgeid=bi.badgeid and ba.id=bi.badgeid and bi.userid=:id)";
    		
    		
            
    		//$params = array('fieldname' => $catalogname);
    		$response = $DB->get_records_sql($sql, array('id' => $id ));
    
    	} catch (Exception $e) {
    		$response = $e;
    	}
    	return $response;
    }
    public static function get_posible_badges_to_earn_returns() {
    	return new external_multiple_structure(
    			new external_single_structure(
    					array(
    							'id' => new external_value(PARAM_TEXT, 'id of the earned badge'),
    							'name' => new external_value(PARAM_TEXT, 'Badge\'s name'),
    							'description' => new external_value(PARAM_TEXT, 'Badge\'s description'),
                				'earned_times' => new external_value(PARAM_TEXT, 'Times that this badge has been earned'),
                				'points' => new external_value(PARAM_TEXT, 'Points for getting a this badge'),
    					)
    			)
    	);
    }
    
   
}

