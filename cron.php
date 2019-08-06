<?php

// Autoload our classes
require __DIR__ . '/vendor/autoload.php';


// Run the services


// Load the sites and application settings into memory from config/"db"
$db = new \ImtRssAggregator\DatabaseProcessor\JsonDatabaseProcessor(__DIR__.'/db');
$db->loadTablesIntoMemory(['sites', 'app_settings', 'app_status']);

// Figure out which site has gone longest without an update
$oldest_site = null;

foreach ($db->tables['sites'] as $site_index => $site) {
	if (is_null($oldest_site) || 
		is_null($site->last_cached) ||
		$site->last_cached < $oldest_site->last_cached) {
		
		$oldest_site = $site;
		$oldest_site_index = $site_index;
	}
}

// Aggregate the latest "cache_limit" posts
$aggregator = null;
switch ($oldest_site->aggregator_type) {
	case 'fightback':
		$aggregator = new \ImtRssAggregator\RssAggregator\FightbackRssAggregator($oldest_site, $db->tables['app_status']->articles_processed);
		break;
	case 'wordpress':
		$aggregator = new \ImtRssAggregator\RssAggregator\WordPressRssAggregator($oldest_site, $db->tables['app_status']->articles_processed);
		break;
	case 'joomla':
		$aggregator = new \ImtRssAggregator\RssAggregator\JoomlaRssAggregator($oldest_site, $db->tables['app_status']->articles_processed);
		break;
	default:
		$aggregator = null;
		break;
}
if (is_null($aggregator))
	throw new Exception("The site '".$oldest_site->name."' has an unrecognized aggregator type: '".$oldest_site->aggregator_type);
$aggregator->fetchLatestPosts($db->tables['app_settings']->cache_limit);


// TODO!! SKIP IF NO NEW ARTICLES!! DONT RE-INDEX THEN SAVE CAUSE THAT STUPID.
/* Add a key to each cached article
Dont re-save an article if the key(hash??) is the same. Or copy over the cached index??*/

// Save them to the cache
$oldest_site_table_name = 'article_cache/'.$oldest_site->slug;
$db->tables[$oldest_site_table_name] = $aggregator->posts;
$db->saveWholeTable($oldest_site_table_name);

// Update the sites last-used or wtv
$db->tables['sites'][$oldest_site_index]->last_cached = strtotime("now");
$db->saveWholeTable('sites');
$db->tables['app_status']->articles_processed = $aggregator->top_post_index;
$db->saveWholeTable('app_status');


// See if anyone needs their mail..
$db->loadTableIntoMemory('mailing_list');
$count_people_notified = 0;
foreach($db->tables['mailing_list'] as $mailing_recipient_array) {
	$notifier = new \ImtRssAggregator\Notifier($db, $mailing_recipient_array, $oldest_site);

	if ($notifier->shouldFire()) {
		$notifier->fire();
		$count_people_notified++;
	}
}

// Some nice output for the runner :)
echo "Aggregated ".count($db->tables[$oldest_site_table_name])." articles for ".$oldest_site->name." at ".date("d M Y H:i:s", $oldest_site->last_cached).".";
if ($count_people_notified > 0)
	echo $count_people_notified." users received notifications.";
echo "\n";
