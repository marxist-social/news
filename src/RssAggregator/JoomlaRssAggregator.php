<?php
namespace ImtRssAggregator\RssAggregator;
use ImtRssAggregator\RssAggregator;
use Exception;

class JoomlaRssAggregator extends RssAggregator {
	public function parseRssIntoArray($raw_rss_data) {
		throw new Exception("Please implement this method!!");
	}
}
