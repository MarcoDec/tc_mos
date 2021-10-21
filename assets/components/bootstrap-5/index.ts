import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './modal'
export * from './navbar'

export const AppAlert = defineAsyncComponent<Component>(async () => import('./AppAlert.vue'))
export const AppBtn = defineAsyncComponent<Component>(async () => import('./AppBtn.vue'))
