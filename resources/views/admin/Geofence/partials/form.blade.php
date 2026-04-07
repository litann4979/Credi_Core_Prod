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
        </p>
    </div>
    <div class="col-12">
        <label for="mapSearchInput" class="form-label text-muted fw-bold small text-uppercase">Search location</label>
        <div class="input-group">
            <input type="text" class="form-control" id="mapSearchInput"
                   placeholder="Search office, area, city, landmark...">
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

            streetLayer.addTo(map);
            L.control.layers(
                {
                    'Street': streetLayer,
                    'Satellite': satelliteLayer
                },
                null,
                { collapsed: false }
            ).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
            const circle = L.circle([defaultLat, defaultLng], {
                radius: defaultRadius,
                color: '#4f46e5',
                fillColor: '#4f46e5',
                fillOpacity: 0.15
            }).addTo(map);

            function updateInputs(latlng) {
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
                    updateMap(lat, lng, radius);
                    map.setView([lat, lng], 16);

                    setSearchMessage(`Showing: ${first.display_name || query}`, 'success');
                } catch (error) {
                    setSearchMessage('Search failed. Please try again.', 'danger');
                } finally {
                    if (mapSearchBtn) mapSearchBtn.disabled = false;
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
