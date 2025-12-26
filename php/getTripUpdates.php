<?php
	require_once 'csrf.php';
	require_once 'vendor/autoload.php';
	require_once 'proto/autoload.php';

	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Check For vehicle id
	if(empty($_POST['vehicle']) || !ctype_alnum($_POST['vehicle'])) {
		http_response_code(400);
		die('Vehicle id required and must be alpha-numeric');
	}


	# Check if new download of file is needed
	$updates = false;
	createCacheDir();
	if(is_file(GTFS_REALTIME_TRIPS_TIMESTAMP_FILE)) {
		$lastUpdate = (int)file_get_contents(GTFS_REALTIME_TRIPS_TIMESTAMP_FILE);
		if(time() - $lastUpdate < GTFS_REALTIME_TRIPS_UPDATE_FREQUENCY) {
			$updates = json_decode(file_get_contents(GTFS_REALTIME_TRIPS_DEST), true);
		}
	}

	if(!$updates) {
		$pb = file_get_contents(TRIP_UPDATES_ENDPOINT);
		if(!$pb) {
			http_response_code(500);
			die('Failed to complete API request for trip update');
		}

		try {
			$updates = [];

			$msg = new Transit_realtime\FeedMessage();
			$msg->mergeFromString($pb);
			foreach($msg->getEntity() as $entity) {
				$tripUpdate = $entity->getTripUpdate();
				if($tripUpdate->hasVehicle()) {
					$updates[$tripUpdate->getVehicle()->getId()] = [
						'timestamp' => $tripUpdate->getTimestamp(),
						'times' => [],
					];
					foreach($tripUpdate->getStopTimeUpdate() as $stopTime) {
						if(!array_key_exists($stopTime->getStopId(), $updates[$tripUpdate->getVehicle()->getId()]['times'])) {
							$updates[$tripUpdate->getVehicle()->getId()]['times'][$stopTime->getStopId()] = [];
						}
						$updates[$tripUpdate->getVehicle()->getId()]['times'][$stopTime->getStopId()][$stopTime->getStopSequence()] = [
							'arrival' => $stopTime->hasArrival() ? $stopTime->getArrival()->getTime() : 0,
							'departure' => $stopTime->hasDeparture() ? $stopTime->getDeparture()->getTime() : 0,
							'scheduleRelationship' => $stopTime->getScheduleRelationship(),
						];
					}	
				}
			}
			file_put_contents(GTFS_REALTIME_TRIPS_DEST, json_encode($updates));
			file_put_contents(GTFS_REALTIME_TRIPS_TIMESTAMP_FILE, time());
		}
		catch(GPBDecodeException $e) {
			http_response_code(500);
			echo $e->getMessage();
		}
	}



	if(array_key_exists($_POST['vehicle'], $updates)) {
		echo json_encode($updates[$_POST['vehicle']]);
	}
	else {
		http_response_code(404);
		die('Vehicle not found in updates list'); 
	}