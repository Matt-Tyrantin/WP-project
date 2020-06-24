<?php
	// PHP code containing configuration and constants for the web-page

	class Config {
		private static configurations = {
			DB_SERVER_NAME = '';
			DB_USERNAME    = 'root';
			DB_PASSWORD    = '';
			DB_DATABASE    = '';
		};

		public static Get($key) 
		{
			$key = strtoupper($key);

			if empty(self::configurations[$key]) {
				return echo('Attempted to get non-existant configuration with key '.$key);
			}

			return self::configurations[$key];
		}
	}
?>