<?php
namespace ImtRssAggregator\RssAggregators;
use ImtRssAggregator\RssAggregator;
use Exception;

class JoomlaAggregator extends RssAggregator {
	public function parseRssIntoArray($raw_rss_data) {
		throw new Exception("Please implement this method!!");
	}
}
