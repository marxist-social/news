<?php
namespace MarxistSocialNews\CronService;
use MarxistSocialNews\CronService;
use Exception;

class RedditService extends CronService {

	public $reddit_config = [];
	public $user_agent = "Marxist.Social News Aggregator / news.marxist.social";

	public function run() {
		$oldest_site = $this->previous_service_history['aggregate']['oldest_site'];

		// Does the oldest site have reddit enabled?
		if (!in_array('reddit', $oldest_site->services))
			return ['output' => "Reddit service not enabled."];
		elseif (empty($oldest_site->subreddits) || is_null($oldest_site->subreddits))
			return ['output' => "Reddit service is enabled, but no subreddits are specified."];

		// Are there any new articles?
		$new_post_indexes = $this->previous_service_history['aggregate']['new_post_indexes'];
		if (empty($new_post_indexes))
			return ['output' => "Reddit service is enabled, and subreddits are specified, but there are no new posts."];



		$oldest_site_table_name = 'article_cache/'.$oldest_site->slug;
		$db = $this->connectToDatabase([
			'path' => $this->db_config['path'], 
			'seed' => $this->db_config['seed'], 
			'tables' => [$oldest_site_table_name, 'app_settings', 'app_status']
		]);

		$this->initiateReddit($db); // Initial oAuth request, get bearer token
		$new_posts = $this->getPostsFromIndexes($db->tables[$oldest_site_table_name], $new_post_indexes);

		foreach ($new_posts as $np) {
			foreach ($oldest_site->subreddits as $sr) {
				$this->postLinkToSubreddit($np, $sr, $oldest_site);
			}

			// Just do one for now... let's not be an annoying bot.
			break;
		}
		return [
			'output' => "Posted ".count($new_posts)." articles for ".$oldest_site->name." to ".count($oldest_site->subreddits)." subreddits at ".date("d M Y H:i:s", $oldest_site->last_cached)."."
		];
	}

	private function connectToDatabase($config) {
		$db = new \MarxistSocialNews\DatabaseProcessor\JsonDatabaseProcessor($config['path'], $config['seed']);
		$db->loadTablesIntoMemory($config['tables']);

		return $db;
	}


	private function getPostsFromIndexes($db_posts, $new_indexes) {
		$new_posts = [];

		foreach ($db_posts as $post_array) {
			if (in_array($post_array->index, $new_indexes)) {
				$post = new \MarxistSocialNews\Post($post_array);
				array_push($new_posts, $post);
			}
		}

		return $new_posts;
	}


	private function initiateReddit() {
		$ch = curl_init();

		// set URL and other appropriate optionsm
		curl_setopt($ch, CURLOPT_URL, "https://www.reddit.com/api/v1/access_token");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->reddit_config['client'] . ":" . $this->reddit_config['secret']); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent: '.$this->user_agent
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'grant_type' => 'password',
			'username' => $this->reddit_config['user'],
			'password' => $this->reddit_config['pass'],
		]);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($ch));

		if(curl_errno($ch)) {
			$error_str = "CURL error when initiating reddit: ".curl_error($ch);
			curl_close($ch);
			throw new Exception($error_str);
		}
		curl_close($ch);

		$this->reddit_config['bearer'] = $response->access_token;
	}

	private function postLinkToSubreddit($post, $subreddit, $site) {
		$title_prefix = "[News][".$site->name."] ";


		$ch = curl_init();

		// set URL and other appropriate optionsm
		curl_setopt($ch, CURLOPT_URL, "https://oauth.reddit.com/api/submit");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent: '.$this->user_agent,
			'Authorization: bearer '.$this->reddit_config['bearer']
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'kind' => 'link',
			'resubmit' => false, // Let's not be obnoxious by mistake
			'sr' => $subreddit,
			'title' => $title_prefix.$this->cleanTitleUp($post->title),
			'url' => $post->link
		]);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($ch));

		if(curl_errno($ch)) {
			$error_str = "CURL error when initiating reddit: ".curl_error($ch);
			curl_close($ch);
			throw new Exception($error_str);
		}
		curl_close($ch);

		// See what our response was like
		var_dump($response);
	}

	private function cleanTitleUp($title) {
		$replacements = [
			'&#8211;' => "–",
			'&#8216;' => "‘",
			'&#8217;' => "’",
			'&#8220;' => '“',
			'&#8221;' => '”'
		];

		foreach ($replacements as $search => $replace) {
			$title = str_replace($search, $replace, $title);
		}

		return $title;
	}

	private function output($str) {
		echo "      ".$str;
	}
}
