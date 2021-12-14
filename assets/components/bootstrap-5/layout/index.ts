import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppCol = defineAsyncComponent<Component>(async () => import('./AppCol.vue'))
export const AppContainer = defineAsyncComponent<Component>(async () => import('./AppContainer.vue'))
export const AppRow = defineAsyncComponent(async () => import('./AppRow'))
