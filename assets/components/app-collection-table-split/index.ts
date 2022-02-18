import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppTableSplit = defineAsyncComponent<Component>(async () => import('./AppTableSplit.vue'))
