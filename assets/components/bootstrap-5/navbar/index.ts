import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export const AppNavbar = defineAsyncComponent<Component>(async () => import('./AppNavbar.vue'))
export const AppNavbarBrand = defineAsyncComponent<Component>(async () => import('./AppNavbarBrand.vue'))
export const AppNavbarCollapse = defineAsyncComponent<Component>(async () => import('./AppNavbarCollapse'))
export const AppNavbarNav = defineAsyncComponent<Component>(async () => import('./AppNavbarNav'))
export const AppNavbarText = defineAsyncComponent<Component>(async () => import('./AppNavbarText'))
