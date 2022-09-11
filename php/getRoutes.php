<?php
	require_once 'csrf.php';

	# Check For CSRF
	if(empty($_POST['csrf_token']) || !validateToken($_POST['csrf_token'])) {
		http_response_code(401);
		die('Invalid CSRF');
	}

	# Fetch all route data
	try {
		$db = connect();
		$data = $db->query('SELECT * FROM routes')->fetchAll(PDO::FETCH_ASSOC);
		$routes = [];
		foreach($data as &$route) {
			$routes[$route['id']] = $route;
			unset($routes[$route['id']]['id']);
		}
		echo json_encode($routes);
	}
	catch(Exception $e) {
		http_response_code(500);
		die($e->getMessage());
	}