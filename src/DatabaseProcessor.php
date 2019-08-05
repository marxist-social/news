<?php

namespace ImtRssAggregator;

class DatabaseProcessor {
	public $tables = null;
	public $path = null;

	function __construct($path) {
		$this->path = path;
	}

	public function loadTablesIntoMemory($table_names) {
		foreach($table_names as $table_name) {
			$this->loadTableIntoMemory($table_name);
		}
	}

	public function loadTableIntoMemory($table_name) {
		throw new Exception("You have to implement loadTableIntoMemory()!");
	}

	public function saveWholeTables($table_names) {
		foreach($table_names as $table_name) {
			$this->saveWholeTable($table_name);
		}
	}

	public function saveWholeTable($table_name) {
		throw new Exception("You have to implement saveWholeTable()!");
	}
}

