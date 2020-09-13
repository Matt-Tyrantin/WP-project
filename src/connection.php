<?php
	namespace App\Database;

	require_once 'configuration.php';

	use \App\Configuration as Configuration;
	use mysqli;

	/*
	* Establishes connection with project's database in a form of singleton pattern
	*/
	class Connection 
	{
		private static $connection;

		public static function GetConnection()
		{
			if (self::$connection == null) {
				self::$connection = new mysqli(
					Configuration::Get('DB_SERVER_NAME'),
					Configuration::Get('DB_USERNAME'),
					Configuration::Get('DB_PASSWORD'),
					Configuration::Get('DB_DATABASE')
				);

				if (self::$connection->connect_error) {
					throw new \Exception('Connection failed: '.$conn->connect_error);
				}
			}

			return self::$connection;
		}

		/*
		* Immediatly queries a statment
		*/
		public static function Query($query)
		{
			return self::GetConnection()->query($query);
		}

		/*
		* Returns all errors if they exist
		*/
		public static function GetErrors()
		{
			return self::GetConnection()->error;
		}

		/*
		* Returns the id of last inserted table row
		*/
		public static function GetLastInsertedID()
		{
			return self::GetConnection()->insert_id;
		}
	}
?>