import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppScheduleTable = defineAsyncComponent<Component>(async () => import('./AppScheduleTable.vue'))
