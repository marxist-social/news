<?php
namespace MarxistSocialNews\View;
use MarxistSocialNews\View;

class Homepage extends View {
	public function constructTemplate() {
		return <<<TEMPLATE
			<html>
				<head>
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta charset="UTF-8">
					
					<meta property="og:title" content="%title%"/>
					<meta property="og:type" content="website"/>
					<meta property="og:image" content="%logo%"/>
					<meta property="og:description" content="%meta_description%" />
					
					<link rel="stylesheet" type="text/css" href="{$this->base_url}/css/imtrss.css">
					<link rel="stylesheet" type="text/css" href="{$this->base_url}/css/mobile-imtrss.css">
					<title>IMT RSS Aggregator</title>
					<link rel="icon" type="image/png" href="%favicon%"/>
				</head>
				<body class="home__body">
					<h1 class="home__title">%title%</h1>
					%description%
					<hr  style="margin-top: 3rem;"/>
					<div class="home__aggregators">
						%aggregators%
					</div>
					<hr />
					<p class="home__end">End of %title%</p>
					<p class="home__footer_meta">%footer% <a href="%vc_repo%" target="_blank">%vc_repo%</a>.</p>
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
			$site_posts_table_name = 'article_cache/'.$site->slug;
			if ($this->db->tableExistsInDatabase($site_posts_table_name)) {

				$aggregator_post_html = ""; // Get individual post html
				$this->db->loadTableIntoMemory($site_posts_table_name);
				foreach ($this->db->tables[$site_posts_table_name] as $post_array) {
					$post = new \MarxistSocialNews\Post($post_array);
					$aggregator_post_html .= $post->getHtml();
				}
				$this->db->removeTableFromMemory($site_posts_table_name);

				$aggregators_html .= $this->makeAggregatorHtml(['site' => $site, 'post_html' => $aggregator_post_html]);
			}
		}

		$template = str_replace('%aggregators%', $aggregators_html, $this->template);

		// Perform SIMPLE replacements
		$template = $this->performSimpleReplacements($template, $this->user_properties);

		return $template;
	}

	private function makeAggregatorHtml($args) {
		$province = null; // Get HTML for the whole site (insert individual posts in it)
		if (!is_null($args['site']->province))
			$province = ', '.$args['site']->province;
		$last_cached_date = date('l F jS \a\t H:i T', $args['site']->last_cached);

		return <<<TEMPLATE
			<hr />
			<div class="aggregator">
				<h2 class="aggregator__title">{$args['site']->name} 
					<small>{$args['site']->country}{$province}</small>
				</h2>
				<p class="aggregator__meta">Cached {$last_cached_date}</p>
				<div class="aggregator__latest_posts">
					{$args['post_html']}
				</div>
			</div>
		TEMPLATE;
	}
}
