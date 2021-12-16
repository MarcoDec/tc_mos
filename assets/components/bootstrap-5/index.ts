import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './navbar'

export const AppBtn = defineAsyncComponent<Component>(async () => import('./AppBtn.vue'))
