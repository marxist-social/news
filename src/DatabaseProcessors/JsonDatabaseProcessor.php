<?php

namespace ImtRssAggregator;
use ImtRssAggregator\DatabaseProcessor;

class JsonDatabaseProcessor extends DatabaseProcessor {
	public function loadTableIntoMemory($table_name) {
		$this->tables[$table_name] = file_get_contents($this->makeTablePath($table_name));
	}

	public function saveWholeTable($table_name) {
		file_put_contents($this->makeTablePath($table_name), $this->tables[$table_name]);
	}

	private function makeTablePath($table_name) {
		return $this->path.'/'.$table_name.'.json';
	}
}

