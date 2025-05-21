<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class WordPressApiAggregator extends Aggregator {
	const API_PATH = "wp-json/wp/v2/posts";
	const API_QUERY = [
		"_embed" => ["wp:term"],
		"_fields" => [
			"_links",
			"_embedded",
			"date",
			"slug",
			"link",
			"title",
			"excerpt",
			"author",
			"yoast_head_json.twitter_misc",
			"acf.author"
		]
	];
	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);
		$raw_data = file_get_contents($this->constructApiUrl(), false, $context);
		return $raw_data;
	}

	public function parseRawDataIntoPosts($raw_data) {
		$parsed_posts = [];
		$unparsed_posts = json_decode($raw_data);
		foreach ($unparsed_posts as $up) {
			$categories = [];
			foreach ($up->_embedded->{"wp:term"}[0] as $category)
				array_push($categories, $category->name);

			$tags = [];
			foreach ($up->_embedded->{"wp:term"}[1] as $tag)
				array_push($tags, $tag->name);

			$author = $this->getAuthor($up);

			array_push($parsed_posts, new Post([
				'title' => $up->title->rendered,
				'author' => $author,
				'post_date' => $up->date,
				'categories' => $categories,
				'tags' => $tags,
				'blurb' => $up->excerpt->rendered,
				'link' => $up->link
			]));
		}

		return $parsed_posts;
	}

	private function constructApiUrl(): string {
		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';
		$api_url .= self::API_PATH.'?';
		foreach (self::API_QUERY as $k => $kv) {
			$api_url .= $k.'=';
			foreach ($kv as $v) // lol. Sorry future me. Its foolproof prommy.
				$api_url .= $v.',';
			$api_url = rtrim($api_url, ',');
			$api_url .= '&';
		}
		return rtrim($api_url, '&');
	}

	private function getAuthor($unparsed_post) {
		if (property_exists($unparsed_post, "yoast_head_json"))
			return $unparsed_post->yoast_head_json->twitter_misc->{"Written by"};
		else if (property_exists($unparsed_post, "acf"))
			return $unparsed_post->acf->author;
		else
			throw new Exception("Can't parse an author in getAuthor for WordPress");
	}
}
