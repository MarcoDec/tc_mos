import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './modal'
export * from './navbar'
export * from './tab'

export const AppAlert = defineAsyncComponent(async () => import('./AppAlert.vue'))
export const AppBadge = defineAsyncComponent(async () => import('./AppBadge.vue'))
export const AppBtn = defineAsyncComponent(async () => import('./AppBtn.vue'))
