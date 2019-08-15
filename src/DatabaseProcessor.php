<?php
namespace MarxistSocialNews;
use Exception;

class DatabaseProcessor {
	public $tables = null;
	public $path = null;

	function __construct($path, $seed = null) {
		$this->path = $path;

		if (!file_exists($this->path) && !is_dir($this->path))
			mkdir($this->path);
		elseif (file_exists($this->path) && !is_dir($this->path))
			throw new Exception("There is a file where we expect the path $this->path!");

		$this->schema = $this->getSchemaArray();

		// Check if the tables exist in the database.
		// If a table doesn't exist, copy the keys from the schema, empty array, create file
		// If a table doesn't exist aND a seed is passed, do above ++ fill the data array
		// There are tables AND configs in the schema.... we'll see how it goes! (key => val v.s. 0 => key, 1 => val..)

		foreach ($this->schema->tables as $table_name => $keys) {
			if (!$this->tableExistsInDatabase($table_name) && !is_null($seed)) {
				$this->createTable($table_name);
				$this->seedTable($seed, $table_name);
			} elseif (!$this->tableExistsInDatabase($table_name)) {
				$this->createTable($table_name);
			}
		}

		// Done, assume all database tables exist in DB. If they didn't they got created, and seeded.
	}

	public function createTables($table_names) {
		if (!is_array($table_names) && is_string($table_names))
			$this->createTable($table_names); // fool. .. we will allow it.
		else
			foreach($table_names as $table_name)
				$this->createTable($table_name);
	}

	public function createTable($table_name) {
		throw new Exception("You have to implement createTable()!");
	}

	public function loadTablesIntoMemory($table_names) {
		if (!is_array($table_names) && is_string($table_names))
			$this->loadTableIntoMemory($table_names); // fool. .. we will allow it.
		else
			foreach($table_names as $table_name)
				$this->loadTableIntoMemory($table_name);
	}

	public function loadTableIntoMemory($table_name) {
		throw new Exception("You have to implement loadTableIntoMemory()!");
	}

	public function saveWholeTables($table_names) {
		foreach($table_names as $table_name)
			$this->saveWholeTable($table_name);
	}

	// Would avoid using this function if possible. Built for simple file-based databases, not the most effecient
	public function saveWholeTable($table_name) {
		throw new Exception("You have to implement saveWholeTable()!");
	}

	public function removeTablesFromMemory($table_names) {
		foreach($table_names as $table_name)
			$this->removeTableFromMemory($table_name);
	}

	public function removeTableFromMemory($table_name) {
		unset($this->tables[$table_name]);
	}

	public function tableExistsInDatabase($table_name) {
		throw new Exception("You have to implement tableExistsInDatabase()!");
	}

	public function seedTable($seed, $table_name) {
		throw new Exception("You have to implement seed_table()!");
	}



	private function getSchemaArray() {
		return (object) [
			'tables' => [
				'app_settings' => [
					"cache_limit" => null
				],
				'app_status' => [
					"articles_processed" => null,
					"app_started" => null
				],
				'mailing_list' => [
					"should_notify" => null,
					"services" => null,
					"sites" => null,
					"frequency" => null,
					"last_article_processed" => null,
					"email" => null,
					"phone_number" => null,
					"phone_provider" => null
				],
				'sites' => [ // These are the tables
					"name" => null, // These are the columns. key is name, val is default value
					"slug" => null,
					"url" => null,
					"aggregator_type" => null,
					"country" => null,
					"province" => null,
					"last_cached" => null,
					"raw_data_hacks" => null,
					"post_object_hacks" => null,
					"flags" => null
				]
			]
		];
	}
}

