import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppRouterLink = defineAsyncComponent<Component>(async () => import('./AppRouterLink.vue'))
