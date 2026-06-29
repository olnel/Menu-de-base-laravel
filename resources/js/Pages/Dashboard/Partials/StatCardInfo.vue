<template>
    <a-card
        :bordered="false"
        :class="[
            'group relative overflow-hidden transition-transform duration-500 ease-in-out hover:-translate-y-0.5 hover:shadow-lg',
            cardBgClass,
        ]"
    >
        <div class="relative z-10">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="min-w-0">
                    <p
                        :class="[
                            'text-base uppercase tracking-widest opacity-70 font-medium',
                            textColorClass,
                        ]"
                    >
                        {{ subTitle }}
                    </p>
                    <p
                        :class="[
                            'text-4xl md:text-5xl font-semibold mt-1 tracking-tight leading-none',
                            mainValueColorClass,
                        ]"
                    >
                        {{ displayValue }}
                    </p>
                </div>
                <div v-if="icon" class="rounded-xl p-2" :class="iconBgClass">
                    <font-awesome-icon
                        :icon="icon"
                        :class="[
                            'h-12 w-12 relative z-10 drop-shadow-sm ',
                            iconColorClass,
                        ]"
                    />
                </div>
            </div>

            <!-- Divider + details -->
            <template v-if="details && details.length">
                <a-divider class="my-4" />
                <div class="text-sm" :class="detailTextColorClass">
                    <p
                        v-for="(detail, index) in details"
                        :key="index"
                        class="flex items-center justify-between py-2"
                    >
                        <span class="opacity-80 truncate uppercase"
                            >{{ detail.label }}</span
                        >
                        <span class="font-bold text-base text-gray-500">{{
                            detail.value
                        }}</span>
                    </p>
                </div>
            </template>
        </div>
    </a-card>
</template>

<script setup>
import { defineProps, onBeforeUnmount, ref, watchEffect } from "vue";

const props = defineProps({
    subTitle: String,
    value: [String, Number],
    details: { type: Array, default: () => [] },
    textColorClass: { type: String, default: "text-gray-800" },
    mainValueColorClass: { type: String, default: "text-gray-700" },
    detailTextColorClass: { type: String, default: "text-gray-600" },
    icon: String,
    iconBgClass: { type: String, default: "bg-white/60" },
    iconColorClass: { type: String, default: "text-gray-700" },
    cardBgClass: { type: String, default: "bg-white/80 backdrop-blur-md" },
});

const displayValue = ref(0);
let animationFrameId = null;

// easing réutilisable
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

function animateValue(start, end, duration = 900) {
    cancelAnimationFrame(animationFrameId); // stoppe l’ancienne animation si elle existe
    const startTimestamp = performance.now();
    const startVal = Number(start) || 0;
    const endVal = Number(end) || 0;
    const diff = endVal - startVal;

    function step(now) {
        const progress = Math.min(1, (now - startTimestamp) / duration);
        displayValue.value = Math.max(
            0,
            Math.round(startVal + diff * easeOutCubic(progress))
        );

        if (progress < 1) {
            animationFrameId = requestAnimationFrame(step);
        }
    }

    animationFrameId = requestAnimationFrame(step);
}

// lance / relance l’animation dès que props.value change
watchEffect(() => {
    animateValue(displayValue.value, props.value);
});

// nettoyage
onBeforeUnmount(() => {
    cancelAnimationFrame(animationFrameId);
});
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: all 0.8s ease;
}
.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateY(3px);
}
</style>
