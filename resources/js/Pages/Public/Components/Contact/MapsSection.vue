<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    locations: {
        type: Array,
        required: true
    },
    selectedLocation: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:selectedLocation']);

const map = ref(null);
const markers = ref([]);
const infoWindow = ref(null);
const mapElement = ref(null);
const isMapLoaded = ref(false);
const mapLoadError = ref(false);

// Watch for location changes
watch(() => props.selectedLocation, (newLocation) => {
    if (map.value && newLocation) {
        centerMapOnLocation(newLocation);
        openInfoWindow(newLocation);
    }
}, { deep: true });

const centerMapOnLocation = (location) => {
    if (map.value) {
        map.value.setCenter(location.position);
        map.value.setZoom(15);
    }
};

const openInfoWindow = (location) => {
    if (!infoWindow.value || !map.value) return;

    // Find the marker for this location
    const markerObj = markers.value.find(m => m.location.name === location.name);

    if (!markerObj) return;

    // Update all marker icons
    markers.value.forEach(m => {
        m.marker.setIcon({
            path: google.maps.SymbolPath.CIRCLE,
            scale: 10,
            fillColor: m.location.name === location.name ? '#16a34a' : '#9ca3af',
            fillOpacity: 0.9,
            strokeWeight: 2,
            strokeColor: '#ffffff'
        });
    });

    // Set content and open info window
    const content = `
    <div class="p-3 max-w-xs">
      <h3 class="text-lg font-bold text-gray-800">${location.name}</h3>
      <p class="text-sm text-green-600">${location.storeType}</p>
      <div class="mt-2">
        <p class="text-gray-600">${location.address}</p>
        <p class="text-gray-700 font-medium">${location.phone}</p>
      </div>
    </div>
  `;

    infoWindow.value.setContent(content);
    infoWindow.value.open(map.value, markerObj.marker);
};

const initMap = () => {
    try {
        if (!mapElement.value || !window.google || !window.google.maps) {
            throw new Error('Google Maps not initialized properly');
        }

        // Create map instance
        map.value = new google.maps.Map(mapElement.value, {
            center: props.selectedLocation.position,
            zoom: 15,
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [{"color": "#444444"}]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [{"color": "#f2f2f2"}]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [{"saturation": -100}, {"lightness": 45}]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [{"visibility": "simplified"}]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "labels.icon",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [{"visibility": "off"}]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [{"color": "#c2e8f9"}, {"visibility": "on"}]
                }
            ]
        });

        // Create info window
        infoWindow.value = new google.maps.InfoWindow();

        // Create markers for each location
        markers.value = props.locations.map(location => {
            const marker = new google.maps.Marker({
                position: location.position,
                map: map.value,
                title: location.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: location.name === props.selectedLocation.name ? '#16a34a' : '#9ca3af',
                    fillOpacity: 0.9,
                    strokeWeight: 2,
                    strokeColor: '#ffffff'
                },
                animation: google.maps.Animation.DROP
            });

            // Add click event to marker
            marker.addListener('click', () => {
                emit('update:selectedLocation', location);
            });

            return { location, marker };
        });

        // Open info window for selected location
        openInfoWindow(props.selectedLocation);
        isMapLoaded.value = true;
        mapLoadError.value = false;
    } catch (error) {
        console.error('Error initializing map:', error);
        mapLoadError.value = true;
    }
};

const loadGoogleMaps = () => {
    return new Promise((resolve, reject) => {
        try {
            if (window.google && window.google.maps) {
                resolve();
                return;
            }

            window.initGoogleMaps = () => {
                resolve();
            };

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initGoogleMaps`;
            script.async = true;
            script.defer = true;
            script.onerror = () => {
                reject(new Error('Failed to load Google Maps API'));
            };
            document.head.appendChild(script);
        } catch (error) {
            reject(error);
        }
    });
};

onMounted(async () => {
    try {
        await loadGoogleMaps();
        initMap();
    } catch (error) {
        console.error('Error loading Google Maps:', error);
        mapLoadError.value = true;
    }
});
</script>

<template>
    <div class="w-full h-[500px] my-16 relative rounded-xl overflow-hidden shadow-lg">
        <!-- Map container -->
        <div ref="mapElement" class="w-full h-full bg-gray-200">
            <!-- Loading state -->
            <div v-if="!isMapLoaded && !mapLoadError" class="w-full h-full flex items-center justify-center">
                <div class="text-center p-4">
                    <font-awesome-icon icon="map-marker-alt" class="text-green-600 text-5xl mb-4" />
                    <h3 class="text-xl font-bold">Chargement de la carte...</h3>
                    <p class="text-gray-600 mt-2">Veuillez patienter pendant le chargement de la carte.</p>
                </div>
            </div>

            <!-- Error state -->
            <div v-if="mapLoadError" class="w-full h-full flex items-center justify-center">
                <div class="text-center p-4">
                    <font-awesome-icon icon="exclamation-circle" class="text-red-600 text-5xl mb-4" />
                    <h3 class="text-xl font-bold text-red-600">Erreur de chargement</h3>
                    <p class="text-gray-600 mt-2">Impossible de charger Google Maps. Veuillez réessayer plus tard.</p>
                </div>
            </div>
        </div>

        <!-- Map Info Overlay -->
        <div v-if="isMapLoaded" class="absolute top-4 right-4 z-10 bg-white p-4 rounded-lg shadow-md max-w-sm border-l-4 border-green-600 location-card-active">
            <div class="flex items-center mb-3">
                <div class="bg-green-100 p-3 w-10 h-10 flex items-center justify-center rounded-full mr-3">
                    <font-awesome-icon icon="map-marker-alt" class="text-green-600 text-xl" />
                </div>
                <div>
                    <h3 class="font-bold text-xl text-gray-800">{{ selectedLocation.name }}</h3>
                    <p class="text-sm text-green-600">{{ selectedLocation.storeType }}</p>
                </div>
            </div>
            <div class="space-y-3 pl-2">
                <p class="text-gray-600 flex items-start">
                    <font-awesome-icon icon="location-dot" class="text-green-500 mr-2 mt-1" />
                    {{ selectedLocation.address }}
                </p>
                <p class="text-gray-700 font-medium flex items-center">
                    <font-awesome-icon icon="phone" class="text-green-500 mr-2" />
                    {{ selectedLocation.phone }}
                </p>
                <p class="text-gray-700 font-medium flex items-center">
                    <font-awesome-icon icon="clock" class="text-green-500 mr-2" />
                    Lun-Ven: 9h00-19h00
                </p>
            </div>
            <div class="mt-4 pt-2 border-t border-gray-100">
                <a :href="`https://www.google.com/maps/dir/?api=1&destination=${selectedLocation.position.lat},${selectedLocation.position.lng}`"
                   target="_blank"
                   class="text-green-600 font-medium flex items-center">
                    <font-awesome-icon icon="directions" class="mr-2" />
                    Itinéraire
                </a>
            </div>
        </div>

        <!-- Map Pin Legend -->
        <div v-if="isMapLoaded" class="absolute bottom-4 right-4 z-10 bg-white p-3 rounded-lg shadow-md">
            <p class="font-medium text-gray-800 mb-2">Points de vente</p>
            <div class="flex items-center mb-1">
                <div class="h-3 w-3 rounded-full bg-green-600 mr-2"></div>
                <span class="text-sm">Emplacement actuel</span>
            </div>
            <div class="flex items-center">
                <div class="h-3 w-3 rounded-full bg-gray-400 mr-2"></div>
                <span class="text-sm">Autres emplacements</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.map-container {
    width: 100%;
    height: 100%;
    background: #f5f5f5;
}
</style>
