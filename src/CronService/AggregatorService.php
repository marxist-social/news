<?php
namespace MarxistSocialNews\CronService;
use MarxistSocialNews\CronService;
use Exception;

class AggregatorService extends CronService {
	function run() {
		// Load the sites and application settings into memory from config/"db"
		$db = $this->connectToDatabase(['path' => $this->db_config['path'], 'seed' => $this->db_config['seed'], 'tables' => ['sites', 'app_settings', 'app_status']]); // connectToDatabase good trait ?

		// Figure out which site has gone longest without an update
		$site_and_index = $this->getLeastUpToDateSite($db->tables['sites']);
		$oldest_site = $site_and_index['site'];
		$oldest_site_index = $site_and_index['index'];

		// Aggregate the latest "cache_limit" posts
		$aggregator_class = $this->getAggregatorByType($oldest_site->aggregator_type);
		$aggregator = new $aggregator_class($oldest_site, $db->tables['app_status']->articles_processed);
		$aggregator->fetchLatestPosts($db->tables['app_settings']->cache_limit);

		// Save them to the cache
		$oldest_site_table_name = 'article_cache/'.$oldest_site->slug; // get table name
		if (!$db->tableExistsInDatabase($oldest_site_table_name)) // create db table/cache if it doesnt already exist
			$db->createTable($oldest_site_table_name);

		$db->loadTablesIntoMemory($oldest_site_table_name);

		// TODO -> function checkIfCanSkip - sees if all URLs are the same as the cached one

		if (!is_null($db->tables[$oldest_site_table_name])) // This maintains indexes.
			$db->tables[$oldest_site_table_name] = $this->modifyArrayWithUrlIndexing($aggregator->posts, $db->tables[$oldest_site_table_name]);
		else
			$db->tables[$oldest_site_table_name] = $aggregator->posts; // overwrite the table ! Either with cross indexed posts or nothin.

		$db->saveWholeTable($oldest_site_table_name); // save it

		// Update the sites last-used or wtv
		$db->tables['sites'][$oldest_site_index]->last_cached = strtotime("now");
		$db->saveWholeTable('sites');
		$db->tables['app_status']->articles_processed = $aggregator->top_post_index;
		$db->saveWholeTable('app_status');

		// Save a couple values:
		$count_articles_cached = count($db->tables[$oldest_site_table_name]);

		// Remove tables from memory
		$db->removeTablesFromMemory(['sites', 'app_settings', 'app_status', $oldest_site_table_name]);

		// And return the output + info other services will need
		return [
			'output' => "Aggregated {$count_articles_cached} articles for ".$oldest_site->name." at ".date("d M Y H:i:s", $oldest_site->last_cached).".",
			'oldest_site' => $oldest_site
		];
	}

	private function getAggregatorByType($type_name) { // Great trait also lol?

		$types = [
			'wordpress-api' => \MarxistSocialNews\Aggregator\WordPressApiAggregator::class,
			'rss-atom' => \MarxistSocialNews\Aggregator\RssAtomAggregator::class
		];

		return $types[$type_name];
	}

	private function connectToDatabase($config) {
		$db = new \MarxistSocialNews\DatabaseProcessor\JsonDatabaseProcessor($config['path'], $config['seed']);
		$db->loadTablesIntoMemory($config['tables']);

		return $db;
	}

	private function getLeastUpToDateSite($sites_array) {
		$oldest_site = null;

		foreach ($sites_array as $site_index => $site) {
			if (is_null($oldest_site) || 
				is_null($site->last_cached) ||
				$site->last_cached < $oldest_site->last_cached) {
				
				$oldest_site = $site;
				$oldest_site_index = $site_index;
			}
		}

		return ['site' => $oldest_site, 'index' => $oldest_site_index];
	}

	/**
	 * Modify array with url indexing function
	 * 
	 * Otherwise this happens:
	 * Site A collects posts 'bolsanaro out!' => 345, 'trudeau sucks!' => 346, 'bj is a clown!' => 347, 'https://probably-urls/' => 348.
	 * Site B collects posts 'x' => 349, 'y' => 350, 'z' => 351, 'aa' => 352
	 * ...
	 * Site A collects posts 'bj is a clown!' => 421, 'https://probably-urls/' => 422, 'todays new article' => 433, 'todays second article' => 434
	 * This is not accurate. The bj clown article and probably urls were published earlier than site B's articles, yet occupy a high index.
	 * Cross-referencing against URLs allows us to maintain order among separate caches.
	 * 
	 * @param PostArray new_posts
	 * @param PostArray old_posts
	 * 
	 * @return PostArray modified_new_posts
	 */
	private function modifyArrayWithUrlIndexing($new_posts, $old_posts) {
		// At some point I should make sure both of these are post objects...
		// As it stands, they're not? or NP is, OP isn't...

		$overwrite_table = [];

		if (!is_null($new_posts) && !is_null($old_posts)) // beautiful
			foreach ($new_posts as $n_key => $np)
				foreach ($old_posts as $o_key => $op)
					if ($np->link === $op->link)
						$new_posts[$n_key]->index = $op->index;

		return $new_posts;

		// it would maybe be nice to see how many were re-indexed.
	}
}
