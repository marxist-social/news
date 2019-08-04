<?php
namespace ImtRssAggregator;

class View {

	public $user_properties;
	public $template = null;
	public $base_url;

	function __construct($user_properties) {
		$this->user_properties = $user_properties;
		$this->template = $this->constructTemplate();

		var_dump($_SERVER);
		var_dump($_SERVER['SCRIPT_NAME']);
		$base_url_parts = explode('/', $_SERVER['SCRIPT_NAME']);
		var_dump($base_url_parts);
		array_pop($base_url_parts);
		var_dump($base_url_parts);
		$base_url = implode('/', $base_url_parts);
		var_dump($base_url);
		if ($base_url !== '')
			$base_url = '/'.$base_url;
		var_dump($base_url);
		die();
		$this->base_url = $base_url;
	}

	public function constructTemplate() {
		return <<<TEMPLATE
			<p>Please define a template!</p>
		TEMPLATE;
	}

	/**
	 * Take this view and render it to an HTML string
	 */
	public function render() {
		// Perform SIMPLE replacements
		$template = $this->template;
		foreach ($this->user_properties as $prop_key => $prop_value) {
			$template = str_replace('%'.$prop_key.'%', $prop_value, $template);
		}
		return $template;
	}
}

