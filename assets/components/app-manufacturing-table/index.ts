import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppManufacturingTable = defineAsyncComponent<Component>(async () => import('./AppManufacturingTable.vue'))
