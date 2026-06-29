import {usePage} from "@inertiajs/vue3";

export function role(...roles) {
    const page = usePage();
    return roles.some(element => page.props.auth.roles?.includes(element))
}
