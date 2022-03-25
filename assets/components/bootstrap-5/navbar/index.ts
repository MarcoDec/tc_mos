import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppDropdownItem = defineAsyncComponent<Component>(async () => import('./AppDropdownItem.vue'))
export const AppNavbar = defineAsyncComponent<Component>(async () => import('./AppNavbar.vue'))
export const AppNavbarBrand = defineAsyncComponent<Component>(async () => import('./AppNavbarBrand.vue'))
export const AppNavbarCollapse = defineAsyncComponent(async () => import('./AppNavbarCollapse'))
export const AppNavbarItem = defineAsyncComponent<Component>(async () => import('./AppNavbarItem.vue'))
export const AppNavbarLink = defineAsyncComponent<Component>(async () => import('./AppNavbarLink.vue'))
