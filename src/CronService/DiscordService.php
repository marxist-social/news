<?php
namespace MarxistSocialNews\CronService;
use MarxistSocialNews\CronService;
use Exception;

class DiscordService extends CronService {

    public $user_agent = "Marxist.Social News Aggregator / news.marxist.social";
    public $sub_output = [];

    public function run() {
        $oldest_site = $this->previous_service_history['aggregate']['oldest_site'];

        // check there are discords set
        if (empty($oldest_site->discords) || is_null($oldest_site->discords))
            return ['output' => "Discord service is enabled, but no discords are specified."];

        // Are there any new articles?
        $new_post_indexes = $this->previous_service_history['aggregate']['new_post_indexes'];
        if (empty($new_post_indexes))
            return ['output' => "Discord service is enabled, and discords are specified, but there are no new posts."];


        $oldest_site_table_name = 'article_cache/'.$oldest_site->slug;
        $db = $this->connectToDatabase([
            'path' => $this->db_config['path'],
            'seed' => $this->db_config['seed'],
            'tables' => [$oldest_site_table_name, 'app_settings', 'app_status', 'discord_bots']
        ]);

        $new_posts = $this->getPostsFromIndexes($db->tables[$oldest_site_table_name], $new_post_indexes);

        $published_posts = 0;
        foreach ($new_posts as $np) {
            foreach ($oldest_site->discords as $discord_key) {

                // Get the config based on the ID specified
                $discord_config = null;
                foreach ($db->tables['discord_bots'] as $bot_config)
                    if ($bot_config->id === $discord_key)
                        $discord_config = $bot_config;
                if (is_null($discord_config))
                    return ['output' => 'Could not find bot config for bot with ID '.$discord_key];

                if ($this->postLinkToDiscordBot($np, $oldest_site, $discord_config))
                    $published_posts++;
            }

            // >:)
            /*if ($published_posts > 0) {
                $this->output("Stopped after ".$published_posts." were posted.");
                break;
            }*/
        }

        $output = "Posted ".$published_posts." articles successfully (out of "
            .count($new_posts) * count($oldest_site->discords).") for "
            .$oldest_site->name." to ".count($oldest_site->discords)
            ." discords at ".date("d M Y H:i:s", $oldest_site->last_cached).".";

        foreach ($this->sub_output as  $sub_out) {
            $output .= "\n".$sub_out;
        }
        return [
            'output' => $output
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

    private function postLinkToDiscordBot($post, $site, $discord_config) {
        $message = json_encode([
            'passphrase' => $discord_config->passphrase,
            'title' => $this->cleanTitleUp($post->title),
            'link' => $post->link,
            'blurb' => $post->blurb,
            'date' => date('l F jS, Y (h:i A)', strtotime($post->post_date)),
            'author' => $post->author,
            'category' => $post->category,
            'site' => $site->name
        ]);

        $ch = curl_init();

        // set URL and other appropriate optionsm
        curl_setopt($ch, CURLOPT_URL, $discord_config->news_listener_url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: '.$this->user_agent,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if(curl_errno($ch)) {;
            $this->output("CURL error when initiating discord ".$discord_config->name.": ".curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }

    private function cleanTitleUp($title) {
        $replacements = [
            '&#8211;' => "–",
            '&#8216;' => "‘",
            '&#8217;' => "’",
            '&#8220;' => '“',
            '&#8221;' => '”',
            '&rsquo;' => '’',
            '&lsquo;' => '‘'
        ];

        foreach ($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        return $title;
    }

    private function output($str) {
        array_push($this->sub_output, "      ".$str);
    }
}
