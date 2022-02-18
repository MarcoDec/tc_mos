import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTableItem = defineAsyncComponent<Component>(async () => import('./AppTableItem.vue'))
export const AppTableItemField = defineAsyncComponent<Component>(async () => import('./AppTableItemField.vue'))
export const AppTableItemInput = defineAsyncComponent<Component>(async () => import('./AppTableItemInput.vue'))
export const AppTableItems = defineAsyncComponent<Component>(async () => import('./AppTableItems.vue'))
