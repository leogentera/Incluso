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
            
//             $newresponse=array();
//             foreach($response as $badge){
//             	$file = file_get_contents("$moodleurl/$badge->pictureroute");
//             	$file = base64_encode($file);
//             	$badge->badgeimage=$file;
//             	array_push($newresponse, $badge);
//             }
           
//             $response=$newresponse;
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
                				//'badgeimage' => new external_value(PARAM_TEXT, 'picture of the badge'),
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
    		
//     		foreach($response as $badge){
//     			//var_dump("$moodleurl/$badge->pictureroute");
// //     			$file = file_get_contents("$moodleurl/$badge->pictureroute");
// //     			$file = base64_encode($file);
//     			$badge->badgeimage="iVBORw0KGgoAAAANSUhEUgAAAG8AAABvCAYAAADixZ5gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAACfZJREFUeNrsXVtTE0kUbgYCZEICMSaCFK5hXdCCB/fBl331d/u6L7tlWaVborsbFIggCEhuXEKSnYNn1jFMZrp7+jaT+aq6QsJc++tz7dsESS4KTsk4peuURhJfcCKB71Ryyjp+ujh2yjv8TMkzELZT1pyyEnDMrlPeO6WTkmcOQNKqqCbDAGp0GyUxJU8jFp2ygVLHCpC+v5xykJKn3hnZHLJrvAA7+CaOTk3cyKOxa7yInT2c0CQ1S4znTDnlLp4rGw1OVbqvWnpVkwf2aTXBsWUN7agSTCp8MVB1T0iyUXTKuSoJtBS9VAalbhywQRmyxIa8VVUvZAAyqkzDlEIPUda1Z/ATTEBuyCTY6D32PL+38Tv8finJu1xD71Wq56rCYflNUDzmklVErzMv8BmbaKdOBVY4xI+/x1nySgKIK2CYUJToYOWxLKNUAolfIjoe7rsfx1XynhO+1BWQVHbKPVSLugBq9bNTjoZULy1Ail/EMVRYxZbM+jz3nfIIJW2K6AXcfx4bkYVkDBidl2uU5NiQBw/9jOH6XtLmFXrBLF55gZNEaIQfndKPC3lP0E7Rvtwv+GkaaaNIBFt25ZQLyjq2UPUaTx5002xS3vsRqtYpEi9MIYFgz88opLCIzk/LZPKAuKcU1y2idNok3siiKr2gkMIKktcyjbwMkrFJcU2QtIcxUJEsqrTkiReD6noZ6+qrCBsoIlRYxYxChqKhrKLUJRXgVdYowgoYivEej9VCXglVpE0p4UlQk7Sx3VvKuBCOfcUbyPOQZyNptJmTcSKOh0CC5L0ijKk5FpuXQZf+GQMR40icW1cLSMqAUiBWUZgatPaQVvKgI5W1n2pciYsiga49hN74XRGSt4EksHqmj4iaMSemSyCEEyeMjX4Rzz2KQh5U/q8cD72McU2Kb+SFhRGjYmEYCHUZFKMEYYnjYYuEPSGddCxzhkhLYQGmSLixXAr/eFhoRssy/QETBOENWyR5RZLs7IlxdWQJbFUPUm6o8ECUdhLVFQOu7YzuWlmYn5/Ozc1lsrOztyrn/OKi1261ul/Pzq40P+YM1lfdBPLcuEQbSnfuzFQqFTuTyYzUJLZtZ5zjZpe63f7h4WHn+OTkUuMjL2IY0Ita8UGA3vCwHCYMX5jX4gFMTk78vLpaKJVKWfib9pxCoTCdz+czZ43G1WAw0PHo0MgGFLHfMQlIWltxlTqXOJAonvPhPDiflnRJ0jcZtQVEQVlXaFB9+DA/OzsbSe3D+XAdjaFDWSd593TZOF6JG2ELdTlb93SRV9DlYYJzEnZMp9PpukXE9SR6ntzJ+yhq566Ot7Wz2alRXmWv3x8c7O+3IRzo9XqDYWldXFrKTVrWLRsH14Prds7PrzW8EtRjg4efMMlbDMkWKMe8E8uNIm67VmtACDBM3I3b5vy+tbV12nVCBZbrKsq6cAlIEHl2gEjbuhyVXC7n2xp3Pn5shkkOkLq3t9diua4ix8UOME02D3lrpkndKFxcXFw3W60uzbFwHBxPzEKRhwcrgPGVEGdFC/y8zGaz2WW5ht/xorzXCM7fKKyM+v8o8sKGq+dNarYtSqnjPV4Bwupzk5Y8cFJKIbYwhQSlEhTa+jmPfuSFrdowk9aztJgvCBth5K1TSJZyyYP8Y6VcnpWdyoLrw3005Ttp6n19VBAI/6xSurZKg/IqJJB9gmvhhiefh96G6XKlYkPMqDhop6lX4Of/VSa8FfKU0C3I9kSVwwIS8Pjx46IK4vyCfgjq/QJ+SYDuobcUxwF5r7xqs0TkrKQXCY4Ky+og7qbhOPeF+xtoG1dch9Ly2DrjoDHrYcT9A7DuklcgbGulpEP79Nk8b+hQAPJYMwtpnKfH2xzGTdcKa7ahk9azFLDWaxfIg74klpmZvbSe5Ti4DMcCXw3XYXmX1l2s8M4bpB9j/LCShDdrNptX9U+f2ldXV/3p6Wlr+f79HATfCSFu19WU3vTYe0r71zbacHQ63e0PH5pAHHyHT/hOM5ZFM2jq1V1FggyTBwZzO+42r91uX7P8HjObt+11bCwfXRrm9RjtbVojksqWvsG1orzNzrBv4tclFLa0/KXJNbCwsDADyewfAijnO/xuOHlh9XqLFz/yDkJCB6MlD3KS0Avh/U1Vr4REyTsmPht1jBoG8SbMoTOdwKDvJjrIIf/35WMUeQ0SvA5IIneC1IhGSGjQYCGPeF1SH5ym9S0Upzw8WCE6uBHwP6NDhvzcXMb7aXiI0AmQyJG2MKy/CpanLwS0lrum1ki1Wi1AYK55PGZUqQvcTSxsrsJ1CLFSAfPIo5wflbio96cEdz1GmeLVkB3zHR0dncNYEi26zLkv3F9BbNfQQR7gs8w3g7wkTNlSTaA7VczNj0pEpPqLOkYDVqWDdbWkDY2AqVkwOaRcLmf9luiQoapB4hQQ1yMRl+ufEvAAB0TyQnFQkfV63ejeDA5EXsrDMuEhxhA9ImCLb8uUBxkzCGnwlsCHuUw5ofYwhTR2S6Aa2El5ocKOKDMjcsnGU5LmPJXWkejFUmskdV6CtFNN5AUt0x8wQRDesMPI2+dUDfWUqx9Q51SXgfUflrEAzwiSu6xLd0DPMIy9z6a83ZD2gVNS96KQB4AUDiRoS4QtDQabAi6Q8dnc3g/QF/c3YdtnFsZmvnbKP2EHsoztABLc7dZoMc5b0vBsReNuy0Y1QDjdxcsM4rh28Ur3z9NLnPL984aR7lz5o3MSi50rh+3hGqHbsWOZJHOvoTpliFQj9JN6QqVBBProlUKXfiXkuk1UFwskGZv+gpT965RDCml7Sb5NFhHS0Su6Z9rdSjpMsi7wZbMxjwVP0b7ROBpAnNCuMxnDClqUgT3EPif44nMkXhvdX6IE1SljuBqhmz6nnTwA7AP+E+X1QQq/YCXkDFeloCL3kQxatx7U5Z+iVKUK8vpYaHevdHf3ODSURJc0yHqcMWZMtnhDAV3kufZghbClx7wkwoDfWc3qFNTjJ5Q0VtLcOO6lrIeTPZSuQfgWKRig7fxMvk9/mlEkjT20xTtYWhykufiDfMsLS4HsVu1uZFSK2ADcUcU2OkIwf0LkyoNNvMcpETd59FiWuhQdpAcBKvy5xGvPkO9bBeSGtIpNbs9oapPvM3MuibyZvi+I5FnEqmaMrhO23oi4AzIo0hcmUuXRUXdzJABdomgoiKrlF/uoohbHgLzXRNEoOpVrZzYI35CKOKFGKHrA42bzvABPcYnjvEWiZicVaGRfSPDEUj/sE8ULLUzErGXb6PjIWOBuFx2N2KwnGjfyvNK7GTF+9MZjb0gMlyeJK3leVbpB+IZXgITBklCxneEUd/K8cWSV0OVRwZXfJglYIDYp5NHaw9jZtXEhz0UJJbE0ZNfeEcm5xpQ8sU5NBtVkItdK+0+AAQAZ7ccZTCgKiQAAAABJRU5ErkJggg==";
//     			array_push($newresponse, $badge);
//     		}
//     		$response=$newresponse;
    
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
    							//'badgeimage' => new external_value(PARAM_TEXT, 'picture of the badge'),
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

