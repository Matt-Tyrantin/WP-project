<?php
	namespace App\Entities;

	require_once($_SERVER['DOCUMENT_ROOT'] . '/src/connection.php');

	use \App\Database\Connection as Connection;
	use mysqli;

	// Abstract class where a single table row is represented with an entity instance
	abstract class Entity {
		/*
		* Entity name corresponds to table name
		*/
		protected static $entity_name;

		/*
		* These properties are defined in each extended class
		*/
		protected static $primary_key = 'id';
		protected static $columns;

		/*
		* Associative array where keys represent columns and values values for one row
		*/
		public $attributes;

		/*
		* $entity_name is defined in each extended class
		*
		* $entity_attributes is an array containing row data. The array can be Associative
		* where each key corresponds to a table column
		*/
		public function __construct($entity_attributes) 
		{
			if (empty($entity_attributes[0])) {
				foreach ($entity_attributes as $attribute => $value) {
					$this->SetAttribute($attribute, $value);
				}
			} else {
				foreach ($entity_attributes as $index => $value) {
					$this->SetAttribute(static::$columns[$index], $value);
				}
			}
		}

		/*
		* Returns an array of all entities from the existing table. Table is defined with $entity_name.
		*
		* $params is associative array containing pairs (column, value). If $params isn't provided,
		* all entities from the table will be fetched
		*/
		public static function GetAll($params = [])
		{
			$sql = 'SELECT * FROM '.static::$entity_name;
			$result = array();

			if (!empty($params)) {
				$sql .= ' WHERE ';

				foreach ($params as $col => $value) {
					$sql .= $col.'='.$value.' AND ';
				}

				// Remove last 'AND'
				$sql = substr($sql, 0, -5);
			}

			$result = Connection::Query($sql);
			$entities = array();
			$class_name = static::class;

			if ($result === false) {
				return array();
			}

			while ($row = $result->fetch_assoc()) {
				$entities[] = new $class_name($row);
			}

			return $entities;
		}

		/*
		* Returns a single object which contains $key_value as it's primary key value
		*/
		public static function GetFromPrimaryKey($key_value)
		{
			$object_array = static::GetAll([static::$primary_key => $key_value]);

			if (!empty($object_array[0])) {
				return $object_array[0];
			} else {
				return null;
			}
		}

		/**
		 * Shorter version of GetFromPrimaryKey
		 */
		public static function Get($key_value)
		{
			return static::GetFromPrimaryKey($key_value);
		}

		/*
		* Sets entity's attribute which corresponds to a column
		*/
		public function SetAttribute($attribute, $value)
		{
			$this->attributes[$attribute] = $value;
		}

		/*
		* Returns entity's attribute which corresponds to column value
		*/
		public function GetAttribute($attribute) 
		{
			if (!empty($this->attributes[$attribute])) {
				return $this->attributes[$attribute];
			} else {
				return null;
			}
		}

		/*
		* Updates or inserts this entity depending if it already exists in the table.
		* Returns whether the update/insert was successful or not
		*/
		public function Save()
		{
			if ($this->Exists()) {
				return $this->UpdateRow();
			} else {	
				return $this->InsertRow();
			}
		}

		/*
		* Removes table row corresponding to this entity. Returns whether the removal was success
		*/
		public function Delete()
		{
			return $this->DeleteRow();
		}

		/*
		* Checks whether this entity is already in the table or not
		*/
		public function Exists()
		{
			if ($this->GetAttribute(static::$primary_key) == null) {
				return false;
			}

			$sql  = 'SELECT * FROM '.static::$entity_name.' WHERE ';
			$sql .= $this->GetRowIdentifierStatement();
			$sql .= ' LIMIT 1';

			return mysqli_num_rows(Connection::Query($sql)) > 0;
		}

		/*
		* Returns mysql statement for primary keys in form of:
		*     pk1=pk1_value AND pk2=pk2_value AND ... 
		*
		* Used for identifying which row to manipulate
		*/
		private function GetRowIdentifierStatement()
		{
			return static::$primary_key.'='.$this->GetAttribute(static::$primary_key);;
		}

		/*
		* Inserts the entity into table row. Returns whether the insertation was success
		*/
		private function InsertRow()
		{
			$sql = 'INSERT INTO '.static::$entity_name.' (';
			foreach (static::$columns as $column) {
				if ($this->GetAttribute($column) == null) continue;
				$sql .= $column.',';
			}

			// Remove the last comma ','
			$sql = substr($sql, 0, -1);
			$sql .= ') VALUES (';

			foreach (static::$columns as $column) {
				$value = $this->GetAttribute($column);
				if ($value == null) continue;
				if (is_numeric($value)) {
					$sql .= $value.',';
				} else {
					$sql .= "'".$value."',";
				}
			}

			// Remove the last comma ','
			$sql = substr($sql, 0, -1);
			$sql .= ')';

			$inserted = Connection::Query($sql);

			if ($inserted) {
				$this->SetAttribute(static::$primary_key, Connection::GEtLastInsertedID());
			}

			return $inserted;
		}

		/*
		* Updates table row with entity attributes. Returns whether the update was success
		*/
		private function UpdateRow()
		{
			$sql = 'UPDATE '.static::$entity_name.' SET ';
			foreach (static::$columns as $column) {
				$sql .= $column.'=';
				$value = $this->GetAttribute($column);

				if (is_numeric($value)) {
					$sql .= $value.',';
				} else {
					$sql .= "'".$value."',";
				}
			}

			// Remove the last comma ','
			$sql  = substr($sql, 0, -1);
			$sql .= ' WHERE ';
			$sql .= $this->GetRowIdentifierStatement();

			return Connection::Query($sql);
		}

		/*
		* Deletes table row corresponding to this entity. Returns whether the removal was success
		*/
		private function DeleteRow()
		{
			$sql  = 'DELETE FROM '.static::$entity_name.' WHERE ';
			$sql .= $this->GetRowIdentifierStatement();

			return Connection::Query($sql);
		}
	}
?>