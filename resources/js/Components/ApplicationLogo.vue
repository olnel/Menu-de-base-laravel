<script setup>

defineProps({
    responsive: {
        type: Boolean,
        default: false
    }
})

</script>

<template>
    <!--    <img src="/logo.png" alt="Logo" :class="{'hidden lg:block': responsive}" class="w-auto h-14 object-cover"/>-->
    <!--    <img v-if="responsive" src="/logo_mini.png" alt="Logo" class=" lg:hidden" />-->
    <div class="flex items-center justify-center">
        <div class="flex flex-col">
            <span
                class="text-primary font-['Arial'] !font-extrabold text-5xl leading-tight flex items-center">
                T
                <svg class="w-22 h-22 text-primary truck-move" viewBox="0 0 24 24" fill="currentColor">
                            <!-- Corps du camion -->
                            <path d="M3 17h2c0 1.1.9 2 2 2s2-.9 2-2h6c0 1.1.9 2 2 2s2-.9 2-2h2v-2H3v2z"/>
                            <path d="M3 13h12v-2H3v2z"/>
                    <!-- Cabine du camion -->
                            <path d="M16 13h5l-3-4h-2v4z"/>
                            <path d="M3 11h12V8H3v3z"/>
                    <!-- Roues -->
                            <circle cx="7" cy="17" r="1"/>
                            <circle cx="17" cy="17" r="1"/>
                        </svg>
                <span class="text-secondary">M </span>S
            </span>
            <span class="text-primary text-xs font-medium tracking-wide -mt-1.5">Transport Management System</span>
        </div>
    </div>
</template>
