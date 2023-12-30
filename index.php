<?php
	require_once 'php/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="tags" content="san diego mts transit data gps map leaflet gtfs" />
	<meta name="description" content="A visualization of GTFS transit data from MTS San Diego." />
	<meta name="author" content="Owen Kuhn" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#0A0A0A" media="(prefers-color-scheme: dark)"/>
    <meta name="theme-color" content="#FBF8F3" media="(prefers-color-scheme: light)"/>
	<meta name="csrf_token" content="<?php echo createToken(); ?>" />

	<title>SDMTS Realtime Visualizer</title>

	<link defer rel="shortcut icon" type="image/x-icon" href="assets/favicon.webp" />
	<link defer rel="apple-touch-icon" href="assets/favicon_maskable.webp">
	<link defer rel="manifest" href="manifest.json" />
	<link defer rel="stylesheet" href="assets/leaflet/leaflet.css" />
	<script defer src="assets/leaflet/leaflet.js"></script>
	<link defer rel="stylesheet" href="style.css" />
	<script defer src="script.js"></script>
</head>
<body>
	<div id="info">
		<svg class="close" onclick="closeInfo();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 329.26933 329"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"></path></svg>
		<div id="info_loader" class="loader"></div>
		<div id="info_head">
			<img id="trip_icon" draggable="false" src="assets/bus.webp" alt="[ICON]" />
			<div>
				<div id="trip">SDMTS Data Visualization</div>
				<div id="direction">Developed by Owen Kuhn</div>
			</div>
		</div>
		<div id="info_body">
			<div id="route"></div>
			<div id="timestamp"></div>
			<div id="info_stops">
				<span class="empty">Click on a vehicle to view more info</span>
			</div>
		</div>
	</div>
	<div id="map">
		<!-- <input id="search" type="text" placeholder="Stop Name" /> -->
		<div id="map_loader" class="loader"></div>
		<svg id="attrib_icon" title="Attributions" onclick="openAttrib();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M6 4H5a1 1 0 1 1 0-2h11V1a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V5a1 1 0 0 0-1-1h-7v8l-2-2-2 2V4z"/></svg>
	</div>
	<div id="attributions">
	    <svg class="close" onclick="closeAttrib()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 329.26933 329"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"></path></svg>
	    <h1>Attributions</h1>
	    <br><br>
	    <p>Topology Vectors by <a rel="noreferer" target="_blank" href="https://www.vecteezy.com/free-vector/topology">Vecteezy</a></p>
	    <p>Vehicle icons created by <a rel="noreferer" target="_blank" href="https://www.flaticon.com/free-icons/bus" title="bus icons">Freepik<a></a> - <a rel="noreferer" target="_blank" href="https://www.flaticon.com">Flaticon</a></p>
	    <p>Basemap images from <a rel="noreferer" target="_blank" href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a rel="noreferer" target="_blank" href="https://carto.com/attributions">CARTO</a></p>
	    <p>Map technology using <a rel="noreferer" target="_blank" href="https://leafletjs.com">LeafletJS</a></p>
	   	<br>
		<div class="btn" onclick="closeAttrib()">Close</div>
	</div>
	<div id="notifications"></div>
</body>
</html>