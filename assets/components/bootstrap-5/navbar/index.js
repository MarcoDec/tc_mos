import {defineAsyncComponent} from 'vue'

export const AppDropdownItem = defineAsyncComponent(async () => import('./AppDropdownItem.vue'))
export const AppNavbar = defineAsyncComponent(async () => import('./AppNavbar.vue'))
export const AppNavbarBrand = defineAsyncComponent(async () => import('./AppNavbarBrand.vue'))
export const AppNavbarCollapse = defineAsyncComponent(async () => import('./AppNavbarCollapse'))
export const AppNavbarItem = defineAsyncComponent(async () => import('./AppNavbarItem.vue'))
export const AppNavbarLink = defineAsyncComponent(async () => import('./AppNavbarLink.vue'))
