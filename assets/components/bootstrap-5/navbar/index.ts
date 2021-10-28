import {defineAsyncComponent} from 'vue'

export const AppNavbar = defineAsyncComponent(async () => import('./AppNavbar.vue'))
export const AppNavbarBrand = defineAsyncComponent(async () => import('./AppNavbarBrand.vue'))
