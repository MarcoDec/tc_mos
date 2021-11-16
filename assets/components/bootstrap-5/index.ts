import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './navbar'

export const AppBtn = defineAsyncComponent(async () => import('./AppBtn.vue'))
