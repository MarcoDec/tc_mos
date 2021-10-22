import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export const AppTopNavbar = defineAsyncComponent<Component>(async () => import('./AppTopNavbar.vue'))
export const AppTopNavbarUser = defineAsyncComponent<Component>(async () => import('./AppTopNavbarUser.vue'))
