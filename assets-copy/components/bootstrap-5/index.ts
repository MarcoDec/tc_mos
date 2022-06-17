import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './navbar'
export * from './pagination'

export const AppBtn = defineAsyncComponent<Component>(async () => import('./AppBtn.vue'))
