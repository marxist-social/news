<?php
namespace ImtRssAggregator\RssAggregators;
use ImtRssAggregator\RssAggregator;
use ImtRssAggregator\Post;
use Exception;

class FightbackRssAggregator extends RssAggregator {
	public function parseRssIntoPosts($raw_rss_data) {
		$parsed_rss_posts = [];
		$rss_simple_xml = simplexml_load_string($raw_rss_data);

		foreach ($rss_simple_xml->entry as $simple_xml_post) {

			// rewrite the link to remove "admin" from url beginning, and the date (just leave stripped url + slug)
			$link = (string) $simple_xml_post->link['href'];
			$link = str_replace('https://admin.', 'https://', $link); // remove admin
			$link_parts = explode('/', $link);
			$link = 'https://'.$link_parts[2].'/article/'.$link_parts[6]; // remove date


			array_push($parsed_rss_posts, new Post([
				'title' => (string) $simple_xml_post->title,
				'author' => (string) $simple_xml_post->author->name,
				'post_date' => (string) $simple_xml_post->published,
				'category' => (string) $simple_xml_post->category[count($simple_xml_post->category) - 1]['term'],
				'blurb' => (string) $simple_xml_post->summary,
				'link' => $link
			]));
		}

		return $parsed_rss_posts;
	}
}
