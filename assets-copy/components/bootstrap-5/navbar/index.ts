import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppNavbarComputed = {navClass: () => string}
type AppNavbarProps = {variant?: string}
type AppNavbarComponent = Component<AppNavbarProps, never, never, AppNavbarComputed>
export const AppNavbar = defineAsyncComponent<AppNavbarComponent>(async () => import('./AppNavbar.vue'))

type AppNavbarBrandProps = {href: string, title: string}
type AppNavbarBrandComponent = Component<AppNavbarBrandProps>
export const AppNavbarBrand = defineAsyncComponent<AppNavbarBrandComponent>(async () => import('./AppNavbarBrand.vue'))
