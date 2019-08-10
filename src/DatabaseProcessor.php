<?php
namespace ImtRssAggregator;
use Exception;

class DatabaseProcessor {
	public $tables = null;
	public $path = null;

	function __construct($path) {
		$this->path = $path;
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
}

