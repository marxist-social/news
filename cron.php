<?php

// Set timezone...
date_default_timezone_set("America/Toronto");

// Autoload our classes
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

// Run the services
$services_to_run = [
	'aggregate' => \MarxistSocialNews\CronService\AggregatorService::class, 
	'notify' => \MarxistSocialNews\CronService\NotificationService::class,
	'reddit' => \MarxistSocialNews\CronService\RedditService::class
];

$previous_service_data = [];
$imt_seed = new \MarxistSocialNews\DatabaseSeed\ImtDatabaseSeed(); // or EmptySeed, SeedFromArray, SeedFromFile, etc...
foreach ($services_to_run as $service_name => $service_class) {
	$service = new $service_class([
		'previous_service_history' => $previous_service_data, 
		'db_config' => ['path' => __DIR__.'/db', 'seed' => $imt_seed],
		'reddit_config' => [
			'user' => getenv('REDDIT_USER'), 
			'pass' => getenv('REDDIT_PASS'), 
			'client' => getenv('REDDIT_CLIENT_ID'), 
			'secret' => getenv('REDDIT_SECRET')
		]
	]);

	$previous_service_data[$service_name] = $service->run();
}


// Done -> output 'output' key of prev_serv_data
echo "\n";
echo "[".date('Y-m-d H:i:s')."] Ran ".count($previous_service_data)." services.\n";
foreach ($previous_service_data as $service_name => $prev_serv_data) {
	 echo " ---> {$service_name}: {$prev_serv_data['output']}\n";
}
echo "\n";

