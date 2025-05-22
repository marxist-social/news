<?php

// Set timezone...
date_default_timezone_set("America/Toronto");

// Autoload our classes
require __DIR__ . '/vendor/autoload.php';

// Load the sites and application settings into memory from config/"db"
$imt_seed = new \MarxistSocialNews\DatabaseSeed\RciDatabaseSeed(); // or EmptySeed, SeedFromArray, SeedFromFile, etc...
$db = new \MarxistSocialNews\DatabaseProcessor\JsonDatabaseProcessor(__DIR__.'/db', $imt_seed);

// Create the rss page view
$view = new \MarxistSocialNews\View\Rss($db, [
    'title' => 'International Marxist Tendency News Aggregator',
    'subtitle' => 'This RSS feed contains recent articles from sections of the IMT. It is ordered chronologically',
    'vc_link' => 'https://github.com/marxist-social/news',
    'link' => 'https://'.$_SERVER['SERVER_NAME'].strtok($_SERVER["REQUEST_URI"], '?')
]);
header('Content-Type: application/atom+xml; charset=utf-8');
echo $view->render();


