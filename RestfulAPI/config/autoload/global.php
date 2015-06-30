<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array (
	'MOODLE_API_URL' => 'http://moodle.gentera.com:8080/webservice/rest/server.php?wstoken=%s&wsfunction=%s&moodlewsrestformat=json',
	'TOKEN_GENERATION_URL' => 'http://moodle.gentera.com:8080/login/token.php?username=%s&password=%s&service=%s',
	'MOODLE_SERVICE_NAME' => 'profile',
);

/*
 * // Use the global configs by doing the following:
 * // Example for being inside any of your Controllers
 * $serviceLocator = $this->getServiceLocator();
 * $config         = $serviceLocator->get('config');
 * $myValue        = $config['someVariable'];
 * 
*/