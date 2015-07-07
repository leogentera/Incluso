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
 * External Web Service Catalogs
 *
 * @package    localcatalogs
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "../../config.php"); 

class MoodleSelectItem
{
	public $value;

	public function exchangeArray($data)
	{
		$this->value     =  $data;
	}
}

class local_catalogs_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function values_parameters() {
        return new external_function_parameters(
                array('catalogname' => new external_value(PARAM_TEXT, 'The name of the catalog. By default it is "securityquestions"', VALUE_DEFAULT, 'securityquestions'))
        );
    }

    /**
     * Returns welcome message
     * @return array of catalog elements
     */
    public static function values($catalogname = 'CustomCountry') {
        global $USER;
	global $DB;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::values_parameters(),
                array('catalogname' => $catalogname));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

	$sql = 'select param1 from {user_info_field} where shortname=:fieldname';
	$params = array('fieldname' => $catalogname);
	$result = $DB->get_field_sql($sql, $params);
	$listOfValues = explode(chr(10), $result );
	return $listOfValues;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
	public static function values_returns() {
	    return new external_multiple_structure(
	        new external_value(PARAM_TEXT, 'id of the created user')
        );
    }
}