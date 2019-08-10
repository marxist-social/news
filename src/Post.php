<?php

namespace MarxistSocialNews;

class Post {
	public $title;
	public $author;
	public $post_date;
	public $category;
	public $blurb;
	public $link;
	public $index;

	function __construct($config) {
		foreach($config as $config_key => $config_val) {
			$this->{$config_key} = $config_val;
		}
	}

	public function getHtml() {
		$date = date('l F jS, Y (h:i A)', strtotime($this->post_date)); // dirty date format

		return <<<TEMPLATE
			<div class="post">
				<h3 class="post__title">{$this->title}</h3>
				<p class="post__meta" title="Post index is {$this->index}">{$this->author} | {$date} | {$this->category}</p>
				<div class="post__blurb">
					{$this->blurb}
				</div>
				<p class="post__link"><a href="{$this->link}" target="_blank">Read more</a></p>
			</div>
		TEMPLATE;
	}
}

