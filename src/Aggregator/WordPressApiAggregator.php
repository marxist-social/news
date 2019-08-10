<?php
namespace MarxistSocialNews\Aggregator;
use MarxistSocialNews\Aggregator;
use MarxistSocialNews\Post;
use Exception;

class WordPressApiAggregator extends Aggregator {
	public function retrieveRawDataFromSite() {
		$context = stream_context_create(["http" => [
			"header" => "User-Agent: {$this->user_agent}"
		]]);

		$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';

		if (!is_null($this->site_info->flags) && in_array('uses-no-api-prefix', $this->site_info->flags)) // For the US section...
			$api_url .= 'posts?_embed=1&page=1';
		else
			$api_url .= 'wp-json/wp/v2/posts?_embed=1&page=1';

		$raw_data = file_get_contents($api_url, false, $context);
		return $raw_data;
	}

	public function parseRawDataIntoPosts($raw_data) {
		$parsed_posts = [];
		$unparsed_posts = json_decode($raw_data);

		$unparsed_posts = $this->replaceAuthorIdWithEmbedAuthor($unparsed_posts);
		$unparsed_posts = $this->replaceCategoryIdsWithStrings($unparsed_posts);

		foreach ($unparsed_posts as $up) {
			
			$category_string = "";
			foreach ($up->categories as $category) {
				$category_string .= $category.', ';
			}
			$category_string = trim($category_string, ', ');

			array_push($parsed_posts, new Post([
				'title' => (string) $up->title->rendered,
				'author' => (string) $up->author,
				'post_date' => (string) $up->date,
				'category' => (string) $category_string,
				'blurb' => (string) $up->excerpt->rendered,
				'link' => (string) $up->link
			]));
		}

		return $parsed_posts;
	}

	private function replaceCategoryIdsWithStrings($decoded_posts) {
		// Maybe they came with names? and we don't have to query the API?
		if (is_object($decoded_posts[0]->categories[0]) && property_exists($decoded_posts[0]->categories[0], 'name')) {
			foreach ($decoded_posts as $index => $post)
				foreach ($post->categories as $cat_index => $category)
					$decoded_posts[$index]->categories[$cat_index] = $category->name; // flat overwrite of the object with the string
		} else {
			// Query the categories API
			$context = stream_context_create(["http" => [
				"header" => "User-Agent: {$this->user_agent}"
			]]);

			$api_url = (substr($this->site_info->url, -1) === '/') ? $this->site_info->url : $this->site_info->url.'/';
			$api_url .= 'wp-json/wp/v2/categories?per_page=100';
			$raw_data = file_get_contents($api_url, false, $context);
			$decoded_categories = json_decode($raw_data);

			// Index the decoded categories
			$indexed_categories = [];
			foreach ($decoded_categories as $index => $category)
				$indexed_categories[$category->id] = $category->name;

			foreach ($decoded_posts as $index => $post)
				foreach ($post->categories as $cat_index => $category)
					$decoded_posts[$index]->categories[$cat_index] = $indexed_categories[$category];
		}

		return $decoded_posts;
	}


	private function replaceAuthorIdWithEmbedAuthor($decoded_posts) {
		foreach($decoded_posts as $index => $post) { // Should NOT overwrite real authors hacked in.
			if (is_int($post->author) && property_exists($post, '_embedded')) {
				$decoded_posts[$index]->author = $post->_embedded->author[0]->name;
			}
		}

		return $decoded_posts;
	}

	/** Hacks! **/
	public function fightback_raw_data_hack($raw_data) { // Runs before parseRawDataIntoPosts
		$decoded_json = json_decode($raw_data);

		foreach ($decoded_json as $index => $post) {
			$decoded_json[$index]->author = $post->acf->author; // Set the author

			// rewrite the link to remove "admin" from url beginning, and the date (just leave stripped url + slug)
			$link = $post->link;
			$link = str_replace('https://admin.', 'https://', $link); // remove admin
			$link_parts = explode('/', $link);
			$link = 'https://'.$link_parts[2].'/article/'.$link_parts[6]; // remove date
			$decoded_json[$index]->link = $link;
		}

		return json_encode($decoded_json);
	}

	public function socialist_revolution_raw_data_hack($raw_data) {
		$decoded_json = json_decode($raw_data);

		foreach ($decoded_json as $index => $post) {
			$author_string = ""; // Author is in an array, turn it into a string

			if (!is_array($post->acf->imt_author)) {
				var_dump($post->acf->imt_author);
				die();
			}
			

			foreach ($post->acf->imt_author as $imt_author)
				$author_string .= $imt_author.', ';
			$author_string = trim($author_string, ', ');

			$decoded_json[$index]->author = $author_string; // Set the author

			// rewrite the link to remove "admin" from url beginning, and the date (just leave stripped url + slug)
			$link = $post->link;
			$link = str_replace('https://api.', 'https://', $link); // remove 'api'
			$decoded_json[$index]->link = $link;
		}

		return json_encode($decoded_json);
	}
}
