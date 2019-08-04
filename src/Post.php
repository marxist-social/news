<?php

namespace ImtRssAggregator;

class Post {
	public $title;
	public $author;
	public $post_date;
	public $category;
	public $blurb;
	public $link;

	function __construct($config) {
		foreach($config as $config_key => $config_val) {
			$this->{$config_key} = $config_val;
		}
	}
}

