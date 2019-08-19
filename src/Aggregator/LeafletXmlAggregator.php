<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class LeafletXmlAggregator extends Aggregator {
	private $api_url;

	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);
		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';
		$this->api_url = $api_url;
		$raw_data = file_get_contents($api_url, false, $context);
		return $raw_data;
	}
	
	public function parseRawDataIntoPosts($raw_data) {
		$parsed_rss_posts = [];
		$rss_simple_xml = simplexml_load_string($raw_data);

		foreach ($rss_simple_xml->leaflet as $simple_xml_post) {
			
			$content = '<p>';
			if (isset($simple_xml_post->illustrations->media['preview'])) {
				$filePath = (string) $simple_xml_post->illustrations->media['preview'];
				$filePath = substr($filePath, 5);
				$content .= '<img src="'.$this->api_url.$filePath.'" style="float: left;" />';
			}
			$content .= (string) $simple_xml_post->abstract.'</p>';

			$categoryString = "";
			foreach ($simple_xml_post->keywords->w as $keyword)
				$categoryString .= (string) $keyword.', ';
			$categoryString = trim($categoryString, ', ');

			array_push($parsed_rss_posts, new Post([
				'title' => (string) $simple_xml_post->title,
				'author' => 'unknown',
				'post_date' => (string) $simple_xml_post['date'],
				'category' => $categoryString,
				'blurb' => $content,
				'link' => $this->api_url.'XML/'. (string) $simple_xml_post['aref']
			]));
		}

		return $parsed_rss_posts;
	}
}
