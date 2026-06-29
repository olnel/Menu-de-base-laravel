import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const LEVELS = ['basic', 'standard', 'advanced', 'premium']

export function usePlan() {
    const page = usePage()

    const plan = computed(() => page.props.auth?.tenant?.plan ?? 'silver')
    const dashboardLevel = computed(() => page.props.auth?.tenant?.dashboard_level ?? 'basic')

    // isAtLeast('standard') → true pour gold, platinum, diamond
    const isAtLeast = (level) => {
        const current = LEVELS.indexOf(dashboardLevel.value)
        const target  = LEVELS.indexOf(level)
        return current >= target
    }

    return { plan, dashboardLevel, isAtLeast }
}
