<?php
namespace ImtRssAggregator;
use Exception;

class RssAggregator {

	public $user_agent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36";
	public $site_info;
	public $posts;

	/**
	 * Takes the supplied site info and assigns it to a property
	 */
	function __construct($site_info) {
		$this->site_info = $site_info;
	}

	/**
 	 * Fetches the latest n posts from the source.
 	 *
 	 * @param int $n number of posts to fetch.
	 */
	public function fetchLatestPosts($n) {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);

		$raw_rss_data = file_get_contents($this->site_info->url, false, $context);
		$parsed_rss_posts = $this->parseRssIntoPosts($raw_rss_data);
		$trimmed_rss_posts = $this->limitPosts($parsed_rss_posts, $n);
		$this->posts = $trimmed_rss_posts; // Array of post objects
	}

	public function parseRssIntoPosts($raw_rss_data) {
		throw new Exception("Please implement this method!!");
	}

	public function limitPosts($parsed_rss_posts, $n) {
		$new_posts_array = [];

		for ($i = 0; $i < $n && $i < count($parsed_rss_posts); $i++) {
			if (isset($parsed_rss_posts[$i]))
				$new_posts_array[$i] = $parsed_rss_posts[$i];
		}

		return $new_posts_array;
	}
}
