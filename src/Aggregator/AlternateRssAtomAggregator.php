<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class AlternateRssAtomAggregator extends Aggregator {
	private $api_url;

	public function retrieveRawDataFromSite() {
		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';

		if (!is_null($this->site_info->flags) && in_array('rss-home', $this->site_info->flags))
			$api_url .= 'rss-home?format=feed&type=rss';
		else
			$api_url .= 'index.php?format=feed&type=rss';

		if (!is_null($this->site_info->flags) && in_array('use-curl', $this->site_info->flags)) {
			// set URL and other appropriate optionsm
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'User-Agent: '.$this->user_agent
			));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$raw_data = curl_exec($ch);

			if(curl_errno($ch)) {
				$error_str = "CURL error when requesting aggregate alternate rss atom for ".$api_url.": ".curl_error($ch);
				curl_close($ch);
				throw new Exception($error_str);
			}

			curl_close($ch);
		} else {
			$context = stream_context_create(["http" => [
				"header" => "User-Agent: {$this->user_agent}"
			]]);
			$raw_data = file_get_contents($api_url, false, $context);	
		}
		
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
