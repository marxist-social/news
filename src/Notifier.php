<?php
namespace MarxistSocialNews;
use Exception;

class Notifier {
	private $allowed_frequencies = ['always', 'hourly', 'daily', 'weekly'];

	public $should_notify;
	public $services;
	public $sites;
	public $frequency;
	public $last_article_processed;
	public $email;
	public $phone_number;
	public $phone_provider;

	public $db;
	public $oldest_site;

	function __construct($db, $mail_recipient, $oldest_site) {
		foreach($mail_recipient as $config_key => $config_val) {
			$this->{$config_key} = $config_val;
		}

		$this->db = $db;
		$this->oldest_site = $oldest_site; 
	}

	public function shouldFire() {
		// Should it fire? (should_notify?) like enabled

		// whats the frequency (always, or catch-ups?)

		// if always..
		//		is the site allowed?
		//			then fire away!!

		// If catch-up...
		//		Has enough time passed?
		//		Do ANY articles in allowed sites exist with index > last_article_processed?
		return false;
	}

	public function fire() {
		if ($this->frequency === 'always')
			$this->sendAlwaysNotification();
		elseif (in_array($this->frequency, $this->allowed_frequencies))
			$this->sendCatchUpNotification();
		else
			throw new Exception("Trying to fire notification for ".$this->email." but frequency type of ".$this->frequency." is unrecognized");
	}

	public function sendAlwaysNotification() {
		throw new Exception("TODO-> implement this function");
	}

	public function sendCatchUpNotification() {
		throw new Exception("TODO-> implement this function");
	}
}

