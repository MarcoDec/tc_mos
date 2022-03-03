import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppCollectionTable = defineAsyncComponent<Component>(async () => import('./AppCollectionTable.vue'))
