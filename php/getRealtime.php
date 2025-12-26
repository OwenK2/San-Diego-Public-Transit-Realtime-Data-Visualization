<?php
	require_once 'csrf.php';
	require_once 'vendor/autoload.php';
	require_once 'proto/autoload.php';


	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Check if new download of file is needed
	if(is_file(GTFS_REALTIME_TIMESTAMP_FILE)) {
		$lastUpdate = (int)file_get_contents(GTFS_REALTIME_TIMESTAMP_FILE);
		if(time() - $lastUpdate < GTFS_REALTIME_UPDATE_FREQUENCY) {
			echo file_get_contents(GTFS_REALTIME_DEST);
			exit();
		}
	}

	# Get data from API
	$vehiclePositions = file_get_contents(VEHICLE_POSITIONS_ENDPOINT);

	$tripUpdates = file_get_contents(TRIP_UPDATES_ENDPOINT);
	if($vehiclePositions === false && $tripUpdates === false) {
		http_response_code(500);
		die('Failed to get data from API');
	}


	$results = ['positions' => [], 'updates' => []];
	try {
		// Parse Vehicle Data
		$msg = new Transit_realtime\FeedMessage();
		$msg->mergeFromString($vehiclePositions);
		foreach($msg->getEntity() as $entity) {
			$vehicle = $entity->getVehicle();
			if($vehicle->hasTrip() && $vehicle->hasVehicle()) {
				$results['positions'][$vehicle->getVehicle()->getId()] = [
					'position' => [$vehicle->getPosition()->getLatitude(), $vehicle->getPosition()->getLongitude()],
					'route' => $vehicle->getTrip()->getRouteId(),
					'trip' => $vehicle->getTrip()->getTripId(),
				];
			}
		}

		// Parse Trip Updates
		$msg = new Transit_realtime\FeedMessage();
		$msg->mergeFromString($tripUpdates);
		foreach($msg->getEntity() as $entity) {
			$tripUpdate = $entity->getTripUpdate();
			if($tripUpdate->hasVehicle()) {
				$results['updates'][$tripUpdate->getVehicle()->getId()] = [
					'timestamp' => $tripUpdate->getTimestamp(),
					'times' => [],
				];
				foreach($tripUpdate->getStopTimeUpdate() as $stopTime) {
					if(!array_key_exists($stopTime->getStopId(), $results['updates'][$tripUpdate->getVehicle()->getId()]['times'])) {
						$results['updates'][$tripUpdate->getVehicle()->getId()]['times'][$stopTime->getStopId()] = [];
					}
					$results['updates'][$tripUpdate->getVehicle()->getId()]['times'][$stopTime->getStopId()][$stopTime->getStopSequence()] = [
						'arrival' => $stopTime->hasArrival() ? $stopTime->getArrival()->getTime() : 0,
						'departure' => $stopTime->hasDeparture() ? $stopTime->getDeparture()->getTime() : 0,
						'scheduleRelationship' => $stopTime->getScheduleRelationship(),
					];
				}	
			}
		}

		echo json_encode($results);
		file_put_contents(GTFS_REALTIME_DEST, json_encode($results));
		file_put_contents(GTFS_REALTIME_TIMESTAMP_FILE, time());
	}
	catch(GPBDecodeException $e) {
		http_response_code(500);
		echo $e->getMessage();
	}