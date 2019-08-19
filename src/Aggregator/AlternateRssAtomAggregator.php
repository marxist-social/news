<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class AlternateRssAtomAggregator extends Aggregator {
	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);

		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';
		$api_url .= 'index.php?format=feed&type=rss';

		$raw_data = file_get_contents($api_url, false, $context);
		return $raw_data;
	}
	
	public function parseRawDataIntoPosts($raw_data) {
		$parsed_rss_posts = [];
		$rss_simple_xml = simplexml_load_string($raw_data);

		foreach ($rss_simple_xml->channel->item as $simple_xml_post) {
			array_push($parsed_rss_posts, new Post([
				'title' => (string) $simple_xml_post->title,
				'author' => (string) $simple_xml_post->author,
				'post_date' => (string) $simple_xml_post->pubDate,
				'category' => (string) $simple_xml_post->category,
				'blurb' => (string) $simple_xml_post->description,
				'link' => (string) $simple_xml_post->link
			]));
		}

		return $parsed_rss_posts;
	}
}
