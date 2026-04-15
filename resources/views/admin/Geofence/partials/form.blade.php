@php
    $g = $geofence ?? null;
    $lat = old('latitude', $g?->latitude ?? 28.6139);
    $lng = old('longitude', $g?->longitude ?? 77.2090);
    $radius = old('radius', $g?->radius ?? 100);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="office_name" class="form-label text-muted fw-bold small text-uppercase">Office name</label>
        <input type="text" class="form-control" id="office_name" name="office_name"
               value="{{ old('office_name', $g?->office_name) }}" required>
        @error('office_name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label for="latitude" class="form-label text-muted fw-bold small text-uppercase">Latitude</label>
        <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude"
               value="{{ $lat }}" required>
        @error('latitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3">
        <label for="longitude" class="form-label text-muted fw-bold small text-uppercase">Longitude</label>
        <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude"
               value="{{ $lng }}" required>
        @error('longitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="radius" class="form-label text-muted fw-bold small text-uppercase">Radius (meters)</label>
        <input type="number" min="1" step="1" class="form-control" id="radius" name="radius"
               value="{{ $radius }}" required>
        @error('radius')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-8 d-flex align-items-end">
        <p class="text-muted small mb-2">
            <i class="fas fa-info-circle me-1"></i>
            Click anywhere on map to set location. Drag marker or change radius to update the geofence circle.
            GPS can be a few metres off; if the pin is not on your building, drag it to the exact spot.
        </p>
    </div>
    <div class="col-12">
        <label for="mapSearchInput" class="form-label text-muted fw-bold small text-uppercase">Search location</label>
        <div class="input-group">
            <input type="text" class="form-control" id="mapSearchInput"
                   placeholder="Search office, area, city, landmark...">
            <button type="button" class="btn btn-outline-secondary" id="useCurrentLocationBtn">
                <i class="fas fa-location-crosshairs"></i> Use current location
            </button>
            <button type="button" class="btn btn-modern" id="mapSearchBtn">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
        <div id="mapSearchMessage" class="small mt-1 text-muted"></div>
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="mapFullscreenBtn">
                <i class="fas fa-expand me-1"></i> Full screen
            </button>
        </div>
        <div id="geofenceMap" style="height: 430px; border-radius: 12px; border: 1px solid #e5e7eb;"></div>
    </div>
</div>

<script>
    (function () {
        function initGeofenceMap() {
            const mapElement = document.getElementById('geofenceMap');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');
            const mapSearchInput = document.getElementById('mapSearchInput');
            const mapSearchBtn = document.getElementById('mapSearchBtn');
            const mapSearchMessage = document.getElementById('mapSearchMessage');
            const useCurrentLocationBtn = document.getElementById('useCurrentLocationBtn');
            const mapFullscreenBtn = document.getElementById('mapFullscreenBtn');

            if (!mapElement || !latInput || !lngInput || !radiusInput || typeof window.L === 'undefined') {
                return false;
            }

            const defaultLat = parseFloat(latInput.value) || 28.6139;
            const defaultLng = parseFloat(lngInput.value) || 77.2090;
            const defaultRadius = parseFloat(radiusInput.value) || 100;

            const map = L.map('geofenceMap').setView([defaultLat, defaultLng], 15);

            const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            });

            const satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri'
                }
            );

            const placeLabelsForHybrid = L.tileLayer(
                'https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 19,
                    attribution: 'Labels &copy; Esri'
                }
            );

            const placeLabelsOverlay = L.tileLayer(
                'https://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 19,
                    attribution: 'Labels &copy; Esri'
                }
            );

            const hybridLayer = L.layerGroup([satelliteLayer, placeLabelsForHybrid]);

            streetLayer.addTo(map);
            L.control.layers(
                {
                    'Street': streetLayer,
                    'Satellite': satelliteLayer,
                    'Hybrid': hybridLayer
                },
                {
                    'Place Labels': placeLabelsOverlay
                },
                { collapsed: false }
            ).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
            const circle = L.circle([defaultLat, defaultLng], {
                radius: defaultRadius,
                color: '#4f46e5',
                fillColor: '#4f46e5',
                fillOpacity: 0.15
            }).addTo(map);

            /** GPS uncertainty ring (metres); cleared when user moves pin manually */
            let locationAccuracyCircle = null;

            function clearLocationAccuracyCircle() {
                if (locationAccuracyCircle && map.hasLayer(locationAccuracyCircle)) {
                    map.removeLayer(locationAccuracyCircle);
                }
                locationAccuracyCircle = null;
            }

            function setLocationAccuracyCircle(lat, lng, accuracyMeters) {
                clearLocationAccuracyCircle();
                let r = Number(accuracyMeters);
                if (isNaN(r) || r <= 0) {
                    r = 45;
                }
                r = Math.min(Math.max(r, 5), 2000);
                locationAccuracyCircle = L.circle([lat, lng], {
                    radius: r,
                    color: '#2563eb',
                    weight: 2,
                    fillColor: '#3b82f6',
                    fillOpacity: 0.14,
                    interactive: false
                }).addTo(map);
            }

            function updateInputs(latlng) {
                clearLocationAccuracyCircle();
                latInput.value = latlng.lat.toFixed(6);
                lngInput.value = latlng.lng.toFixed(6);
            }

            function updateMap(lat, lng, radius) {
                const latlng = L.latLng(lat, lng);
                marker.setLatLng(latlng);
                circle.setLatLng(latlng);
                circle.setRadius(radius);
            }

            function setSearchMessage(text, type) {
                if (!mapSearchMessage) return;
                mapSearchMessage.textContent = text || '';
                mapSearchMessage.className = `small mt-1 ${type ? 'text-' + type : 'text-muted'}`;
            }

            async function searchPlace() {
                if (!mapSearchInput) return;
                const query = mapSearchInput.value.trim();

                if (!query) {
                    setSearchMessage('Please enter a location to search.', 'danger');
                    return;
                }

                if (mapSearchBtn) mapSearchBtn.disabled = true;
                setSearchMessage('Searching location...', null);

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`
                    );

                    if (!response.ok) {
                        throw new Error('Search failed');
                    }

                    const results = await response.json();
                    if (!Array.isArray(results) || results.length === 0) {
                        setSearchMessage('No location found. Try another search.', 'warning');
                        return;
                    }

                    const first = results[0];
                    const lat = parseFloat(first.lat);
                    const lng = parseFloat(first.lon);
                    const radius = Math.max(1, parseFloat(radiusInput.value) || 1);

                    if (isNaN(lat) || isNaN(lng)) {
                        throw new Error('Invalid location');
                    }

                    latInput.value = lat.toFixed(6);
                    lngInput.value = lng.toFixed(6);
                    clearLocationAccuracyCircle();
                    updateMap(lat, lng, radius);
                    map.setView([lat, lng], 16);

                    setSearchMessage(`Showing: ${first.display_name || query}`, 'success');
                } catch (error) {
                    setSearchMessage('Search failed. Please try again.', 'danger');
                } finally {
                    if (mapSearchBtn) mapSearchBtn.disabled = false;
                }
            }

            function useCurrentLocation() {
                if (!navigator.geolocation) {
                    setSearchMessage('Geolocation is not supported in this browser.', 'warning');
                    return;
                }

                if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    setSearchMessage('Use HTTPS in production for accurate GPS; HTTP often falls back to coarse location.', 'warning');
                }

                if (useCurrentLocationBtn) {
                    useCurrentLocationBtn.disabled = true;
                }

                const radius = Math.max(1, parseFloat(radiusInput.value) || 1);
                const geoOptions = { enableHighAccuracy: true, maximumAge: 0, timeout: 25000 };

                let watchId = null;
                let settled = false;
                let best = null;
                const maxMs = 20000;
                const goodEnoughM = 35;

                function cleanupWatch() {
                    if (watchId !== null) {
                        navigator.geolocation.clearWatch(watchId);
                        watchId = null;
                    }
                }

                function applyBestReading() {
                    if (!best) return;
                    latInput.value = Number(best.lat).toFixed(6);
                    lngInput.value = Number(best.lng).toFixed(6);
                    updateMap(best.lat, best.lng, radius);
                    setLocationAccuracyCircle(best.lat, best.lng, best.accuracy);
                }

                function finish(lat, lng, accuracyM) {
                    if (settled) return;
                    settled = true;
                    cleanupWatch();
                    latInput.value = Number(lat).toFixed(6);
                    lngInput.value = Number(lng).toFixed(6);
                    updateMap(lat, lng, radius);
                    setLocationAccuracyCircle(lat, lng, accuracyM);
                    map.setView([lat, lng], 18);

                    const accText = typeof accuracyM === 'number' && !isNaN(accuracyM)
                        ? ' ±' + Math.round(accuracyM) + ' m.'
                        : '';
                    setSearchMessage(
                        'Location updated.' + accText + ' If the pin is not on your building, drag it to the correct spot.',
                        'success'
                    );
                    if (useCurrentLocationBtn) {
                        useCurrentLocationBtn.disabled = false;
                    }
                }

                function onGeoError() {
                    cleanupWatch();
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            const acc = typeof position.coords.accuracy === 'number' ? position.coords.accuracy : 80;
                            finish(position.coords.latitude, position.coords.longitude, acc);
                        },
                        function () {
                            setSearchMessage('Unable to get location. Allow permission, use HTTPS, or place the marker manually.', 'danger');
                            if (useCurrentLocationBtn) useCurrentLocationBtn.disabled = false;
                        },
                        geoOptions
                    );
                }

                function handleReading(position) {
                    if (settled) return;
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = typeof position.coords.accuracy === 'number' ? position.coords.accuracy : 999;

                    if (!best || acc < best.accuracy) {
                        best = { lat: lat, lng: lng, accuracy: acc };
                    }

                    applyBestReading();
                    map.panTo([best.lat, best.lng]);

                    setSearchMessage(
                        'Locking GPS… best ±' + Math.round(best.accuracy) + ' m. Hold still a few seconds.',
                        null
                    );

                    if (acc <= goodEnoughM) {
                        finish(best.lat, best.lng, best.accuracy);
                    }
                }

                setSearchMessage('Getting high-accuracy GPS… hold still for a few seconds.', null);

                if (navigator.geolocation.watchPosition) {
                    watchId = navigator.geolocation.watchPosition(
                        handleReading,
                        onGeoError,
                        geoOptions
                    );

                    setTimeout(function () {
                        if (settled) return;
                        cleanupWatch();
                        if (best) {
                            finish(best.lat, best.lng, best.accuracy);
                        } else {
                            navigator.geolocation.getCurrentPosition(
                                function (position) {
                                    handleReading(position);
                                    if (!settled && best) {
                                        finish(best.lat, best.lng, best.accuracy);
                                    }
                                },
                                function () {
                                    setSearchMessage('No GPS fix yet. Try near a window/outdoors, then retry — or drag the marker.', 'danger');
                                    if (useCurrentLocationBtn) useCurrentLocationBtn.disabled = false;
                                },
                                geoOptions
                            );
                        }
                    }, maxMs);
                } else {
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            handleReading(position);
                            if (!settled && best) {
                                finish(best.lat, best.lng, best.accuracy);
                            }
                        },
                        function () {
                            setSearchMessage('Unable to fetch current location. Please allow location permission.', 'danger');
                            if (useCurrentLocationBtn) useCurrentLocationBtn.disabled = false;
                        },
                        geoOptions
                    );
                }
            }

            map.on('click', function (e) {
                updateInputs(e.latlng);
                updateMap(e.latlng.lat, e.latlng.lng, parseFloat(radiusInput.value) || 100);
            });

            marker.on('dragend', function (e) {
                const latlng = e.target.getLatLng();
                updateInputs(latlng);
                updateMap(latlng.lat, latlng.lng, parseFloat(radiusInput.value) || 100);
            });

            radiusInput.addEventListener('input', function () {
                const lat = parseFloat(latInput.value) || defaultLat;
                const lng = parseFloat(lngInput.value) || defaultLng;
                const radius = Math.max(1, parseFloat(this.value) || 1);
                updateMap(lat, lng, radius);
            });

            latInput.addEventListener('change', function () {
                const lat = parseFloat(this.value);
                const lng = parseFloat(lngInput.value);
                const radius = Math.max(1, parseFloat(radiusInput.value) || 1);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMap(lat, lng, radius);
                    map.panTo([lat, lng]);
                }
            });

            lngInput.addEventListener('change', function () {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(this.value);
                const radius = Math.max(1, parseFloat(radiusInput.value) || 1);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateMap(lat, lng, radius);
                    map.panTo([lat, lng]);
                }
            });

            if (mapSearchBtn) {
                mapSearchBtn.addEventListener('click', searchPlace);
            }
            if (mapSearchInput) {
                mapSearchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchPlace();
                    }
                });
            }

            if (useCurrentLocationBtn) {
                useCurrentLocationBtn.addEventListener('click', useCurrentLocation);
            }

            function updateFullscreenButton(isFullscreen) {
                if (!mapFullscreenBtn) return;
                mapFullscreenBtn.innerHTML = isFullscreen
                    ? '<i class="fas fa-compress me-1"></i> Exit full screen'
                    : '<i class="fas fa-expand me-1"></i> Full screen';
            }

            async function toggleMapFullscreen() {
                try {
                    if (!document.fullscreenElement) {
                        await mapElement.requestFullscreen();
                    } else {
                        await document.exitFullscreen();
                    }
                } catch (err) {
                    setSearchMessage('Fullscreen is not supported in this browser.', 'warning');
                }
            }

            if (mapFullscreenBtn) {
                mapFullscreenBtn.addEventListener('click', toggleMapFullscreen);
            }

            document.addEventListener('fullscreenchange', function () {
                const isFullscreen = document.fullscreenElement === mapElement;
                if (isFullscreen) {
                    mapElement.style.height = '100vh';
                    mapElement.style.borderRadius = '0';
                } else {
                    mapElement.style.height = '430px';
                    mapElement.style.borderRadius = '12px';
                }
                updateFullscreenButton(isFullscreen);
                setTimeout(function () {
                    map.invalidateSize();
                }, 120);
            });

            setTimeout(function () {
                map.invalidateSize();
            }, 100);

            return true;
        }

        function waitForLeaflet() {
            if (initGeofenceMap()) return;
            setTimeout(waitForLeaflet, 100);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', waitForLeaflet);
        } else {
            waitForLeaflet();
        }
    })();
</script>
