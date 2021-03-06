<?php
namespace MarxistSocialNews\DatabaseSeed;
use MarxistSocialNews\DatabaseSeed;
use Exception;

class ImtDatabaseSeed extends DatabaseSeed {
	// Just scope out what tables you need. Refernce $->tables afterwards when creating new tables or setting/assuming defaults
	public function constructSeed() {
		return $this->constructSeedFromArray([
			'app_settings' => [
				'cache_limit' => 6
			],
			'app_status' => [
				'articles_processed' => 0,
				'app_started' => date('Y-m-d')
			],
			'mailing_list' => [
				[
					'email' => 'junipermcintyre@gmail.com',
					'should_notify' => true,
					'services' => ['email'],
					'sites' => 'all',
					'frequency' => 'always'
				]
			],
			'sites' => [
				[
					"name" => "IMT Center",
					"slug" => "mx-com",
					"url" => "https://marxist.com/",
					"aggregator_type" => "rss-atom",
					"country" => "International",
					"subreddits" => ['imt']
				], [
					"name" => "Fightback",
					"slug" => "fb-ca",
					"url" => "https://admin.marxist.ca/",
					"aggregator_type" => "wordpress-api",
					"country" => "Canada",
					"raw_data_hacks" => ["fightback"],
					"subreddits" => ['imt']
				], [
					"name" => "La Riposte Socialiste",
					"slug" => "lrs-ca-qc",
					"url" => "https://admin.marxiste.qc.ca/",
					"aggregator_type" => "wordpress-api",
					"country" => "Canada",
					"province" => "Québec",
					"raw_data_hacks" => ["fightback"],
					"subreddits" => ['imt']
				], [
					"name" => "Socialist Revolution",
					"slug" => "sr-org",
					"url" => "https://api.socialistrevolution.org/",
					"aggregator_type" => "wordpress-api",
					"country" => "USA",
					"raw_data_hacks" => ["socialist_revolution"],
					"flags" => ["uses-no-api-prefix"],
					"subreddits" => ['imt']
				], [
					"name" => "Esquerda Marxista",
					"slug" => "em-br",
					"url" => "https://www.marxismo.org.br/",
					"aggregator_type" => "rss-atom",
					"country" => "Brasil",
					"subreddits" => ['imt']
				], [
					"name" => "Socialist Appeal",
					"slug" => "sa-gb",
					"url" => "https://www.socialist.net/",
					"aggregator_type" => "rss-atom",
					"country" => "England",
					"subreddits" => ['imt']
				], [
					"name" => "Lal Salaam",
					"slug" => "ls-pk",
					"url" => "https://www.marxist.pk/",
					"aggregator_type" => "wordpress-api",
					"country" => "Pakistan",
					"subreddits" => ['imt']
				], [
					"name" => "El Militante",
					"slug" => "em-ar",
					"url" => "https://argentina.elmilitante.org/",
					"aggregator_type" => "alternate-rss-atom",
					"country" => "Argentina",
					"subreddits" => ['imt']
				], [
					"name" => "Révolution",
					"slug" => "rv-be",
					"url" => "https://marxiste.be/",
					"aggregator_type" => "alternate-rss-atom",
					"country" => "Belgium",
					"subreddits" => ['imt']
				], [
					"name" => "Враг Капитала",
					"slug" => "bk-ru",
					"url" => "http://www.1917.com/",
					"aggregator_type" => "leaflet-xml",
					"country" => "Russia",
					"subreddits" => ['imt']
				], [
					"name" => "Bloque Popular Juvenil",
					"slug" => "bj-es",
					"url" => "https://bloquepopularjuvenil.org/",
					"aggregator_type" => "wordpress-api",
					"country" => "El Salvador",
					"subreddits" => ['imt']
				], [
					"name" => "Révolution",
					"slug" => "rv-fr",
					"url" => "https://www.marxiste.org/",
					"aggregator_type" => "alternate-rss-atom",
					"flags" => ["rss-home", "use-curl"],
					"country" => "France",
					"subreddits" => ['imt']
				], [
					"name" => "La Izquierda Socialista",
					"slug" => "is-mx",
					"url" => "https://marxismo.mx/",
					"aggregator_type" => "wordpress-api",
					"country" => "Mexico",
					"subreddits" => ['imt']
				]
			],
            'discord_bots' => [
                [
                    'id' => 'botsky',
                    'name' => 'Botsky',
                    'news_listener_url' => 'http://localhost:3000/news',
                    'passphrase' => 'lololololol'
                ]
            ], 'discord_webhooks' => [
                ['todo']
            ]
		]);
	}
}

