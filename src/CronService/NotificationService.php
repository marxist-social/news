<?php
namespace ImtRssAggregator\CronService;
use ImtRssAggregator\CronService;

class NotificationService extends CronService {

	function run() {
		// See if anyone needs their mail..
		$db = new \ImtRssAggregator\DatabaseProcessor\JsonDatabaseProcessor($this->db_path);
		$db->loadTableIntoMemory('mailing_list');

		$count_people_notified = 0;
		foreach($db->tables['mailing_list'] as $mailing_recipient_array) {
			$notifier = new \ImtRssAggregator\Notifier($db, $mailing_recipient_array, $this->previous_service_history['aggregator']['oldest_site']);

			if ($notifier->shouldFire()) {
				$notifier->fire();
				$count_people_notified++;
			}
		}

		return ['output' => $count_people_notified." users received notifications."];
	}
}

