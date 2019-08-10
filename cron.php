<?php

// Set timezone...
date_default_timezone_set("America/Toronto");

// Autoload our classes
require __DIR__ . '/vendor/autoload.php';


// Run the services
$services_to_run = [
	'aggregator' => \ImtRssAggregator\CronService\AggregatorService::class, 
	'notification' => \ImtRssAggregator\CronService\NotificationService::class
];


$previous_service_data = [];
foreach ($services_to_run as $service_name => $service_class) {
	$service = new $service_class([
		'previous_service_history' => $previous_service_data, 
		'db_path' => __DIR__.'/db'
	]);

	$previous_service_data[$service_name] = $service->run();
}


// Done -> output 'output' key of prev_serv_data
echo "[".date('Y-m-d H:i:s')."] Ran ".count($previous_service_data)." services.\n";
foreach ($previous_service_data as $service_name => $prev_serv_data) {
	 echo " ---> {$service_name}: {$prev_serv_data['output']}\n";
}

