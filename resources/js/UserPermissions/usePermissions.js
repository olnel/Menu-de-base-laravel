import { computed } from "vue"
import { usePage } from "@inertiajs/vue3"

export default function usePermissions() {
    const user = computed(() => usePage().props.auth.user)

    // Fonction pour normaliser les routes (objet ou tableau → tableau)
    const normalizeRoutes = (routes) => {
        if (!routes) return []
        if (Array.isArray(routes)) return routes
        if (typeof routes === 'object') return Object.values(routes)
        return []
    }

    const can = (routeOrPermission) => {
        if (user.value?.is_dna) return true

        const acceptedRoutes = normalizeRoutes(user.value?.accepted_routes)
        const permissions = normalizeRoutes(user.value?.permissions)

        return acceptedRoutes.includes(routeOrPermission) ||
            permissions.includes(routeOrPermission)
    }

    return { can }
}
