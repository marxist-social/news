<?php
use ImtRssAggregator\RssAggregators\WordPressRssAggregator;
use ImtRssAggregator\RssAggregators\JoomlaRssAggregator;
use ImtRssAggregator\Views\Homepage;


// Autoload our classes
require __DIR__ . '/vendor/autoload.php';

// Load list of sites from the json config
$all_rss_sites = json_decode(file_get_contents('sites.json'));

// Foreach, create an aggregator object
$aggregators = [];
foreach ($all_rss_sites as $rss_site) {

	switch ($rss_site->type) {
		case 'wordpress':
			$aggregator = new WordPressRssAggregator($rss_site);
			break;
		case 'joomla':
			$aggregator = new JoomlaRssAggregator($rss_site);
			break;
		default:
			$aggregator = null;
			break;
	}

	if (!is_null($aggregator))
		array_push($aggregators, $aggregator);
}

// Foreach, get the latest data from the source
foreach ($aggregators as $aggregator) {
	$aggregator->fetchLatestPosts(6);
}

// Create the home page view
$view = new Homepage(['aggregators' => $aggregators]);
echo $view->render(); 


