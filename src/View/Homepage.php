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
					<div class="home__stickyflex">
						<div class="home__contents">
							%contents%
						</div>
						<div class="home__aggregators">
							<ul>
								%aggregators%
							</ul>
						</div>
					</div>
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
		$indexed_sites = [];
		foreach ($this->db->tables['sites'] as $site) {
		    if (!in_array('homepage', $site->services))
		        continue;
			$site_posts_table_name = 'article_cache/'.$site->slug;
			if ($this->db->tableExistsInDatabase($site_posts_table_name)) {

				$aggregator_post_html = ""; // Get individual post html
				$this->db->loadTableIntoMemory($site_posts_table_name);
				foreach ($this->db->tables[$site_posts_table_name] as $post_array) {
					$post = new \MarxistSocialNews\Post($post_array);

                    if (is_null($post->contributor))
                        $post->contributor = $site->name;

					$aggregator_post_html .= $post->getShortHtml();
				}
				$this->db->removeTableFromMemory($site_posts_table_name);

				$aggregators_html .= $this->makeAggregatorHtml(['site' => $site, 'post_html' => $aggregator_post_html]);
			}

			$letter = strtolower($site->country[0]);
			if (!isset($indexed_sites[$letter])) {
			    $indexed_sites[$letter] = [];
            }
			array_push($indexed_sites[$letter], $site);
		}

		$content_html = "<ul class='aggregator__contents'>";
		ksort($indexed_sites);
		foreach ($indexed_sites as $letter => $sites) {
            $content_html .= "<li>".strtoupper($letter)."<ul>";
            foreach ($sites as $site) {
                $content_html .= "<li><strong>".$site->country."</strong> | <a href='#".$site->slug."'>".$site->name."</a></li>";
            }
            $content_html .= "</ul></li>";
        }
		$content_html .= "</ul>";

		$template = str_replace('%aggregators%', $aggregators_html, $this->template);
        $template = str_replace('%contents%', $content_html, $template);

		// Perform SIMPLE replacements
		$template = $this->performSimpleReplacements($template, $this->user_properties);

		return $template;
	}

	private function makeAggregatorHtml($args) {
		$province = null; // Get HTML for the whole site (insert individual posts in it)
		if (!is_null($args['site']->province))
			$province = ', '.$args['site']->province;
		$last_cached_date = date('l F jS \a\t H:i T', $args['site']->last_cached);

		$url = !is_null($args['site']->friendly_url) ? $args['site']->friendly_url : $args['site']->url;

		return <<<TEMPLATE
			<li class="aggregator">
				<h2 class="aggregator__title" id="{$args['site']->slug}">{$args['site']->country}, {$args['site']->name} 
				</h2>
				<p class="aggregator__link"><em><a href="{$args['site']->url}" target="_blank">{$url}</a></em></p>
				<!--<p class="aggregator__meta">Cached {$last_cached_date}</p>-->
				<ul class="aggregator__latest_posts">
					{$args['post_html']}
				</ul>
			</li>
		TEMPLATE;
	}
}
