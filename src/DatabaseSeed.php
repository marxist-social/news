<?php
namespace MarxistSocialNews;
use Exception;

class DatabaseSeed {
	function __construct() {
		$this->tables = $this->constructSeed();
	}

	// Just scope out what tables you need. Refernce tables afterwards when creating new tables or setting/assuming defaults
	public function constructSeed() {
		return $this->constructSeedFromArray([]);
	}

	public function constructSeedFromArray($array) {
		return (object) $array;
	}

	public function constructSeedFromJsonFile($path) {
		return json_decode(file_get_contents($path));
	}
}

