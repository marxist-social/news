<?php
namespace MarxistSocialNews\View;
use MarxistSocialNews\View;

class Rss extends View {
    public function constructTemplate() {
        $str = <<<TEMPLATE
			<feed xmlns="http://www.w3.org/2005/Atom">
				<title>{$this->user_properties['title']}</title>
				<link rel="self" href="{$this->user_properties['link']}"/>
				<id>{$this->user_properties['link']}</id>
				<updated>%recent_article_date%</updated>
				<subtitle>{$this->user_properties['subtitle']}</subtitle>
				<generator uri="{$this->user_properties['vc_link']}">Marxist Social News Aggregator</generator>
				%entries%
			</feed>
		TEMPLATE;
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?" . ">\n" . $str; // prepend cause question mark greater than is too scary for my IDE
    }

    /**
     * Take this view and render it to an HTML string
     */
    public function render() {
        // Load the sites and application settings into memory from config/"db"
        $this->db->loadTableIntoMemory('sites');
        $all_rss_entries_string = '';
        $latest_date = 0;
        $posts = [];
        $aggregator_post_rss = ""; // Get individual post html
        foreach ($this->db->tables['sites'] as $site) {
            if (!in_array('homepage', $site->services)) // change to rss one day but for now its the same
                continue;
            $site_posts_table_name = 'article_cache/'.$site->slug;
            if ($this->db->tableExistsInDatabase($site_posts_table_name)) {
                $this->db->loadTableIntoMemory($site_posts_table_name);
                foreach ($this->db->tables[$site_posts_table_name] as $post_array) {
                    $post = new \MarxistSocialNews\Post($post_array);
                    if (is_null($post->contributor))
                        $post->contributor = $site->name;
                    if (strtotime($post->post_date) > $latest_date)
                        $latest_date = strtotime($post->post_date);
                    array_push($posts, $post);
                }
                $this->db->removeTableFromMemory($site_posts_table_name);
            }
        }
        // Sort posts chronologically
        usort($posts, function($a, $b) { return strtotime($a->post_date) < strtotime($b->post_date) ? 1 : -1; });
        foreach ($posts as $post) {
            $aggregator_post_rss .= $post->getRss() . "\n";
        }
        unset($posts);
        $all_rss_entries_string .= $aggregator_post_rss;

        $template = str_replace('%entries%', $all_rss_entries_string, $this->template);
        $template = str_replace('%recent_article_date%', date('Y-m-d\TH:i:sP', $latest_date), $template);

        // Perform SIMPLE replacements (move this into the parent logic...)
        $template = $this->performSimpleReplacements($template, $this->user_properties);

        return $template;
    }
}
