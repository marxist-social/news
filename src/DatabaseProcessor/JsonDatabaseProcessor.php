<?php
namespace MarxistSocialNews\DatabaseProcessor;
use MarxistSocialNews\DatabaseProcessor;

class JsonDatabaseProcessor extends DatabaseProcessor {
	public function loadTableIntoMemory($table_name) {
		// Check if it exists. If not, load empty array
		if (file_exists($this->makeTablePath($table_name)))
			$this->tables[$table_name] = json_decode(file_get_contents($this->makeTablePath($table_name)));
		else
			$this->tables[$table_name] = [];
	}

	public function saveWholeTable($table_name) {
		file_put_contents($this->makeTablePath($table_name), json_encode($this->tables[$table_name]));
	}

	private function makeTablePath($table_name) {
		return $this->path.'/'.$table_name.'.json';
	}
}

