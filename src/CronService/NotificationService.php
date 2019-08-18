<?php
namespace MarxistSocialNews\CronService;
use MarxistSocialNews\CronService;

class NotificationService extends CronService {

	function run() {
		// See if anyone needs their mail.. (TODO: only consider those in notify service)
		$db = new \MarxistSocialNews\DatabaseProcessor\JsonDatabaseProcessor($this->db_config['path']);
		$db->loadTableIntoMemory('mailing_list');

		$count_people_notified = 0;
		foreach($db->tables['mailing_list'] as $mailing_recipient_array) {
			$notifier = new \MarxistSocialNews\Notifier($db, $mailing_recipient_array, $this->previous_service_history['aggregate']['oldest_site']);

			if ($notifier->shouldFire()) {
				$notifier->fire();
				$count_people_notified++;
			}
		}

		return ['output' => $count_people_notified." users received notifications."];
	}
}

