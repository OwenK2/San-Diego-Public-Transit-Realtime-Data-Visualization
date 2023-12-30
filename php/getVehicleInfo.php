<?php
	require_once 'csrf.php';

	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Check for invalid trip
	$trip = $_POST['trip'];
	if(empty($trip) || !ctype_alnum($trip)) {
		http_response_code(400);
		die('Must specify trip of vehicle');
	}
	
	# Fetch relevant data
	try {
		$db = connect();
		$stmt = $db->prepare('SELECT headsign, direction, path, distances FROM trips LEFT JOIN shapes ON shape=shapes.id WHERE trips.id=? LIMIT 1');
		$stmt->execute([$trip]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result === false) {
			http_response_code(500);
			die('Could not find trip data for this vehicle');
		} 

		$stmt = $db->prepare('SELECT stop, name, lat, lng, arrival, departure, distance, sequence FROM schedule INNER JOIN stops ON schedule.stop=stops.id WHERE trip=? ORDER BY sequence ASC');
		$stmt->execute([$trip]);
		$result['stops'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$result['path'] = json_decode($result['path']);
		$result['distances'] = json_decode($result['distances']);
		echo json_encode($result);
	}
	catch(Exception $e) {
		http_response_code(500);
		die($e->getMessage());
	}
