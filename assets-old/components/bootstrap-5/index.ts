import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './modal'
export * from './navbar'
export * from './pagination'

export const AppAlert = defineAsyncComponent<Component>(async () => import('./AppAlert.vue'))
export const AppBadge = defineAsyncComponent<Component>(async () => import('./AppBadge.vue'))
export const AppBtn = defineAsyncComponent<Component>(async () => import('./AppBtn.vue'))