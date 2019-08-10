<?php
namespace ImtRssAggregator;

class CronService {
	public $previous_service_history = null;
	public $db_path = null;



	function __construct($config = null) {

		foreach ($config as $key => $val) // This is a good trait?
			$this->{$key} = $val;
	}

	function run() {
		throw new Exception("Please implement this!!");
	}
}

