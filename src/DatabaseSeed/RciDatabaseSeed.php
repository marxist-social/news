<?php
namespace MarxistSocialNews\DatabaseSeed;
use MarxistSocialNews\DatabaseSeed;
use Exception;

class RciDatabaseSeed extends DatabaseSeed {
	// Just scope out what tables you need. Refernce $->tables afterwards when creating new tables or setting/assuming defaults
	public function constructSeed(): object {
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
					"name" => "RCI Center",
					"slug" => "mx-com",
					"url" => "https://marxist.com/",
					"aggregator_type" => "rss-atom",
					"continent" => "International",
					"country" => "International",
					"subreddits" => ['rci']
				], [
					"name" => "Revolutionary Communist Party",
					"slug" => "rcp-ca",
					"url" => "https://admin.marxist.ca/",
					"friendly_url" => "https://marxist.ca/",
					"aggregator_type" => "wordpress-api",
					"continent" => "North America",
					"country" => "Canada",
					"subreddits" => ['rci']
				], [
					"name" => "Parti Communiste Révolutionnaire",
					"slug" => "pcr-qc",
					"url" => "https://admin.marxiste.qc.ca/",
					"friendly_url" => "https://marxiste.qc.ca/",
					"aggregator_type" => "wordpress-api",
					"continent" => "North America",
					"country" => "Québec",
					"subreddits" => ['rci']
				], [
					"name" => "Revolutionary Communists of America",
					"slug" => "rca-usa",
					"url" => "https://communistusa.org/",
					"aggregator_type" => "wordpress-api",
					"continent" => "North America",
					"country" => "USA",
					"subreddits" => ['rci']
				], [
					"name" => "Marxist Alternative",
					"slug" => "ma-ni",
					"url" => "https://marxistalternative.org/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Africa",
					"country" => "Nigeria",
					"flags" => ["skip-author"],
					"subreddits" => ['rci']
				], [
					"name" => "Revolution",
					"slug" => "rcsa-sa",
					"url" => "https://marxist.co.za/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Africa",
					"country" => "South Africa",
					"subreddits" => ['rci']
				], [
					"name" => "Communist Struggle",
					"slug" => "rci-in",
					"url" => "https://communiststruggle.com/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Asia",
					"country" => "India",
					"subreddits" => ['rci']
				], [
					"name" => "Ombak Revolusi",
					"slug" => "or-ma",
					"url" => "https://communiststruggle.com/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Asia",
					"country" => "Malaysia ",
					"subreddits" => ['rci']
				], [
					"name" => "Inqalabi Communist Party",
					"slug" => "icp-pk",
					"url" => "https://www.marxist.pk/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Asia",
					"country" => "Pakistan",
					"subreddits" => ['rci']
				], [
					"name" => "The Spark",
					"slug" => "rci-tw",
					"url" => "https://marxist.tw/",
					"aggregator_type" => "wordpress-api",
					"continent" => "Asia",
					"country" => "Taiwan",
					"subreddits" => ['rci']
				], [
					"name" => "Revolución",
					"slug" => "ocm-ar",
					"url" => "https://argentinamilitante.org/",
					"aggregator_type" => "wordpress-api",
					"continent" => "South America",
					"country" => "Argentina",
					"subreddits" => ['rci']
				], [
					"name" => "Organização Comunista Internacionalista",
					"slug" => "oci-br",
					"url" => "https://marxismo.org.br/",
					"aggregator_type" => "wordpress-api",
					"continent" => "South America",
					"country" => "Brazil",
					"subreddits" => ['rci']
				],/*, [
					"name" => "Esquerda Marxista",
					"slug" => "em-br",
					"url" => "https://communiststruggle.com/",
					"aggregator_type" => "rss-atom",
					"country" => "Brasil",
					"subreddits" => ['rci']
				], [
					"name" => "Socialist Appeal",
					"slug" => "sa-gb",
					"url" => "https://www.socialist.net/",
					"aggregator_type" => "rss-atom",
					"country" => "England",
					"subreddits" => ['rci']
				], [
					"name" => "Lal Salaam",
					"slug" => "ls-pk",
					"url" => "https://www.marxist.pk/",
					"aggregator_type" => "wordpress-api",
					"country" => "Pakistan",
					"subreddits" => ['rci']
				], [
					"name" => "El Militante",
					"slug" => "em-ar",
					"url" => "https://argentina.elmilitante.org/",
					"aggregator_type" => "alternate-rss-atom",
					"country" => "Argentina",
					"subreddits" => ['rci']
				], [
					"name" => "Révolution",
					"slug" => "rv-be",
					"url" => "https://marxiste.be/",
					"aggregator_type" => "alternate-rss-atom",
					"country" => "Belgium",
					"subreddits" => ['rci']
				], [
					"name" => "Враг Капитала",
					"slug" => "bk-ru",
					"url" => "http://www.1917.com/",
					"aggregator_type" => "leaflet-xml",
					"country" => "Russia",
					"subreddits" => ['rci']
				], [
					"name" => "Bloque Popular Juvenil",
					"slug" => "bj-es",
					"url" => "https://bloquepopularjuvenil.org/",
					"aggregator_type" => "wordpress-api",
					"country" => "El Salvador",
					"subreddits" => ['rci']
				], [
					"name" => "Révolution",
					"slug" => "rv-fr",
					"url" => "https://www.marxiste.org/",
					"aggregator_type" => "alternate-rss-atom",
					"flags" => ["rss-home", "use-curl"],
					"country" => "France",
					"subreddits" => ['rci']
				], [
					"name" => "La Izquierda Socialista",
					"slug" => "is-mx",
					"url" => "https://marxismo.mx/",
					"aggregator_type" => "wordpress-api",
					"country" => "Mexico",
					"subreddits" => ['rci']
				]*/
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

