<?php
namespace ImtRssAggregator;

class RssAggregator {

	private $user_agent = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36";
	private $site_info;

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

		return file_get_contents($this->site_info->url, false, $context);
	}

	public function getName() {
		return $this->site_info->name;
	}

	public function getCountry() {
		return $this->site_info->country;
	}

	public function getProvince() {
		return $this->site_info->province;
	}
}
