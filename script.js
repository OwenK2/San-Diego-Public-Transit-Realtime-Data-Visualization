let map, tileLayer, positionInterval, tripUpdateInterval, routes = {}, vehicles = {}, tripUpdates = {}, selectedVehicle, selectedData, userMarker, accuracyCircle, headingMarker, loaded;


const positionFetchRate = 5000;
const tripUpdateFetchRate = 15000;
const mapOptions = {
	attributionControl: false,
	zoomControl: false,
	zoomDelta: 1,
	zoomSnap: 0,
	zoom: 13,
	center: [32.7153300, -117.1572600],
	keyboard: true,
	scrollWheelZoom: true,
	doubleClickZoom: true,
	boxZoom: true,
	touchZoom: true,
	zoomAnimation: false,
	fadeAnimation: false,
	markerZoomAnimation: false,
};

const busIcon = L.icon({iconUrl: 'assets/bus.webp', iconSize: [24, 24]});
const trolleyIcon = L.icon({iconUrl: 'assets/trolley.webp', iconSize: [24, 24]});
const ferryIcon = L.icon({iconUrl: 'assets/ferry.webp', iconSize: [24, 24]});
const trainIcon = L.icon({iconUrl: 'assets/train.webp', iconSize: [24, 24]});



window.addEventListener("load", function() {
    loaded = 0;
    document.getElementById('map_loader').classList.add('show');
	if('serviceWorker' in navigator) {
		navigator.serviceWorker.register('sw.js', {scope: '.'})
		.then(function (registration) {}, function (err) {
			console.error('PWA: ServiceWorker registration failed: ', err);
		});
	}

	notificationElem = document.getElementById('notifications');

	map = L.map('map', mapOptions);
	let userPane = map.createPane('user');
	userPane.style.zIndex = 600;
	let stopPane = map.createPane('stops');
	stopPane.style.zIndex = 599;
	let vehiclePane = map.createPane('vehicles');
	vehiclePane.style.zIndex = 598;
	let highlightPane = map.createPane('vehicleHighlight');
	highlightPane.style.zIndex = 597;
	let pathPane = map.createPane('paths');
	pathPane.style.zIndex = 596;

	let theme_listener = window.matchMedia("(prefers-color-scheme: dark)");
	theme_listener.addListener(function(e) {setTheme(e.matches ? 'dark' : 'light')});
	setTheme((theme_listener.matches ? 'dark' : 'light'));

	post('php/downloadStaticGTFS.php', {}, function() {
	    checkInitialLoad();
	}, true, 'text');
	post('php/getRoutes.php', {}, function(r) {
		routes = r;
		beginUpdating();
		checkInitialLoad();
	});


	if(navigator.geolocation) {
		navigator.geolocation.watchPosition(function(position) {
			if(!accuracyCircle) {accuracyCircle = L.circle([position.coords.latitude, position.coords.longitude], position.coords.accuracy || 0, {stroke: true,color: 'var(--background)',weight: 1,fill: true,fillColor: 'var(--accent)',fillOpacity: .5,pane: 'user',interactive:false}).addTo(map);}
			else {
				accuracyCircle.setLatLng([position.coords.latitude, position.coords.longitude]);
				accuracyCircle.setRadius(position.coords.accuracy || 0);
			}
			if(position.coords.heading) {
				const heading = position.coords.heading * Math.PI / 180;
				const r = .0005;
				const a = 20 * (Math.PI / 180);
				if(!headingMarker) {headingMarker = L.polygon([[position.coords.latitude, position.coords.longitude],[position.coords.latitude+r*Math.cos(heading-a), position.coords.longitude+r*Math.sin(heading-a)],[position.coords.latitude+r*Math.cos(heading+a), position.coords.longitude+r*Math.sin(heading+a)]], {stroke: false,fill: true,fillColor: 'var(--accent2)',fillOpacity:.5,pane: 'user',interactive:false}).addTo(map);}
				else {
					headingMarker.setLatLngs([[position.coords.latitude, position.coords.longitude],[position.coords.latitude+r*Math.cos(heading-a), position.coords.longitude+r*Math.sin(heading-a)],[position.coords.latitude+r*Math.cos(heading+a), position.coords.longitude+r*Math.sin(heading+a)]]);
				}
			}
			if(!userMarker) {userMarker = L.circleMarker([position.coords.latitude, position.coords.longitude], {radius: 7, stroke: true,color: 'var(--background)',weight: 1,fill: true,fillColor: 'var(--accent)',fillOpacity: 1,pane: 'user',interactive:false}).addTo(map);}
			else {
				userMarker.setLatLng([position.coords.latitude, position.coords.longitude]);
			}
		});
	}
});
function setTheme(theme) {
	if(tileLayer) {tileLayer.remove();}
    if (theme === 'dark') {
        tileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',subdomains: 'abcd',maxZoom: 20}).addTo(map);
        document.querySelector(':root').className = '';
    }
    else {
        tileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>', subdomains: 'abcd', maxZoom: 20}).addTo(map);
        document.querySelector(':root').className = 'light';
    }
}
function beginUpdating() {
	getPositions();
	positionInterval = setInterval(getPositions, positionFetchRate);
}
function stopUpdating() {
	clearInterval(positionInterval);
}
function checkInitialLoad() {
    if(++loaded == 2) {
        document.getElementById('map_loader').classList.remove('show');
    }
}

function request(method, url, data, callback, async=true, responseType='json') {
	let xhr = new XMLHttpRequest();
	xhr.open(method, url, async);
	xhr.responseType = responseType;
	xhr.addEventListener('readystatechange', function() {
		if(xhr.readyState === 4) {
			if(xhr.status === 401) {
				notification('Invalid CSRF', 'Please refresh the page', true);
			}
			else if(xhr.status === 200) {
				if(xhr.responseType === 'json' && xhr.response === null) {
					notification('Invalid JSON', 'Failed to complete request for \'' + url + '\'', true);
				}
				if(callback instanceof Function) {
					callback(xhr.response);
				}
			}
			else {
				notification('Error ' + xhr.status, 'Failed to complete request for \'' + url + '\'', true);
			}
		}
	});
	data = data instanceof FormData ? data : createFormData(data);
	data.append('csrf_token', document.querySelector('[name="csrf_token"]').getAttribute('content'));
	xhr.send(data);
}
function post(url, data, callback, async=true, responseType='json') {request('POST', url, data, callback, async, responseType);}
function get(url, data, callback, async=true, responseType='json') {request('GET', url, data, callback, async, responseType);}

function getPositions(data) {
	post('php/getPositions.php', {}, function(result) {
		Object.keys(result).forEach(function(id) {
			// Update Positions
			if(id in vehicles) {
				updateVehiclePosition(id, result[id].position);
				if(vehicles[id].route !== result[id].route || vehicles[id].trip !== result[id].trip) {
					vehicles[id].route = result[id].route;
					vehicles[id].trip = result[id].trip;
					if(selectedVehicle === id) {getVehicleInfo();}
				}
			}
			// Add new vehicles
			else {
				vehicles[id] = result[id];
				vehicles[id].marker = L.marker(vehicles[id].position, {icon: getRouteIcon(vehicles[id].route), pane: 'vehicles'}).addTo(map);
				vehicles[id].marker.on('click', function(e) {if(e.originalEvent.isTrusted) {selectVehicle(id);}});
			}
		});

		// Delete any vehicles that no longer have positions
		Object.keys(vehicles).forEach(function(id) {
			if(!(id in result)) {
				if(id === selectedVehicle) {deselectVehicle();}
				vehicles[id].marker.remove();
				delete vehicles[id];
			}
		})
	});
}
function updateVehiclePosition(id, position) {
	if(vehicles[id].position[0] !== position[0] || vehicles[id].position[1] !== position[1]) {
		vehicles[id].position = position;
		clearTimeout(vehicles[id].translateTimeout);
		vehicles[id].marker._icon.style.transition = "transform .5s ease-in";
		if(id === selectedVehicle && vehicles[id].circle) {vehicles[id].circle.setLatLng(vehicles[id].position);}
		vehicles[id].marker.setLatLng(vehicles[id].position);
		vehicles[id].translateTimeout = setTimeout(function() {vehicles[id].marker._icon.style.transition = "";}, 500);
	}
}
function getTripUpdates() {
	if(!selectedVehicle) {return;}
	post('php/getTripUpdates.php', {vehicle: selectedVehicle}, function(result) {
		tripUpdates[selectedVehicle] = result;
		updateInfoPane();
	});
}

function selectVehicle(id) {
	if(selectedVehicle === id) {openInfo();return;}
	else if(id in vehicles) {
		if(selectedVehicle) {deselectVehicle();}
		selectedVehicle = id;
		vehicles[selectedVehicle].marker._icon.classList.add('selected');
		vehicles[selectedVehicle].circle = L.circleMarker(vehicles[selectedVehicle].position, {radius: 50,color: "var(--accent)",weight: 2,fill: true,fillOpacity: 0.3,fillColor: "var(--accent)",interactive: false,pane: 'vehicleHighlight'});
		vehicles[selectedVehicle].circle.addTo(map);
		getTripUpdates();
		clearInterval(tripUpdateInterval);
		tripUpdateInterval = setInterval(getTripUpdates, tripUpdateFetchRate);
		getVehicleInfo();
		openInfo();
	}
}
function getVehicleInfo() {
	updateInfoPaneStatic();
	document.getElementById('info_loader').classList.add('show');
	post('php/getVehicleInfo.php', {trip: vehicles[selectedVehicle].trip}, function(result) {
		selectedData = result;
		updateInfoPaneStatic();
		document.getElementById('info_loader').classList.remove('show');
	});
}
function deselectVehicle() {
	if(selectedData) {
		selectedData.stops.forEach(function(stop) {stop.marker.remove();});
		if(selectedData.pastPath) {selectedData.pastPath.remove();}
		if(selectedData.futurePath) {selectedData.futurePath.remove();}
	}
	if(selectedVehicle) {
		vehicles[selectedVehicle].circle.remove();
		delete vehicles[selectedVehicle].circle;
		vehicles[selectedVehicle].marker._icon.classList.remove('selected');
	}
	clearInterval(tripUpdateInterval);
	selectedVehicle = null;
	selectedData = null;
}

function updateInfoPaneStatic() {
	let route = vehicles[selectedVehicle].route;
	document.getElementById('trip').textContent = 'Loading...';
	document.getElementById('direction').textContent = 'Please Wait';
	document.getElementById('trip_icon').src = getRouteIcon(route, 'img');
	document.getElementById('route').innerHTML = routes[route].name + ' <d>('+routes[route].short_name+')</d>';
	if(selectedData) {
		document.getElementById('trip').textContent = selectedData.headsign;
		document.getElementById('direction').textContent = selectedData.direction;
		let stopsHTML = '<div id="old_stops_toggle" onclick="this.parentNode.classList.toggle(\'showold\');"></div>';
		for(let i = 0; i < selectedData.stops.length; ++i) {
			let stop = selectedData.stops[i];
			let id = stop.stop + '_' + stop.sequence;
			stopsHTML += `<div class="stop" id="stop_`+id+`" onmouseenter="showTooltip(`+i+`)" onmouseleave="hideActiveTooltip();"><div>`+stop.name+`<br><div class="time">Loading</div></div></div>`;
			stop.marker = L.circleMarker([stop.lat, stop.lng], {radius: 5,color: "var(--accent)",weight: 3,fill: true,fillOpacity: 1,fillColor: "var(--background)",pane: 'stops'});
			stop.marker.bindTooltip(stop.name, {opacity: 1, sticky: true, permanent: false, direction: 'top', offset: [0, -5]});
			stop.marker.getTooltip().stop = id;
			stop.marker.on('tooltipopen', scrollToStop);
			stop.marker.on('tooltipclose', unHighlightStop);
			stop.marker.addTo(map);
		}
		document.getElementById('info_stops').innerHTML = stopsHTML;
	}
	updateInfoPane();
}
function updateInfoPane() {
	if(!(selectedVehicle in vehicles)) {deselectVehicle();}
	let vehicle = vehicles[selectedVehicle];
	let updates = selectedVehicle in tripUpdates ? tripUpdates[selectedVehicle] : {timestamp: new Date().valueOf(), times: []};
	let referenceTime = new Date(updates.timestamp * 1000);

	document.getElementById('timestamp').textContent = 'Last Update: ' + referenceTime.toLocaleTimeString(undefined, {timeZone: 'America/Los_Angeles'});

	if(selectedData) {
		let prevStopDistance = Infinity;
		let anyOld = false;
		for(let i = 0; i < selectedData.stops.length; ++i) {
			let stop = selectedData.stops[i];
			let id = stop.stop+'_'+stop.sequence;
			let time = parseTime(stop.arrival > 0 ? stop.arrival : stop.departure, referenceTime);
			let status = 0;
			let isUpdated = false;
			if(stop.stop in updates.times) {
				let s = (stop.sequence in updates.times[stop.stop]) ? stop.sequence : 0;
				time = new Date((updates.times[stop.stop][s].arrival > 0 ? updates.times[stop.stop][s].arrival : updates.times[stop.stop][s].departure) * 1000);
				status = updates.times[stop.stop][s].scheduleRelationship;
				isUpdated = true;
				document.getElementById('stop_' + id).classList.add('realtime');
			}
			else {document.getElementById('stop_' + id).classList.remove('realtime');}
			document.getElementById('stop_' + id).querySelector('.time').textContent = time.toLocaleTimeString(undefined, {timeZone: 'America/Los_Angeles'});
			if(time >= referenceTime) {
				if(prevStopDistance === Infinity) {
					if(i > 0) {prevStopDistance = selectedData.stops[i-1].distance;}
					else {prevStopDistance = -Infinity;}
				}
				document.getElementById('stop_' + id).classList.remove('old');
				stop.marker.setStyle({color: 'var(--accent)'});
				stop.marker.getTooltip().options.className = '';
			}
			else {
				document.getElementById('stop_' + id).classList.add('old');
				stop.marker.setStyle({color: 'var(--dull)'});
				stop.marker.getTooltip().options.className = 'old';
				anyOld = true;
			}
		}

		document.getElementById('old_stops_toggle').style.display = anyOld ? 'block' : 'none';

		// Path
		if(selectedData.pastPath) {selectedData.pastPath.remove();}
		if(selectedData.futurePath) {selectedData.futurePath.remove();}
		let i = 0;
		while(i < selectedData.distances.length) {
			if(selectedData.distances[i++] >= prevStopDistance) {break;}
		}
		selectedData.pastPath = L.polyline(selectedData.path.slice(0, i), {color: "var(--dull)",smoothFactor: 0,noClip: false,weight: 5,fill: false,interactive: false,pane: 'paths'}).addTo(map);
		selectedData.futurePath = L.polyline(selectedData.path.slice(i > 0 ? i-1 : 0), {color: "var(--accent)",smoothFactor: 0,noClip: false,weight: 5,fill: false,interactive: false,pane: 'paths'}).addTo(map);
	}
}

let activeTooltipMarker = null;
function showTooltip(id) {
	hideActiveTooltip();
	if(id > -1 && id < selectedData.stops.length) {
		activeTooltipMarker = selectedData.stops[id].marker;
		let tooltip = activeTooltipMarker.getTooltip()
		selectedData.stops[id].marker.unbindTooltip()
		tooltip.options.permanent = true;
		selectedData.stops[id].marker.bindTooltip(tooltip);
	}
}
function hideActiveTooltip() {
	if(activeTooltipMarker) {
		let tooltip = activeTooltipMarker.getTooltip()
		activeTooltipMarker.unbindTooltip()
		tooltip.options.permanent = false;
		activeTooltipMarker.bindTooltip(tooltip);
		activeTooltipMarker = null;
	}
}
function scrollToStop(evt) {
	let e = document.querySelector('#stop_' + evt.tooltip.stop);
	if(e) {
		if(e.offsetTop+e.offsetHeight-10 <= document.getElementById('info_body').scrollTop || e.offsetTop+10 >= document.getElementById('info_body').scrollTop + document.getElementById('info_body').offsetHeight) {
			document.getElementById('info_body').scrollTo({
				top: e.offsetTop,
				left: 0,
				behavior: 'smooth',
			});
		}
		e.classList.add('highlight');
	}
}
function unHighlightStop(evt) {
	let e = document.querySelector('#stop_' + evt.tooltip.stop);
	if(e) {
		e.classList.remove('highlight');
	}
}

function openInfo() {
	document.getElementById('info').classList.add('show');
}
function closeInfo() {
	document.getElementById('info').classList.remove('show');
}
function openAttrib() {
	document.getElementById('attributions').classList.add('show');
}
function closeAttrib() {
	document.getElementById('attributions').classList.remove('show');
}

// Helpers
function getRouteIcon(routeid, type='icon') {
	if(routeid in routes) {
		switch(routes[routeid].type) {
			case 0: 
			case 5: 
			case 11: return type === 'icon' ? trolleyIcon : 'assets/trolley.webp';

			case 1: 
			case 2: 
			case 12: return type === 'icon' ? trainIcon : 'assets/train.webp';

			case 4: return type === 'icon' ? ferryIcon : 'assets/ferry.webp';

			default: return type === 'icon' ? busIcon : 'assets/bus.webp';
		}
	}
	return busIcon;
}
function createFormData(obj) {
	let fd = new FormData();
	if(obj) {
		Object.keys(obj).forEach(function(key) {
			fd.append(key, obj[key]);
		});
	}
	return fd;
}
function parseTime(time, referenceDate = new Date()) {
	let pst = new Date(referenceDate.toLocaleString('en-US', {timeZone: 'America/Los_Angeles'}));
	let d = new Date(referenceDate);
	let tzAdjustment = d - pst.getTime();
	let parts = time.split(':');
	let hrs = parseInt(parts[0], 10);
	d.setHours(hrs % 24);
	d.setMinutes(parseInt(parts[1], 10));
	d.setSeconds(parseInt(parts[2], 10));
	d.setDate(d.getDate() + Math.floor(hrs / 24));
	d.setMilliseconds(d.getMilliseconds() + tzAdjustment);
	return d;
}
function formatDelay(delay) {
	if(delay === 0) {return 'On Schedule';}
	let d = Math.abs(delay);
	let suffix = delay > 0 ? 'Late' : 'Early';
	let mins = Math.floor(d / 60);
	let sec = d % 60;
	let res = '';
	if(mins === 1) {res += mins + ' min ';}
	else if(mins > 1) {res += mins + ' mins ';}
	if(sec === 1) {res += sec + ' sec ';}
	else if(sec > 1) {res += sec + ' secs ';}
	return res + suffix;
}

// Notifications
let notificationElem;
function notification(title, body, error=false) {
	var n = document.createElement("div");
	if(error) {n.className = 'error';}
	n.innerHTML = '<div>' + title + '</div><p>' + body + '</p>';
	n.onclick = function() {closeNotification(n);}
	notificationElem.insertBefore(n, notificationElem.firstChild);
	setTimeout(function() {n.style.marginLeft = '0%';}, 10);
	setTimeout(function() {closeNotification(n);}, 5000);
}
function closeNotification(elem) {
	if(elem) {
		elem.style.marginLeft = "100%";
		setTimeout(function() {
			elem.remove();
		}, 500);
	}
}