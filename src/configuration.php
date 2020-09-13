<?php
	// PHP code containing configuration and constants for the web-page
	namespace App;

	class Configuration {
		private const configs = array(
			'DB_SERVER_NAME' => 'localhost',
			'DB_USERNAME'    => 'root',
			'DB_PASSWORD'    => '',
			'DB_DATABASE'    => 'student_activity_organizer'
		);

		public static function Get($key) 
		{
			$key = strtoupper($key);

			if (empty(self::configs[$key])) {
				return false; 
			}

			return self::configs[$key];
		}
	}
?>