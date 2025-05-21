<?php

namespace MarxistSocialNews;

class Post {
	public string $title;
	public string $author;
	public $post_date;
	public array $categories;
	public array $tags;
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
		$category = $this->getCategoryString();
		return <<<TEMPLATE
			<div class="post">
				<h3 class="post__title">{$this->title}</h3>
				<p class="post__meta">{$this->author} | {$date} | {$category}</p>
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
        $escaped = [
            'blurb' => preg_replace( "/\r|\n/", "", htmlspecialchars($this->blurb, ENT_XML1)),
            'link' => htmlspecialchars($this->link, ENT_XML1),
            'title' => htmlspecialchars(html_entity_decode($this->title), ENT_XML1),
            'category' => htmlspecialchars($this->getCategoryString(), ENT_XML1),
            'author' => htmlspecialchars($this->author, ENT_XML1),
            'contributor_uri' => htmlspecialchars(parse_url($this->link)['scheme'].'://'.parse_url($this->link)['host'], ENT_XML1),
            'contributor' => htmlspecialchars($this->contributor, ENT_XML1)
        ];
        return <<<TEMPLATE
			<entry>
				<title>{$escaped['title']}</title>
				<author><name>{$escaped['author']}</name></author>
				<link rel="alternate" type="text/html" href="{$escaped['link']}"/>
				<id>{$escaped['link']}</id>
				<published>{$date}</published>
				<updated>{$date}</updated>
				<category term="{$escaped['category']}"/>
				<contributor>
					<name>{$escaped['contributor']}</name>
					<uri>{$escaped['contributor_uri']}</uri>
				</contributor>
				<content type="html"><![CDATA[{$escaped['blurb']}]]></content>
			</entry>
		TEMPLATE;
    }

    public function getCategoryString() {
    	$category_string = "";
    	foreach ($this->categories as $c)
    		$category_string .= $c.", ";
    	return rtrim($category_string, ", ");
    }
}

