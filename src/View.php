<?php
namespace MarxistSocialNews;

class View {

	public $db;
	public $user_properties;
	public $template = null;
	public $base_url;

	function __construct($db = null, $user_properties = null) {
		$base_url_parts = explode('/', $_SERVER['SCRIPT_NAME']);
		array_pop($base_url_parts);
		$base_url = implode('/', $base_url_parts);
		if ($base_url === '/')
			$base_url = '';
		$this->base_url = $base_url;


		$this->user_properties = $user_properties;
		$this->db = $db;
		$this->template = $this->constructTemplate();
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

