<?php

// Database stuff
/*
db = new JsonDatabaseProcesser(__DIR__/'db')
db->loadTableIntoMemory('sites');

foreach (db->tables['sites'] as site)
	site_cache_reader = new SiteCacheReader(site, '__DIR__/article_cache')
	site_cache_reader->loadCacheIntoMemory()
	push onto cache_readers_array idk

view = new \Views\Homepage(['site_readers' => cache_readers_array])
view->render();

*/




























use ImtRssAggregator\RssAggregators\WordPressRssAggregator;
use ImtRssAggregator\RssAggregators\JoomlaRssAggregator;
use ImtRssAggregator\RssAggregators\FightbackRssAggregator;
use ImtRssAggregator\Views\Homepage;


// Autoload our classes
require __DIR__ . '/vendor/autoload.php';

// Load list of sites from the json config
$all_rss_sites = json_decode(file_get_contents('sites.json'));

// Foreach, create an aggregator object
$aggregators = [];
foreach ($all_rss_sites as $rss_site) {

	switch ($rss_site->type) {
		case 'fightback':
			$aggregator = new FightbackRssAggregator($rss_site);
			break;
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


