import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'
export * from './footer'

export const AppTableSplit = defineAsyncComponent<Component>(async () => import('./AppTableSplit.vue'))
