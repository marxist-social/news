<?php
namespace ImtRssAggregator\RssAggregator;
use ImtRssAggregator\RssAggregator;
use ImtRssAggregator\Post;
use Exception;

class WordPressRssAggregator extends RssAggregator {
	public function parseRssIntoPosts($raw_rss_data) {
		$parsed_rss_posts = [];

		/* Hack to allow & sign in the icon url (?) otherwise invalid xml. Needed for ls-pk feed */
		$hacked_rss_data = str_replace('<icon>', '<icon><![CDATA[', $raw_rss_data);
		$hacked_rss_data = str_replace('</icon>', ']]></icon>', $raw_rss_data);

		$rss_simple_xml = simplexml_load_string($hacked_rss_data);

		foreach ($rss_simple_xml->entry as $simple_xml_post) {
			array_push($parsed_rss_posts, new Post([
				'title' => (string) $simple_xml_post->title,
				'author' => (string) $simple_xml_post->author->name,
				'post_date' => (string) $simple_xml_post->published,
				'category' => (string) $simple_xml_post->category[count($simple_xml_post->category) - 1]['term'],
				'blurb' => (string) $simple_xml_post->summary,
				'link' => (string) $simple_xml_post->link['href']
			]));
		}

		return $parsed_rss_posts;
	}

	public function lal_salaam_raw_data_hack($raw_data) {
		// TODO!! lol rip.
		// Probably have to use API. Will disable lal salaam for now.
	}
}
