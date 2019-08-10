<?php

// Set timezone...
date_default_timezone_set("America/Toronto");

// Autoload our classes
require __DIR__ . '/vendor/autoload.php';

// Load the sites and application settings into memory from config/"db"
$db = new \MarxistSocialNews\DatabaseProcessor\JsonDatabaseProcessor(__DIR__.'/db');

// Create the home page view
$view = new \MarxistSocialNews\View\Homepage($db);
echo $view->render(); 


