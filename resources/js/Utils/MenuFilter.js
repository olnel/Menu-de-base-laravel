export function filterMenuByPrivileges(items, privileges, isDna = false, modules = []) {
    if (!items || !items.length) return [];

    return items.filter((item) => {
        if (item.module && !modules.includes(item.module)) {
            return false;
        }
        if (isDna) return true;
        if (!item.routeName) return true;
        return privileges.includes(item.routeName);
    });
}
