<?php
namespace MarxistSocialNews;
use Exception;

class Aggregator {

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
     * @throws Exception
     */
	public function fetchLatestPosts(int $n) {
		$raw_data = $this->retrieveRawDataFromSite(); // Get the raw data (re-implement on child classes)
		$raw_data = $this->applyRawDataHacks($this->site_info->raw_data_hacks, $raw_data);

		$posts = $this->parseRawDataIntoPosts($raw_data); // Parse raw data into Post objects (re-implement on children)
        $posts = $this->setContributor($posts);
		$posts = $this->applyPostObjectHacks($this->site_info->post_object_hacks, $posts);

		$posts = $this->limitPosts($posts, $n); // limit em if needed
		$posts = $this->indexPosts($posts); // Index em

		$this->posts = $posts; // Array of post objects
	}

	// Applies modifiers to the domain, queries for posts, returns raw result
	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);
        return file_get_contents($this->site_info->url, false, $context);
	}

    /**
     * @param $raw_data
     * @return array|void
     * @throws Exception
     */
	public function parseRawDataIntoPosts($raw_data) {
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
		foreach ($posts as $post) {
			$post->index = (++$this->top_post_index);
		}
		return $posts;
	}

	// Subclass keys aren't anything real.. it's just for this function, and in sites.json
	// Whats the proper way to upgrade to a subclass when you dont know the subclass ahead of time??
	// Im making a method in the parent class, and it gets a string... thoughts?
	// Or maybe a trait... write now it's a function in the aggregator service
    // ^ this is solved by resolver/factory methods
	public function upgradeToSubClass($sub_class_key) {
		$sub_classes_names = [
			'wordpress'
		];

		// TODO (low prioerity)
	}

	// 'Hacks' are applied to the data sequentially
	public function applyRawDataHacks($hack_function_names, $raw_data) {
		if (!is_null($hack_function_names)) {
			foreach ($hack_function_names as $hack_name) {
				$full_hack_name = $hack_name.'_raw_data_hack';
				$raw_data = $this->$full_hack_name($raw_data); // LOL this line
			}
		}

		return $raw_data;
	}

	// 'Hacks' are applied to the data sequentially
	public function applyPostObjectHacks($hack_function_names, $post_objects) {
		if (!is_null($hack_function_names)) {
			foreach ($hack_function_names as $hack_name) {
				$full_hack_name = $hack_name.'_post_object_hack';
				$post_objects = $this->$full_hack_name($post_objects); // LOL this line
			}
		}

		return $post_objects;
	}

    /**
     * Based on site_info
     * @param array $posts
     * @return array
     */
	function setContributor(array $posts)
    {
        foreach ($posts as $post) {
            $post->contributor = $this->site_info->name;
        }
        return $posts;
    }
}
