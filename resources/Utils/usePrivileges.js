import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export function usePrivileges() {
    const page = usePage();

    // Accéder aux privilèges
    const userPrivilegeKeys = computed(() => page.props.privileges?.keys || []);

    // Accéder aux routes acceptées
    const userAcceptedRoutes = computed(
        () => page.props.privileges?.routes || []
    );

    //Vérifie si l'utilisateur a un privilège spécifique.
    const hasPrivilege = (privilege) => {
        return userPrivilegeKeys.value.includes(privilege);
    };

    // Vérifie si l'utilisateur peut accéder à une route spécifique.
    const canAccessRoute = (routeName) => {
        return userAcceptedRoutes.value.includes(routeName);
    };

    // Vous pouvez toujours ajouter des raccourcis pour l'utilisateur
    const user = computed(() => page.props.auth?.user);
    return {
        user,
        hasPrivilege,
        canAccessRoute,
        userPrivilegeKeys,
        userAcceptedRoutes,
    };
}
