import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppNavbar = defineAsyncComponent<Component>(async () => import('./AppNavbar.vue'))
export const AppNavbarBrand = defineAsyncComponent<Component>(async () => import('./AppNavbarBrand.vue'))
