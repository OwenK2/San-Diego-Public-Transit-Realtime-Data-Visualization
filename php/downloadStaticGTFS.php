<?php
	require_once 'csrf.php';

	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Function to map GTFS csv files to database
	function updateGTFSDatabase($db, $table, $gtfsFile, $columnMap) {
		# Empty existing table
		$db->exec('TRUNCATE TABLE ' . $table);

		# Open GTFS data file & read header
		if (($handle = fopen(GTFS_STATIC_DEST . '/' . $gtfsFile, 'r')) === false) {
			throw new Exception('Failed to open GTFS file: ' . $gtfsFile);
		}
		fgetcsv($handle); # skip header

		# Prepare insert statement & batching
		$batchSize = 500;
		$batch = [];
		$db->beginTransaction();
		$numCols = count($columnMap);
		$insertPrefix = 'INSERT INTO ' . $table . ' VALUES ';

		# Read file and execute insert statements
		while (($row = fgetcsv($handle)) !== false) {
			$values = array_map(
				function($i) use ($row) {return $i !== false ? $row[$i] : PDO::PARAM_NULL; }
			, $columnMap);
			$batch[] = $values;
			if (count($batch) >= $batchSize) {
				$placeholders = implode(',', array_fill(0, count($batch), '(' . implode(',', array_fill(0, $numCols, '?')) . ')'));
				$stmt = $db->prepare($insertPrefix . $placeholders);
				$stmt->execute(array_merge(...$batch));
				$batch = [];
			}
		}
		if (count($batch) > 0) {
			$placeholders = implode(',', array_fill(0, count($batch), '(' . implode(',', array_fill(0, $numCols, '?')) . ')'));
			$stmt = $db->prepare($insertPrefix . $placeholders);
			$stmt->execute(array_merge(...$batch));
		}
		fclose($handle);
		$db->commit();
	}

	# Ensure only one process can download & update at a time
	try {
		# Acquire Lock
		$lockFile = fopen(GTFS_STATIC_TIMESTAMP_FILE, 'c+');
		if($lockFile === false) {
			throw new Exception('Failed to open lock file', 0);
		}
		if(!flock($lockFile, LOCK_EX | LOCK_NB)) {
			throw new NoUpdateException('Another update is already in progress');
		}

		# Check if new download of file is needed
		$md5 = '';
		if($timestampContents = file_get_contents(GTFS_STATIC_TIMESTAMP_FILE)) {
			$cacheInfo = explode('|', trim($timestampContents));
			$lastUpdate = $cacheInfo[0] ?? 0;
			$md5 = $cacheInfo[1] ?? '';
			$timeDiff = time() - $lastUpdate;
			if($timeDiff < GTFS_STATIC_UPDATE_FREQUENCY) {
				throw new NoUpdateException('Static GTFS data was updated within the update frequency of ' . GTFS_STATIC_UPDATE_FREQUENCY . ' seconds exactly ' . $timeDiff . ' seconds ago');
			}
		}

		# Download GTFS data
		$tmp = tempnam('.', 'gtfs_');
		if(!copy(GTFS_STATIC_ENDPOINT, $tmp)) {
			http_response_code(500);
			throw new Exception('Failed to download static GTFS zip');
		}

		# Check if MD5 has changed
		$newMD5 = md5_file($tmp);
		if($md5 === $newMD5) {
			file_put_contents(GTFS_STATIC_TIMESTAMP_FILE, time() . '|' . $newMD5);
			throw new NoUpdateException('Static GTFS data has not changed');
		}

		# Extract GTFS Data
		$zip = new ZipArchive();
		if(!$zip->open($tmp)) {
			http_response_code(500);
			throw new Exception('Failed to open static GTFS zip');
		}
		if(!is_dir(GTFS_STATIC_DEST)) {
			mkdir(GTFS_STATIC_DEST);
		}
		if(!$zip->extractTo(GTFS_STATIC_DEST)) {
			http_response_code(500);
			$zip->close();
			throw new Exception('Failed to extract static GTFS zip');
		}
		$zip->close();

		# Update database
		$db = connect();
		$routeColMap = [0, 3, 2, 4, 6];
		updateGTFSDatabase($db, 'routes', 'routes.txt', $routeColMap);
		$tripColMap = [2, 0, 3, 9, 6];
		updateGTFSDatabase($db, 'trips', 'trips.txt', $tripColMap);
		$stopColMap = [0, 2, 4, 5];
		updateGTFSDatabase($db, 'stops', 'stops.txt', $stopColMap);
		$scheduleColMap = [false, 0, 3, 1, 2, 8, 4];
		updateGTFSDatabase($db, 'schedule', 'stop_times.txt', $scheduleColMap);

		# Parse shapes
		if (($handle = fopen(GTFS_STATIC_DEST . '/shapes.txt', 'r')) === false) {
			throw new Exception('Failed to open GTFS file: shapes.txt');
		}
		fgetcsv($handle); # skip header
		$shapes = [];
		$distances = [];
		while (($row = fgetcsv($handle)) !== false) {
			if(!array_key_exists($row[0], $shapes)) {$shapes[$row[0]] = [];}
			$shapes[$row[0]][$row[3]] = [(double)$row[1], (double)$row[2]];
			$distances[$row[0]][] = (float)$row[4];
		}
		fclose($handle);

		# Update shapes table in DB with batching
		$db->exec('TRUNCATE TABLE shapes');
		$batchSize = 500;
		$batch = [];
		$db->beginTransaction();
		foreach($shapes as $id=>&$shape) {
			ksort($shape);
			$batch[] = [$id, json_encode(array_values($shape)), json_encode($distances[$id])];
			if (count($batch) >= $batchSize) {
				$placeholders = implode(',', array_fill(0, count($batch), '(?, ?, ?)'));
				$stmt = $db->prepare('INSERT INTO shapes VALUES ' . $placeholders);
				$stmt->execute(array_merge(...$batch));
				$batch = [];
			}
		}
		if (count($batch) > 0) {
			$placeholders = implode(',', array_fill(0, count($batch), '(?, ?, ?)'));
			$stmt = $db->prepare('INSERT INTO shapes VALUES ' . $placeholders);
			$stmt->execute(array_merge(...$batch));
		}
		$db->commit();

		# Update timestamp file
		file_put_contents(GTFS_STATIC_TIMESTAMP_FILE, time() . '|' . $newMD5);
	}
	catch(NoUpdateException $e) {
		echo($e->getMessage());
	}
	catch(Exception $e) {
		http_response_code(500);
		echo($e->getMessage());
	}
	finally {
		if($lockFile) {
			flock($lockFile, LOCK_UN);
			fclose($lockFile);
		}
		if(isset($tmp) && is_file($tmp)) {
			unlink($tmp);
		}
		if(is_dir(GTFS_STATIC_DEST)) {
			$it = new RecursiveDirectoryIterator(GTFS_STATIC_DEST, FilesystemIterator::UNIX_PATHS|FilesystemIterator::SKIP_DOTS|FilesystemIterator::CURRENT_AS_PATHNAME);
			foreach($it as $path) {unlink($path);}
			rmdir(GTFS_STATIC_DEST);
		}
	}