import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppRowsTableItem = defineAsyncComponent<Component>(async () => import('./AppRowsTableItem.vue'))
export const AppRowsTableItems = defineAsyncComponent<Component>(async () => import('./AppRowsTableItems.vue'))
export const AppRowsTableItemField = defineAsyncComponent<Component>(async () => import('./AppRowsTableItemField.vue'))
export const AppRowsTableItemInput = defineAsyncComponent<Component>(async () => import('./AppRowsTableItemInput.vue'))
