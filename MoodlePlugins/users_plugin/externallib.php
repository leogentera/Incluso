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
require_once($CFG->dirroot . '/lib/moodlelib.php');

class user_services extends external_api {   



    public static function generate_user_parameters() {
        return new external_function_parameters(
        		array()
        );
    }
    public static function generate_user($id, $moodleurl) {
        global $USER;
        global $DB;
        $response = array();

        try {

            $sql = 'select concat("fb", max(id)+1)username from mdl_user';
            
            //$params = array('fieldname' => $catalogname);
            $response = $DB->get_record_sql($sql);
        } catch (Exception $e) {
            $response = $e;
        }
        return $response;
    }
    public static function generate_user_returns() {
        return 
            new external_function_parameters(
                array(
    							'username' => new external_value(PARAM_TEXT, 'Generated user'),
    					)
            
        );
    }
    
    public static function get_user_by_facebookid_parameters() {
    	return new external_function_parameters(
    			array('facebookid' => new external_value(PARAM_TEXT, 'Facebook id'),)
    	);
    }
    public static function get_user_by_facebookid($facebookid) {
    	global $USER;
    	global $DB;
    	$response = array();
    	
    	
    	try {
    		
    		$params = self::validate_parameters(
    				self::get_user_by_facebookid_parameters(),
    				array(
    						'facebookid' => $facebookid,
    				));
    
    		$sql = "select username
					from mdl_user_info_data data,
					mdl_user users,
					mdl_user_info_field fields
					where data.userid=users.id
					and fields.id=data.fieldid
					and fields.shortname='facebookid'
					and data.data=$facebookid 
    				and data.data<>''";
    
    		//$params = array('fieldname' => $catalogname);
    		$response = $DB->get_record_sql($sql);
    	} catch (Exception $e) {
    		$response = $e;
    	}
    	return $response;
    }
    public static function get_user_by_facebookid_returns() {
    	return
    	new external_function_parameters(
    			array(
    					'username' => new external_value(PARAM_TEXT, 'User registered by facebook'),
    			)
    
    	);
    }
    


public static function set_token_valid_time_parameters(){
    return new external_function_parameters(
            array(
                'userid'  => new external_value(PARAM_INT, 'User ID'),
                'token'   => new external_value(PARAM_TEXT, 'Token')
            )
        );
}

public static function set_token_valid_time($userid, $token){
    global $USER;
    global $DB;
    
    try {

        $params = self::validate_parameters(
                self::set_token_valid_time_parameters(),
                    array(
                        'userid' => $userid,
                        'token' => $token
                    )
                );

        $sql = "UPDATE {external_tokens}
                SET validuntil = " ;

        $sql.= time();

        $sql.= " WHERE token ='$token' AND userid = $userid";

        $response = $DB->execute($sql);

        $result = new stdClass();
        $result->result = $response;
        
    } catch (Exception $e) {
        $result = $e;
    }

    return $result;
}

public static function set_token_valid_time_returns(){
    return new external_single_structure(
        array(
            'result' => new external_value(PARAM_BOOL, 'Boolean result of the update action.')
        )
            
    );
}

    
   
}

