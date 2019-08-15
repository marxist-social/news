<?php
namespace MarxistSocialNews\DatabaseProcessor;
use MarxistSocialNews\DatabaseProcessor;
use Exception;

class JsonDatabaseProcessor extends DatabaseProcessor {
	public function loadTableIntoMemory($table_name) {
		// Check if it exists. If not, load empty array
		if ($this->tableExistsInDatabase($table_name))
			$this->tables[$table_name] = json_decode(file_get_contents($this->makeTablePath($table_name)));
		else
			throw new Exception("This table $table_name doesn't exist!!");
	}

	public function tableExistsInDatabase($table_name) {
		return file_exists($this->makeTablePath($table_name));
	}

	public function saveWholeTable($table_name) {
		$this->createSubDirectoriesIfNotExisting($table_name);

		file_put_contents($this->makeTablePath($table_name), json_encode($this->tables[$table_name]));
	}

	private function makeTablePath($table_name) {
		return $this->path.'/'.$table_name.'.json';
	}

	public function createTable($table_name) {
		$this->tables[$table_name] = [];
		$this->saveWholeTable($table_name);
	}

	public function seedTable($seed, $table_name) {
		// Is this a one-record config-type table? (i.e. not a set of records, just like.. a key value array lol)
		$is_one_record = false;
		foreach($seed->tables->$table_name as $key => $maybe_array_int) {
			if (!is_int($key)) { // LOL
				$is_one_record = true;
				break;
			}
			break;
		}

		if ($is_one_record) {
			$this->tables[$table_name] = array_merge($this->schema->tables[$table_name], $seed->tables->$table_name);
		} else {
			$table_array = [];
			foreach($seed->tables->$table_name as $individual_seed) {
				array_push($table_array, array_merge($this->schema->tables[$table_name], $individual_seed));
			}
			$this->tables[$table_name] = $table_array;
		}

		$this->saveWholeTable($table_name);		
	}

	private function createSubDirectoriesIfNotExisting($table_name) {
		$table_parts = explode('/', $table_name);
		if (count($table_parts) === 1)
			return;

		array_pop($table_parts);
		$final_dir = implode('/', $table_parts);

		if (is_dir($this->path.'/'.$final_dir))
			return;
		elseif (is_file($this->path.'/'.$final_dir))
			throw new Exception("There is a final where we expect a subdirectory if not existing!");

		$dir_str = $this->path.'/';
		foreach($table_parts as $tp) {
			$dir_str .= $tp;
			mkdir($dir_str);
			$dir_str .= '/';
		}
	}
}

