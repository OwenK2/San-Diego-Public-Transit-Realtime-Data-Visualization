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
	if(is_file(GTFS_REALTIME_POS_TIMESTAMP_FILE)) {
		$lastUpdate = (int)file_get_contents(GTFS_REALTIME_POS_TIMESTAMP_FILE);
		if(time() - $lastUpdate < GTFS_REALTIME_POS_UPDATE_FREQUENCY) {
			echo file_get_contents(GTFS_REALTIME_POS_DEST);
			exit();
		}
	}


	# Download vehicle positions (protobuf)
	$pb = file_get_contents(VEHICLE_POSITIONS_ENDPOINT);
	if($pb === false) {
		http_response_code(500);
		die('Failed to get data from API');
	}

	try {
		$msg = new Transit_realtime\FeedMessage();
		$msg->mergeFromString($pb);
		$results = [];
		foreach($msg->getEntity() as $entity) {
			$vehicle = $entity->getVehicle();
			if($vehicle->hasTrip() && $vehicle->hasVehicle()) {
				$results[$vehicle->getVehicle()->getId()] = [
					'position' => [$vehicle->getPosition()->getLatitude(), $vehicle->getPosition()->getLongitude()],
					'route' => $vehicle->getTrip()->getRouteId(),
					'trip' => $vehicle->getTrip()->getTripId(),
				];
			}
		}

		$r = json_encode($results);
		echo $r;
		file_put_contents(GTFS_REALTIME_POS_DEST, $r);
		file_put_contents(GTFS_REALTIME_POS_TIMESTAMP_FILE, time());
	}
	catch(GPBDecodeException $e) {
		http_response_code(500);
		echo $e->getMessage();
	}