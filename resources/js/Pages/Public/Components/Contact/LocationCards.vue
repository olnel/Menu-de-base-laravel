<script setup>
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

const selectLocation = (location) => {
    emit('update:selectedLocation', location);
};
</script>

<template>
    <div class="container mx-auto px-4 mb-12 mt-16">
        <h2 class="text-3xl font-bold mb-8 text-center text-gray-800 flex items-center justify-center">
            <font-awesome-icon icon="map-marker-alt" class="mr-3 text-green-600" />
            Nos Points de Vente
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div
                v-for="(location, index) in locations"
                :key="index"
                class="bg-white rounded-xl shadow-md p-5 cursor-pointer transition-all duration-300 hover:shadow-xl border-2 relative overflow-hidden group"
                :class="selectedLocation.name === location.name ? 'border-green-600 shadow-lg location-card-active' : 'border-transparent'"
                @click="selectLocation(location)"
            >
                <!-- Status badge -->
                <div
                    class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold"
                    :class="selectedLocation.name === location.name ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                >
                    {{ location.storeType }}
                </div>

                <!-- Indicator line when selected -->
                <div
                    v-if="selectedLocation.name === location.name"
                    class="absolute top-0 left-0 w-1 h-full bg-green-600"
                ></div>

                <div class="flex items-center mb-4">
                    <div class="bg-green-100 p-3 w-10 h-10 flex items-center justify-center rounded-full mr-4 group-hover:bg-green-200 transition-all duration-300">
                        <font-awesome-icon
                            icon="map-marker-alt"
                            :class="selectedLocation.name === location.name ? 'text-green-600' : 'text-green-500'"
                            class="text-xl"
                        />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ location.name }}</h3>
                </div>

                <!-- View on Map button -->
                <div
                    class="mt-4 pt-3 border-t border-gray-100 text-center"
                    :class="selectedLocation.name === location.name ? 'visible' : 'invisible group-hover:visible'"
                >
          <span class="text-green-600 font-medium flex items-center justify-center">
            <font-awesome-icon icon="eye" class="mr-2" />
            {{ selectedLocation.name === location.name ? 'Actuellement affiché' : 'Afficher sur la carte' }}
          </span>
                </div>
            </div>
        </div>
    </div>
</template>
