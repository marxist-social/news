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


// Create the home page view
$view = new Homepage(['aggregators' => $aggregators]);
echo $view->render(); 


