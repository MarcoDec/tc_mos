import {defineAsyncComponent} from 'vue'

export const AppPagination = defineAsyncComponent(async () => import('./AppPagination.vue'))
export const AppPaginationItem = defineAsyncComponent(async () => import('./AppPaginationItem'))
