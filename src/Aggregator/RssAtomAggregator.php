<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class RssAtomAggregator extends Aggregator {
	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);

		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';
		$api_url .= 'feed/atom';

		if (property_exists($this->site_info, "feed_url"))
		    $api_url = $this->site_info->feed_url;

		$raw_data = file_get_contents($api_url, false, $context);
		return $raw_data;
	}

    /**
     * @param $raw_data
     * @return array|void
     */
	public function parseRawDataIntoPosts($raw_data) {
		$parsed_rss_posts = [];
		$rss_simple_xml = simplexml_load_string($raw_data);

		foreach ($rss_simple_xml->entry as $simple_xml_post) {
			$categories = [];
			foreach ($simple_xml_post->category as $category) //TODO HERE
				if (!in_array($category, ["ROOT", "Featured"]))
					array_push($categories, (string) $category);

			array_push($parsed_rss_posts, new Post([
				'title' => (string) $simple_xml_post->title,
				'author' => (string) $simple_xml_post->author->name,
				'post_date' => (string) $simple_xml_post->published,
				'categories' => $categories,
				'blurb' => (string) $simple_xml_post->summary,
				'link' => (string) $simple_xml_post->link['href']
			]));
		}

		return $parsed_rss_posts;
	}
}
