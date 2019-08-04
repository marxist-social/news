<?php
namespace ImtRssAggregator\Views;
use ImtRssAggregator\View;

class Homepage extends View {
	public function constructTemplate() {
		return <<<TEMPLATE
			<html>
				<head>
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta charset="UTF-8">
					<link rel="stylesheet" type="text/css" href="css/imtrss.css">
					<link rel="stylesheet" type="text/css" href="css/mobile-imtrss.css">
					<title>IMT RSS Aggregator</title>
				</head>
				<body class="home__body">
					<h1 class="home__title">Welcome to the IMT RSS aggregator!</h1>
					<p class="home__meta">This page contains a list of IMT sections along with the six latest posts from their website.</p>
					<p class="home__meta">This project is a work in progress. To contribute or report bugs, please visit <a href="https://github.com/junipermcintyre/imt-rss-aggregator" target="_blank">https://github.com/junipermcintyre/imt-rss-aggregator</a>.</p>
					<hr />
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
		// Perform SIMPLE replacements
		$aggregators_html = '';
		foreach ($this->user_properties['aggregators'] as $aggregator) {
			$aggregator_post_html = "";
			foreach ($aggregator->posts as $post) {

				$date = date('l jS \of F Y h:i:s A', strtotime($post->post_date)); // dirty date format

				$aggregator_post_html .= <<<TEMPLATE
					<div class="post">
						<h3 class="post__title">{$post->title}</h3>
						<p class="post__meta">Posted by {$post->author} on {$date} under category {$post->category}</p>
						<div class="post__blurb">
							{$post->blurb}
						</div>
						<p class="post__link"><a href="{$post->link}" target="_blank">Read more</a></p>
					</div>
				TEMPLATE;
			}


			$province = null; // Dirty comma replacement for the locations
			if (!is_null($aggregator->site_info->province))
				$province = ', '.$aggregator->site_info->province;

			$aggregators_html .= <<<TEMPLATE
				<hr />
				<div class="aggregator">
					<h2 class="aggregator__title">{$aggregator->site_info->name} 
						<small>{$aggregator->site_info->country}{$province}</small>
					</h2>
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
