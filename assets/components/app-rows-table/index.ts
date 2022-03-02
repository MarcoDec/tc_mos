import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppRowsTable = defineAsyncComponent<Component>(async () => import('./AppRowsTable.vue'))
