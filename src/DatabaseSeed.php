<?php
namespace MarxistSocialNews;
use Exception;

class DatabaseSeed {
	public object $tables;
	function __construct() {
		$this->tables = $this->constructSeed();
	}

	// Just scope out what tables you need. Refernce tables afterwards when creating new tables or setting/assuming defaults
	public function constructSeed(): object {
		return $this->constructSeedFromArray([]);
	}

	public function constructSeedFromArray(array $array): object {
		return (object) $array;
	}

	public function constructSeedFromJsonFile(string $path): object {
		return json_decode(file_get_contents($path));
	}
}

