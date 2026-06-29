import {reactive} from 'vue';
import {router} from '@inertiajs/vue3';

export const createSearchFilter = () => {
    return reactive({
        search: '',
    });
};

export const refreshData = () => {
    const filter = createSearchFilter();
    filter.search = '';
    router.reload({
        data: {search: ''},
        only: ['data'],
        preserveScroll: true,
        preserveState: true,
    });
};

export const gotoSearch = (filter, url, only = ['data']) => {
    router.visit(url, {
        data: filter,
        only: only,
        preserveState: true,
        preserveScroll: true,
    });
};


