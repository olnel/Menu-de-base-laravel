<script setup>
import {ref, onMounted, nextTick, computed, onUnmounted, onBeforeUnmount} from "vue";
import gsap from "gsap";
import {ScrollTrigger} from "gsap/ScrollTrigger";
import {ScrollToPlugin} from "gsap/ScrollToPlugin";
import {TextPlugin} from "gsap/TextPlugin";
import ButtonAnimation from "@/Pages/Public/Components/Button/ButtonAnimation.vue";


const props = defineProps({
    data: {
        type: Object,
        default: () => {}
    }
})

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin, TextPlugin);

const carousel = ref(null);
const currentSlide = ref(0);
const heroSection = ref(null);
const isAnimating = ref(false);
const windowWidth = ref(window.innerWidth);
const scrollTriggers = ref([]);
const animations = ref([]);



// Responsive variables
const isMobile = computed(() => windowWidth.value < 640);
const isTablet = computed(() => windowWidth.value >= 640 && windowWidth.value < 1024);

// Update window width on resize
const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};


// Slide data array
const slides = [
    {
        title: "COLLECTIONS MODERNES",
        subtitle: "Maillot de bain L",
        description: "Moderne et élégant pour vos femmes.",
        discount: "Soldes jusqu'à 3% de réduction",
        imageUrl: "/img/slider-1.jpg",

    },
    {
        title: "COLLECTIONS EXCLUSIVES",
        subtitle: "Maillot de bain M",
        description: "Confortable et tendance pour vos moments de détente.",
        discount: "Soldes jusqu'à 5% de réduction",
        imageUrl: "/img/slider-1.jpg",

    },
    {
        title: "COLLECTIONS PREMIUM",
        subtitle: "Maillot de bain XL",
        description: "Élégant et luxueux pour les occasions spéciales.",
        discount: "Soldes jusqu'à 10% de réduction",
        imageUrl: "/img/slider-1.jpg",

    },
];

const onSlideChange = (current) => {
    currentSlide.value = current;

    // Prevent animation overlap
    if (isAnimating.value) return;
    isAnimating.value = true;

    nextTick(() => {
        animateSlideContent(current);
        animateGradient(current);
    });
};

const prevSlide = () => {
    if (!isAnimating.value && carousel.value) {
        const prevBtn = document.querySelector(".prev-btn");
        if (prevBtn) {
            prevBtn.classList.add('button-pressed');
            setTimeout(() => {
                prevBtn.classList.remove('button-pressed');
                if (carousel.value) carousel.value.prev();
            }, 200);
        } else if (carousel.value) {
            carousel.value.prev();
        }
    }
};

const nextSlide = () => {
    if (!isAnimating.value && carousel.value) {
        const nextBtn = document.querySelector(".next-btn");
        if (nextBtn) {
            nextBtn.classList.add('button-pressed');
            setTimeout(() => {
                nextBtn.classList.remove('button-pressed');
                if (carousel.value) carousel.value.next();
            }, 200);
        } else if (carousel.value) {
            carousel.value.next();
        }
    }
};

// Safe animation function with tracking for cleanup
const trackAnimation = (animation) => {
    animations.value.push(animation);
    return animation;
};

// Initialize GSAP animations with safer approaches
onMounted(() => {
    // Listen for window resize
    window.addEventListener('resize', updateWindowWidth);

    // Use setTimeout to ensure DOM is ready
    setTimeout(() => {
        initializeAnimations();

        // Initialize first slide with a slight delay
        setTimeout(() => {
            if (!isAnimating.value) {
                animateSlideContent(0);
                animateGradient(0);
            }
        }, 300);
    }, 200);
});

const initializeAnimations = () => {
    try {
        if (!heroSection.value) return;

        // DOM elements
        const heroBg = document.querySelector(".hero-bg");
        const gradientOverlay = document.querySelector(".gradient-overlay");
        const scrollAnimatedElements = document.querySelectorAll('.scroll-animated');

        // Clear any existing ScrollTriggers before creating new ones
        cleanupScrollTriggers();

        if (heroBg && gradientOverlay) {
            // Background and gradient animations without scaling effect
            trackAnimation(
                gsap.fromTo(heroBg,
                    {opacity: 0},
                    {opacity: 1, duration: 1.2}
                )
            );

            trackAnimation(
                gsap.fromTo(gradientOverlay,
                    {opacity: 0},
                    {opacity: 1, duration: 1}
                )
            );
        }

        // Only set up ScrollTrigger if we have elements to animate
        if (scrollAnimatedElements.length > 0) {
            scrollAnimatedElements.forEach(section => {
                if (section) {
                    const trigger = ScrollTrigger.create({
                        trigger: section,
                        start: "top 80%",
                        end: "bottom 20%",
                        onEnter: () => {
                            trackAnimation(
                                gsap.fromTo(section,
                                    {y: 50, opacity: 0},
                                    {y: 0, opacity: 1, duration: 0.8, ease: "power2.out"}
                                )
                            );
                        },
                        onLeaveBack: () => {
                            trackAnimation(
                                gsap.to(section, {opacity: 0, y: 50, duration: 0.8})
                            );
                        }
                    });

                    scrollTriggers.value.push(trigger);
                }
            });
        }

        // Simple parallax effect without scaling
        if (heroBg) {
            window.addEventListener('scroll', () => {
                const scrollPos = window.scrollY;
                const viewportHeight = window.innerHeight;
                if (heroSection.value) {
                    const heroTop = heroSection.value.getBoundingClientRect().top + window.scrollY;
                    const heroHeight = heroSection.value.offsetHeight;

                    if (scrollPos >= heroTop - viewportHeight && scrollPos <= heroTop + heroHeight) {
                        const progress = (scrollPos - (heroTop - viewportHeight)) / (heroHeight + viewportHeight);
                        heroBg.style.transform = `translateY(${0}px)`;
                    }
                }
            });
        }
    } catch (error) {
        console.error("Error initializing animations:", error);
    }
};

// Gradient animation for middle section - without GSAP for better performance
const animateGradient = (slideIndex) => {
    const gradientOverlay = document.querySelector('.gradient-overlay');
    if (!gradientOverlay) return;

    // Different gradient positions for each slide
    const positions = [
        {x: '64.87%', y: '46.18%'},
        {x: '50%', y: '50%'},
        {x: '35%', y: '45%'}
    ];

    gradientOverlay.style.background =
        `radial-gradient(ellipse 55.83% 112.01% at ${positions[slideIndex].x} ${positions[slideIndex].y}, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.84) 100%)`;

    gradientOverlay.classList.add('pulse-gradient');
    setTimeout(() => {
        if (gradientOverlay) {
            gradientOverlay.classList.remove('pulse-gradient');
        }
    }, 2000);
};

// Professional slide content animation - with safer GSAP implementation
const animateSlideContent = (slideIndex) => {
    try {
        // All slide contents
        const slideContents = document.querySelectorAll('.slide-content');
        if (slideContents.length === 0) {
            isAnimating.value = false;
            return;
        }

        // Current slide content
        const currentContent = slideContents[slideIndex];
        if (!currentContent) {
            isAnimating.value = false;
            return;
        }

        // Reset opacity for all slides first
        slideContents.forEach(content => {
            if (content) content.style.opacity = "0";
        });

        // Make current slide visible first
        currentContent.style.opacity = "1";

        // Collect all elements we want to animate
        const elements = [
            {el: currentContent.querySelector('.slide-title'), delay: 0, y: 30},
            {el: currentContent.querySelector('.slide-subtitle'), delay: 0.2, y: 20},
            {el: currentContent.querySelector('.slide-description'), delay: 0.4, y: 20},
            {el: currentContent.querySelector('.slide-discount'), delay: 0.6, y: 20},
            {el: currentContent.querySelector('.button-container'), delay: 0.8, y: 15}
        ].filter(item => item.el); // Filter out null elements

        // Animate each element individually for better control
        elements.forEach(item => {
            // Set initial state
            gsap.set(item.el, {opacity: 0, y: item.y});

            // Animate in
            trackAnimation(
                gsap.to(item.el, {
                    opacity: 1,
                    y: 0,
                    duration: 0.7,
                    delay: item.delay,
                    ease: "power3.out",
                    onComplete: () => {
                        if (item === elements[elements.length - 1]) {
                            isAnimating.value = false;
                        }
                    }
                })
            );
        });

        // If no elements were found, make sure we reset isAnimating
        if (elements.length === 0) {
            isAnimating.value = false;
        }
    } catch (error) {
        console.error("Error in animateSlideContent:", error);
        isAnimating.value = false;
    }
};

// Safely clean up ScrollTriggers
const cleanupScrollTriggers = () => {
    try {
        scrollTriggers.value.forEach(trigger => {
            if (trigger && trigger.kill) {
                trigger.kill();
            }
        });
        scrollTriggers.value = [];
    } catch (error) {
        console.error("Error cleaning up ScrollTriggers:", error);
    }
};

// Safely clean up GSAP animations
const cleanupAnimations = () => {
    try {
        animations.value.forEach(animation => {
            if (animation && animation.kill) {
                animation.kill();
            }
        });
        animations.value = [];
    } catch (error) {
        console.error("Error cleaning up animations:", error);
    }
};

// Clean up resources before component is unmounted
onBeforeUnmount(() => {
    cleanupScrollTriggers();
    cleanupAnimations();
});

// Clean up everything when component is unmounted
onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth);
    cleanupScrollTriggers();
    cleanupAnimations();
    window.onscroll = null;
});
</script>

<template>
    <!-- Hero Carousel -->
    <div
        ref="heroSection"
        class="relative overflow-hidden bg-black min-h-[400px] sm:min-h-[500px] md:min-h-[600px] lg:min-h-[800px] scroll-animated"
    >
        <img class="w-16 h-16 md:w-32 md:h-32 absolute -top-6 md:-top-12 left-20 z-10 animate-bamboo" src="/img/hero/bamboo.png"  alt=""/>
        <img class="w-10 h-10 md:w-24 md:h-28 absolute bottom-3 md:bottom-24 right-3 md:right-48 shadow-[-2px_5px_10px_0px_rgba(0,0,0,0.05)] z-10 animate-fueille" src="/img/hero/fueille.png"  alt=""/>
        <a-carousel
            :autoplay="true"
            effect="fade"
            :afterChange="onSlideChange"
            ref="carousel"
            class="hero-carousel"
            :dots="false"
        >
            <div
                v-for="(slide, index) in data"
                :key="index"
                class="carousel-slide relative min-h-[400px] sm:min-h-[500px] md:min-h-[600px] lg:min-h-[800px]"
            >
                <div
                    :style="{ backgroundImage: `url(${slide.background_image})` }"
                    class="absolute inset-0 bg-cover bg-center hero-bg transition-all duration-500 brightness-110 md:brightness-125 contrast-100"
                >

                    <div class="absolute inset-0 gradient-overlay"></div>
                    <div class="container mx-auto h-full flex items-center px-4 sm:px-6 md:px-8">
                        <div
                            class="relative text-white max-w-full sm:max-w-md md:max-w-2xl slide-content"
                            :class="{
                                'px-4': isMobile,
                                'px-6': isTablet
                            }"
                        >
                            <div
                                class="text-white uppercase tracking-wider mb-1 sm:mb-2 text-xs sm:text-sm md:text-base slide-title"
                            >
                                {{ slide.title }}
                            </div>
                            <h2
                                class="text-primary font-['Prata'] text-3xl sm:text-3xl md:text-4xl lg:text-7xl font-bold mb-1 sm:mb-2 slide-subtitle"
                            >
                                {{ slide.subtitle }}
                            </h2>
                            <div
                                class="text-2xl sm:text-xl leading-normal md:text-2xl lg:text-6xl font-['Prata'] font-thin mb-2 sm:mb-4 md:mb-6 slide-description"
                            >
                                {{ slide.description }}
                            </div>

<!--                            <div class="mb-3 sm:mb-4 md:mb-8">
                                <h3 class="relative text-base sm:text-lg md:text-xl lg:text-2xl mb-1 md:mb-2 slide-discount">
                                    {{ slide.discount }}

                                </h3>
                                &lt;!&ndash;                                <p class="text-gray-200 text-xs sm:text-sm md:text-base slide-discount">&ndash;&gt;
                                &lt;!&ndash;                                    Livraison gratuite sur toutes vos commandes, nous livrons, vous profitez&ndash;&gt;
                                &lt;!&ndash;                                </p>&ndash;&gt;
                            </div>-->

                            <div class="relative button-container">
                                <ButtonAnimation
                                    :label="slide.button_label"
                                    icon="fa-solid fa-arrow-right"
                                    backgroundColor="!bg-yellow-500"
                                    hoverColor="hover:!bg-yellow-400"
                                    borderColor="!border-yellow-500"
                                    textColor="!text-black"
                                    class="transform transition-transform hover:scale-105"
                                />
                                <img class="w-8 h-8 absolute -top-9 left-20 rotate-180 blur-[1px] z-10 animate-float" src="/img/hero/fueille.png"  alt=""/>
                                <img class="w-8 h-16 md:w-20 md:h-28 absolute -bottom-20 -left-8 md:-left-20 z-10 animate-float" src="/img/hero/raphia_hero.png"  alt=""/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a-carousel>

        <a-button
            type="text"
            shape="circle"
            size="middle"
            @click="prevSlide"
            class="group absolute left-1 sm:left-2 md:left-4 top-1/2 -translate-y-1/2 bg-white/30 rounded-full flex items-center justify-center hover:!bg-white hover:scale-110 hover:shadow-lg transition-all duration-300 ease-in-out prev-btn"
        >
            <font-awesome-icon
                icon="fa-solid fa-arrow-left"
                class="text-white/30 group-hover:text-green-500 text-xs sm:text-sm md:text-base"
            />
        </a-button>

        <a-button
            type="text"
            shape="circle"
            size="middle"
            @click="nextSlide"
            class="group absolute right-1 sm:right-2 md:right-4 top-1/2 -translate-y-1/2 bg-white/30 rounded-full flex items-center justify-center hover:!bg-white hover:scale-110 hover:shadow-lg transition-all duration-300 ease-in-out next-btn"
        >
            <font-awesome-icon
                icon="fa-solid fa-arrow-right"
                class="text-white/30 group-hover:text-green-600 text-xs sm:text-sm md:text-base"
            />
        </a-button>

        <!-- Slide Indicators - Custom implementation for better control -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center">
            <div class="flex space-x-2">
                <button
                    v-for="(_, index) in slides"
                    :key="index"
                    @click="() => carousel.value && carousel.value.goTo(index)"
                    class="w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all duration-300"
                    :class="currentSlide === index ? 'bg-green-500 scale-125' : 'bg-white/30 hover:bg-white/80'"
                ></button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Enhanced hover effects */
.group:hover .fa-arrow-left,
.group:hover .fa-arrow-right {
    transform: scale(1.2);
    transition: transform 0.3s ease;
}

/* Add smooth transitions for all animations */
.carousel-slide {
    perspective: 1000px;
}

.hero-bg {
    transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    will-change: transform;
    background-position: 50% 5%;
}

.gradient-overlay {
    background: radial-gradient(ellipse 55.83% 112.01% at 64.87% 46.18%, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.84) 100%);
    transition: background 1.5s ease-in-out, opacity 0.8s ease-in-out;
    will-change: background, opacity;
}

/* Pulse animation using CSS instead of GSAP for better performance */
.pulse-gradient {
    animation: pulse-opacity 2s ease-in-out;
}

@keyframes pulse-opacity {
    0% {
        opacity: 0.85;
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0.85;
    }
}

/* Button press animation using CSS instead of GSAP */
.button-pressed {
    transform: scale(0.8);
    transition: transform 0.2s ease;
}

/* Responsive adjustments for mobile */
@media (max-width: 639px) {
    .slide-content {
        width: 90%;
        margin: 0 auto;
    }

    .button-container {
        transform-origin: left;
    }
}

/* Ant Design carousel customization */
:deep(.ant-carousel .slick-dots) {
    bottom: 12px;
}

:deep(.ant-carousel .slick-dots li button) {
    background: rgba(255, 255, 255, 0.5);
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

:deep(.ant-carousel .slick-dots li.slick-active button) {
    background: #10B981; /* Tailwind green-500 */
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(180deg);
    }
    50% {
        transform: translateY(10px) rotate(182deg);
    }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
    }
    50% {
        transform: translateY(10px) rotate(3deg);
        opacity: 0.8;
    }
}

.animate-bamboo {
    animation: float 6s ease-in-out infinite;
}

@keyframes sway {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(5px) rotate(1deg);
    }
}

.animate-fueille {
    animation: sway 5s ease-in-out infinite;
}
</style>
