<?php
namespace ImtRssAggregator;

class View {

	public $user_properties;
	public $template = null;

	function __construct($user_properties) {
		$this->user_properties = $user_properties;
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

