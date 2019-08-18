<?php
namespace MarxistSocialNews;

class CronService {
	public $previous_service_history = null;
	public $db_config = null;

	function __construct($config = null) {
		foreach ($config as $key => $val) { // This is a good trait?
			if (property_exists($this, $key))
				$this->{$key} = $val;
		}
	}

	function run() {
		throw new Exception("Please implement this!!");
	}
}

