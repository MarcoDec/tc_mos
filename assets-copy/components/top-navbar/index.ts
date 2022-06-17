import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTopNavbar = defineAsyncComponent<Component>(async () => import('./AppTopNavbar.vue'))
