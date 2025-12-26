<?php
	require_once '.env'; # All secret (commented out) defines go in here

	# Setup
	error_reporting(0);
	# error_reporting(E_ALL);
	date_default_timezone_set('America/Los_Angeles'); 
	session_set_cookie_params(['samesite' => 'Strict']);

	# Constants
	define('GTFS_STATIC_DEST', 'gtfs');
	define('GTFS_CACHE_DIR', '../cache');
	define('GTFS_REALTIME_POS_DEST', GTFS_CACHE_DIR . '/realtime_pos.json');
	define('GTFS_REALTIME_TRIPS_DEST', GTFS_CACHE_DIR . '/realtime_trips.json');
	define('GTFS_STATIC_TIMESTAMP_FILE', GTFS_CACHE_DIR . '/static_timestamp.txt');
	define('GTFS_REALTIME_POS_TIMESTAMP_FILE', GTFS_CACHE_DIR . '/realtime_pos_timestamp.txt');
	define('GTFS_REALTIME_TRIPS_TIMESTAMP_FILE', GTFS_CACHE_DIR . '/realtime_trips_timestamp.txt');
	define('GTFS_STATIC_UPDATE_FREQUENCY', 604800); # in seconds (currently set to 1 week)
	define('GTFS_REALTIME_POS_UPDATE_FREQUENCY', 10); # in seconds
	define('GTFS_REALTIME_TRIPS_UPDATE_FREQUENCY', 30); # in seconds

	# API 
	define('GTFS_STATIC_ENDPOINT', 'https://www.sdmts.com/google_transit_files/google_transit.zip');
	define('TRIP_UPDATES_ENDPOINT', 'https://realtime.sdmts.com/api/api/gtfs_realtime/trip-updates-for-agency/MTS.pb?key='.API_KEY);
	define('VEHICLE_POSITIONS_ENDPOINT', 'https://realtime.sdmts.com/api/api/gtfs_realtime/vehicle-positions-for-agency/MTS.pb?key='.API_KEY);

	# Useful Utility Functions
	function connect() {
		return new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DATABASE . ';', MYSQL_USERNAME, MYSQL_PASSWORD, [
			PDO::ATTR_STRINGIFY_FETCHES => false,
			PDO::ATTR_EMULATE_PREPARES => false,
		]);
	}
	function createCacheDir() {
		if(!is_dir(GTFS_CACHE_DIR)) {
			mkdir(GTFS_CACHE_DIR);
		}
	}
	class NoUpdateException extends Exception {}
