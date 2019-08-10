<?php
namespace ImtRssAggregator;
use Exception;

class RssAggregator {

	public $user_agent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36";
	public $site_info;
	public $posts;
	public $base_post_index;
	public $top_post_index;

	/**
	 * Takes the supplied site info and assigns it to a property
	 */
	function __construct($site_info, $base_post_index = 0) {
		$this->site_info = $site_info;
		$this->base_post_index = $base_post_index;
		$this->top_post_index = $base_post_index;
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
		
		$raw_rss_data = $this->applyRawDataHacks($this->site_info->raw_data_hacks, $raw_rss_data); // TODOOO MORE HACKs! turn the fightback class into hacks. Add Lal Salaam hacks.
		$posts = $this->parseRssIntoPosts($raw_rss_data);
		$posts = $this->limitPosts($posts, $n);
		$posts = $this->indexPosts($posts);

		$this->posts = $posts; // Array of post objects
	}

	public function parseRssIntoPosts($raw_rss_data) {
		throw new Exception("Please implement this method!!");
	}

	public function limitPosts($posts, $n) {
		$new_posts_array = [];

		for ($i = 0; $i < $n && $i < count($posts); $i++) {
			if (isset($posts[$i]))
				$new_posts_array[$i] = $posts[$i];
		}

		return $new_posts_array;
	}

	public function indexPosts($posts) {
		$this->current_post_index = $this->base_post_index;
		
		foreach ($posts as $post) {
			$post->index = (++$this->top_post_index);
		}

		return $posts;
	}

	// Subclass keys aren't anything real.. it's just for this function, and in sites.json
	// Whats the proper way to upgrade to a subclass when you dont know the subclass ahead of time??
	// Im making a method in the parent class, and it gets a string... thoughts?
	// Or maybe a trait... write now it's a function in the aggregator service 
	public function upgradeToSubClass($sub_class_key) {
		$sub_classes_names = [
			'wordpress'
		];

		// TODO (low prioerity)
	}

	// Hacks are applied to the data sequentially
	public function applyRawDataHacks($hack_function_names, $raw_data) {
		if (!is_null($hack_function_names)) {
			foreach ($hack_function_names as $hack_name) {
				$full_hack_name = $hack_name.'_raw_data_hack';
				$raw_data = $this->$full_hack_name($raw_data); // LOL this line
			}
		}

		return $raw_data;
	}
}
