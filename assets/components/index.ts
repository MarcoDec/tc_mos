import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export * from './bootstrap-5'
export * from './top-navbar'

export const AppModalError = defineAsyncComponent<Component>(async () => import('./AppModalError.vue'))
