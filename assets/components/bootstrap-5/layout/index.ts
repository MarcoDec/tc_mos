import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export const AppCol = defineAsyncComponent<Component>(async () => import('./AppCol.vue'))
export const AppContainer = defineAsyncComponent<Component>(async () => import('./AppContainer.vue'))
export const AppRow = defineAsyncComponent<Component>(async () => import('./AppRow.vue'))
