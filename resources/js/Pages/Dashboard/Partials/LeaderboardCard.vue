<script setup>
defineProps({
    title: String,
    items: Array,
    icon: String
});
</script>

<template>
    <a-card :bordered="false" class="bg-white main-rounded main-shadow w-full">
        <div class="flex items-center gap-3 mb-4">
            <font-awesome-icon :icon="icon" class="w-6 h-6 text-yellow-500" />
            <h2 class="text-xl font-bold">{{ title }}</h2>
        </div>
        <div class="space-y-3">
            <div
                v-for="(item, index) in items"
                :key="item.tabel"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
            >
                <div class="flex items-center gap-3">
          <span
              :class="`w-6 h-6 flex items-center justify-center rounded-full ${
              index === 0 ? 'bg-yellow-500' :
              index === 1 ? 'bg-gray-400' :
              index === 2 ? 'bg-amber-600' :
              'bg-gray-200'
            } text-white font-bold`"
          >
            {{ index + 1 }}
          </span>
                    <span class="font-medium">{{ item.label }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-text-primary/70" v-if="item.desc">{{ item.desc }}</span>
                    <span class="font-semibold text-primary">{{ item.value }}</span>
                </div>
            </div>
        </div>
    </a-card>
</template>
