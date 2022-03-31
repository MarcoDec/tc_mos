import {defineAsyncComponent} from 'vue'

export * from './app-collection-table'
export * from './bootstrap-5'
export * from './modal'
export * from './top-navbar'
export * from './tree'
export * from './vue-router'

export const Fa = defineAsyncComponent(async () => import('./Fa.vue'))
