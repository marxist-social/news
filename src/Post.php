<?php

namespace MarxistSocialNews;

class Post {
	public $title;
	public $author;
	public $post_date;
	public $category;
	public $blurb;
	public $link;
	public $contributor;
	public $index;

	function __construct($config) {
	    $backup_array = [];
		foreach($config as $config_key => $config_val) {
			$this->{$config_key} = $config_val;
			$backup_array[$config_key] = $config_val;
		}
		if (!isset($backup_array['contributor']))
		    $this->contributor = null; // default since its new, and some might not have it in prod
	}

    /**
     * Represent a post as HTML
     * @return string
     */
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

    /**
     * Represent a post as an RSS atom entry
     * @return string
     */
    public function getRss() {
        $date = date('Y-m-d\TH:i:sP', strtotime($this->post_date)); // dirty date format
        $escaped_blurb = htmlspecialchars($this->blurb, ENT_XML1);
        $escaped_blurb = preg_replace( "/\r|\n/", "", $escaped_blurb);
        $escaped_title = html_entity_decode($this->title);
        $escaped_link = htmlspecialchars($this->link, ENT_XML1);
        $cub = parse_url($this->link);
        $contributor_uri = $cub['scheme'].'://'.$cub['host'];
        return <<<TEMPLATE
			<entry>
				<title>{$escaped_title}</title>
				<author><name>{$this->author}</name></author>
				<link rel="alternate" type="text/html" href="{$escaped_link}"/>
				<id>{$escaped_link}</id>
				<published>{$date}</published>
				<updated>{$date}</updated>
				<category term="{$this->category}"/>
				<contributor>
					<name>{$this->contributor}</name>
					<uri>{$contributor_uri}</uri>
				</contributor>
				<content type="html"><![CDATA[{$escaped_blurb}]]></content>
			</entry>
		TEMPLATE;
    }
}

