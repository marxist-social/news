<?php
namespace ImtRssAggregator\View;
use ImtRssAggregator\View;

class Homepage extends View {
	public function constructTemplate() {
		return <<<TEMPLATE
			<html>
				<head>
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta charset="UTF-8">

					<meta property="og:title" content="IMT RSS Aggregator" />
					<meta property="og:type" content="website" />
					<meta property="og:image" content="{$this->base_url}/img/imt-logo.jpg" />
					<meta property="og:description" content="This aggregator displays the latest articles from Fightback, La Riposte, Esquerda Marxista, and Socialist appeal." /> 

					<link rel="stylesheet" type="text/css" href="{$this->base_url}/css/imtrss.css">
					<link rel="stylesheet" type="text/css" href="{$this->base_url}/css/mobile-imtrss.css">
					<title>IMT RSS Aggregator</title>
					<link rel="icon" type="image/png" href="{$this->base_url}/img/favicon.png" />
				</head>
				<body class="home__body">
					<h1 class="home__title">Welcome to the IMT RSS aggregator!</h1>
					<p class="home__meta">This page contains a list of IMT sections along with the six latest posts from their website.</p>
					<p class="home__meta">This project is a work in progress. To contribute or report bugs, please visit <a href="https://github.com/junipermcintyre/imt-rss-aggregator" target="_blank">https://github.com/junipermcintyre/imt-rss-aggregator</a>.</p>
					<hr  style="margin-top: 3rem;"/>
					<div class="home__aggregators">
						%aggregators%
					</div>
					<hr />
					<p class="home__end">End of IMT RSS Aggregator</p>
					<p class="home__footer_meta">Visit our international website at <a href="https://marxist.com/" target="_blank">https://marxist.com/</a>.</p>
				</body>
			</html>
		TEMPLATE;
	}

	/**
	 * Take this view and render it to an HTML string
	 */
	public function render() {
		// Load the sites and application settings into memory from config/"db"
		$this->db->loadTableIntoMemory('sites');


		$aggregators_html = '';
		foreach ($this->db->tables['sites'] as $site) {
			$aggregator_post_html = ""; // Get individual post html
			$site_posts_table_name = 'article_cache/'.$site->slug;
			$this->db->loadTableIntoMemory($site_posts_table_name);
			foreach ($this->db->tables[$site_posts_table_name] as $post_array) {
				$post = new \ImtRssAggregator\Post($post_array);
				$aggregator_post_html .= $post->getHtml();
			}
			$this->db->removeTableFromMemory($site_posts_table_name);

			$province = null; // Get HTML for the whole site (insert individual posts in it)
			if (!is_null($site->province))
				$province = ', '.$site->province;
			$last_cached_date = date('l F jS \a\t H:i T', $site->last_cached);
			$aggregators_html .= <<<TEMPLATE
				<hr />
				<div class="aggregator">
					<h2 class="aggregator__title">{$site->name} 
						<small>{$site->country}{$province}</small>
					</h2>
					<p class="aggregator__meta">Cached {$last_cached_date}</p>
					<div class="aggregator__latest_posts">
						{$aggregator_post_html}
					</div>
				</div>
			TEMPLATE;
		}

		$template = str_replace('%aggregators%', $aggregators_html, $this->template);

		return $template;
	}
}
