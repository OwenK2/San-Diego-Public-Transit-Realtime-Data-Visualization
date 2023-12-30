<?php
	require_once 'csrf.php';

	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Check if new download of file is needed
	if(is_file(GTFS_STATIC_TIMESTAMP_FILE)) {
		$lastUpdate = (int)file_get_contents(GTFS_STATIC_TIMESTAMP_FILE);
		if(time() - $lastUpdate < GTFS_STATIC_UPDATE_FREQUENCY) {
			exit();
		}
	}
	file_put_contents(GTFS_STATIC_TIMESTAMP_FILE, time());

	# Download & Extract GTFS Data
	$tmp = tempnam('.', 'gtfs_');
	if(copy(GTFS_STATIC_ENDPOINT, $tmp)) {
		$zip = new ZipArchive();
		if($zip->open($tmp)) {
			if(!is_dir(GTFS_STATIC_DEST)) {mkdir(GTFS_STATIC_DEST);}
			if(!$zip->extractTo(GTFS_STATIC_DEST)) {
				
				http_response_code(500);
				$zip->close();
				unlink($tmp);
				die('Failed to extract static GTFS zip');
			}
			$zip->close();
			unlink($tmp);
		}
		else {
			http_response_code(500);
			unlink($tmp);
			die('Failed to open static GTFS zip');
		}
	}
	else {
		http_response_code(500);
		unlink($tmp);
		die('Failed to download static GTFS zip');
	}

	# Connect to database before parsing data
	try {
		$db = connect();
		$db->beginTransaction();

		# Parse routes
		$db->exec('DELETE FROM routes');
		$db->exec('LOAD DATA LOCAL INFILE \'' . GTFS_STATIC_DEST . '/routes.txt\' INTO TABLE routes FIELDS TERMINATED BY \',\' ENCLOSED BY \'\"\' LINES TERMINATED BY \'\\n\' IGNORE 1 ROWS (id, @agency, short_name, name, type, @url, @color, @text_color, @sort_order, @network_id, @dir0_name, @dir1_name, @route_group, @pattern1, @pattern2) SET color = CONCAT(\'#\', @color)');

		# Parse trips
		$db->exec('DELETE FROM trips');
		$db->exec('LOAD DATA LOCAL INFILE \'' . GTFS_STATIC_DEST . '/trips.txt\' INTO TABLE trips FIELDS TERMINATED BY \',\' ENCLOSED BY \'\"\' LINES TERMINATED BY \'\\n\' IGNORE 1 ROWS (route, @service, id, headsign, @dirid, @block, shape, @wheelchair_accessable, @bikes_allowed, direction, @tip_bikes_allowed, @short_headsign)');

		# Parse stops
		$db->exec('DELETE FROM stops');
		$db->exec('LOAD DATA LOCAL INFILE \'' . GTFS_STATIC_DEST . '/stops.txt\' INTO TABLE stops FIELDS TERMINATED BY \',\' ENCLOSED BY \'\"\' LINES TERMINATED BY \'\\n\' IGNORE 1 ROWS (id, @code, name, @desc, lat, lng, @zoneid, @url, @location_type, @parent_station, @wheelchair_boarding, @platform_code, @intersection_code, @reference_place, @stop_name_short, @stop_place)');

		# Parse schedule
		$db->exec('DELETE FROM schedule');
		$db->exec('LOAD DATA LOCAL INFILE \'' . GTFS_STATIC_DEST . '/stop_times.txt\' INTO TABLE schedule FIELDS TERMINATED BY \',\' ENCLOSED BY \'\"\' LINES TERMINATED BY \'\\n\' IGNORE 1 ROWS (trip, arrival, departure, stop, sequence, @headsign, @pickup_type, @drop_off_type, distance, @timepoint)');
		
		# Parse shapes
		$shapesCSV = fopen(GTFS_STATIC_DEST . '/shapes.txt', 'r');
		if(!$shapesCSV) {throw new Exception("Failed to open shapes.txt", 0);}
		fgets($shapesCSV); # ignore first line
		$shapes = [];
		$dists = [];
		while($row = fgets($shapesCSV)) {
			$cols = explode(',', trim($row));
			if(!array_key_exists($cols[0], $shapes)) {$shapes[$cols[0]] = [];}
			$shapes[$cols[0]][$cols[3]] = [(double)$cols[1], (double)$cols[2]];
			$dists[$cols[0]][] = (float)$cols[4];
		}
		$db->exec('DELETE FROM shapes');
		$stmt = $db->prepare('REPLACE INTO shapes VALUES (?, ?, ?)');
		foreach($shapes as $id=>&$shape) {
			ksort($shape);
			$stmt->execute([$id, json_encode(array_values($shape)), json_encode($dists[$id])]);
		}

		$db->commit();
	}
	catch(Exception $e) {
		http_response_code(500);
		die($e->getMessage());
	}

	# Clear GTFS Directory
	$it = new RecursiveDirectoryIterator(GTFS_STATIC_DEST, FilesystemIterator::UNIX_PATHS|FilesystemIterator::SKIP_DOTS|FilesystemIterator::CURRENT_AS_PATHNAME);
	foreach($it as $path) {unlink($path);}
	if(!rmdir(GTFS_STATIC_DEST)) {
		http_response_code(500);
		die('Failed to remove GTFS static folder');
	}