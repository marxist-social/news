<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class WordPressApiAggregator extends Aggregator {
	const API_PATH = "wp-json/wp/v2/posts";
	const API_QUERY = [
		"_embed" => ["wp:term", "author"],
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

			if (!in_array("skip-author", $this->site_info->flags))
				$author = $this->getAuthor($up);
			else
				$author = "Unknown";

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

	// Why is this like 75% of the work...
	private function getAuthor($unparsed_post) {
		$author = null;
		$author_method = null;
		if (property_exists($unparsed_post, "yoast_head_json") && property_exists($unparsed_post->yoast_head_json, "twitter_misc")) {
			if (property_exists($unparsed_post->yoast_head_json->twitter_misc, "Written by")) {
				$author = $unparsed_post->yoast_head_json->twitter_misc->{"Written by"};
				$author_method = "yoast-written-by";
			} else if (property_exists($unparsed_post->yoast_head_json->twitter_misc, "Escrito por")) {
				$author = $unparsed_post->yoast_head_json->twitter_misc->{"Escrito por"};
				$author_method = "yoast-escito-por";
			}
		} else if (property_exists($unparsed_post, "acf")) {
			$author = $unparsed_post->acf->author;
			$author_method = "advanced custom fields";
		} else if (isset($unparsed_post->_embedded->{"wp:term"}[2]) && property_exists($unparsed_post->_embedded->{"wp:term"}[2][0], "name")) {
			$author = $unparsed_post->_embedded->{"wp:term"}[2][0]->name;
			$author_method = "wp term";
		} else if (property_exists($unparsed_post->_embedded, "author") && property_exists($unparsed_post->_embedded->author[0], "name")) {
			$author = $unparsed_post->_embedded->author[0]->name;
			$author_method = "author field";
		} else {
			throw new Exception("Can't parse an author in getAuthor for WordPress");
		}

		if (is_null($author))
			throw new Exception("Author is null. Method is ".$author_method);

		return $author;
	}
}
