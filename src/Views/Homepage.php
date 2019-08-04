<?php
namespace ImtRssAggregator\Views;
use ImtRssAggregator\View;

class Homepage extends View {
	public function constructTemplate() {
		return <<<TEMPLATE
			<html>
				<head>
					<meta name="viewport" content="width=device-width, initial-scale=1">
				</head>
				<body>
					<h1>Welcome to the IMT RSS aggregator!</h1>
					<div class="aggregators">
						%aggregators%
					</div>
				</body>
			</html>
		TEMPLATE;
	}

	/**
	 * Take this view and render it to an HTML string
	 */
	public function render() {
		// Perform SIMPLE replacements
		$aggregators_html = '';
		foreach ($this->user_properties['aggregators'] as $aggregator) {
			$aggregators_html .= <<<TEMPLATE
				<div class="aggregator">
					<h2 class="aggregator__title">{$aggregator->getName()}</h2>
					<p class="aggregator__meta">{$aggregator->getCountry()} <small>{$aggregator->getProvince()}</small></p>
				</div>
			TEMPLATE;
		}

		$template = str_replace('%aggregators%', $aggregators_html, $this->template);

		return $template;
	}
}
