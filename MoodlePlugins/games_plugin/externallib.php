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


class games_services extends external_api{
	
	public static function get_avatar_configuration_parameters(){
		return new external_function_parameters(
			array(
					'userid' => new external_value(PARAM_INT, 'User Id')
			)
		);
	}

	public static function get_avatar_configuration($userid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_avatar_configuration_parameters(), 
					array(
						'userid' => $userid,
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
			$context = get_context_instance(CONTEXT_USER, $USER->id);
			self::validate_context($context);
		
			$sql = "SELECT 
						a.userid,
						a.alias,
						a.aplicacion,
						a.estrellas,
						a.color_cabello,
						a.estilo_cabello,
						a.traje_color_principal,
						a.traje_color_secundario,
						a.rostro,
						a.color_de_piel,
						a.escudo,
						a.imagen_recortada,
						a.ultima_modificacion
					FROM {avatar_log} AS a
					WHERE a.userid = $userid
					ORDER BY ultima_modificacion desc
					LIMIT 1";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;	
	}



	public static function get_avatar_configuration_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
				'userid' 	=> new external_value(PARAM_INT,  'User ID'),
				'alias' 	=> new external_value(PARAM_TEXT, 'Alias'),
				'aplicacion' 	=> new external_value(PARAM_TEXT, 'Aplicacion'),
				'estrellas' 	=> new external_value(PARAM_INT, 'Estrellas'),
				'color_cabello' 	=> new external_value(PARAM_TEXT, 'Color Cabello'),
				'estilo_cabello' 	=> new external_value(PARAM_TEXT, 'Estilo Cabello'),
				'traje_color_principal' 	=> new external_value(PARAM_TEXT, 'Traje color principal'),
				'traje_color_secundario' 	=> new external_value(PARAM_TEXT, 'Traje color secundario'),
				'rostro' 	=> new external_value(PARAM_TEXT, 'Rostro'),
				'color_de_piel' 	=> new external_value(PARAM_TEXT, 'Color de piel'),
				'escudo' 	=> new external_value(PARAM_TEXT, 'Escudo'),
				'imagen_recortada' 	=> new external_value(PARAM_TEXT, 'Imagen Recortada'),
				'ultima_modificacion' 	=> new external_value(PARAM_TEXT, 'Ultima Fecha de Modification')
				)
			)
		);		
	}


	public static function add_avatar_configuration_parameters(){
 		return new external_function_parameters(
            array(
                'avatars' => new external_multiple_structure(
                    new external_single_structure(
                        array(
							'userid' 	=> new external_value(PARAM_INT,  'User ID'),
							'alias' 	=> new external_value(PARAM_TEXT, 'Alias'),
							'aplicacion' 	=> new external_value(PARAM_TEXT, 'Aplicacion'),
							'estrellas' 	=> new external_value(PARAM_INT, 'Estrellas'),
							'color_cabello' 	=> new external_value(PARAM_TEXT, 'Color Cabello'),
							'estilo_cabello' 	=> new external_value(PARAM_TEXT, 'Estilo Cabello'),
							'traje_color_principal' 	=> new external_value(PARAM_TEXT, 'Traje color principal'),
							'traje_color_secundario' 	=> new external_value(PARAM_TEXT, 'Traje color secundario'),
							'rostro' 	=> new external_value(PARAM_TEXT, 'Rostro'),
							'color_de_piel' 	=> new external_value(PARAM_TEXT, 'Color de piel'),
							'escudo' 	=> new external_value(PARAM_TEXT, 'Escudo'),
							'imagen_recortada' 	=> new external_value(PARAM_TEXT, 'Imagen Recortada'),
							'ultima_modificacion' 	=> new external_value(PARAM_TEXT, 'Ultima Fecha de Modification')
                        )
                    )
                )
			)
		);
	}
	public static function add_avatar_configuration(array $avatars){
		global $USER;
		global $DB;

		try {
        	$params = self::validate_parameters(self::add_avatar_configuration_parameters(), array('avatars'=>$avatars));
 
	        foreach ($params['avatars'] as $avatar) {

	            // all the parameter/behavioural checks and security constrainsts go here,
	            // throwing exceptions if neeeded and and calling low level (userlib)

				$newrecord = $DB->insert_record('avatar_log', $avatar, true);
				$response = array('id' => $newrecord);
	        }
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}



	public static function add_avatar_configuration_returns(){
		return new external_function_parameters(
				array(
						'id' => new external_value(PARAM_INT, 'Id of the latest course'),
				)
		);
	}

}