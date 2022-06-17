import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppCollectionTableItem = defineAsyncComponent<Component>(async () => import('./AppCollectionTableItem.vue'))
export const AppCollectionTableItemField = defineAsyncComponent<Component>(async () => import('./AppCollectionTableItemField.vue'))
export const AppCollectionTableItemInput = defineAsyncComponent<Component>(async () => import('./AppCollectionTableItemInput.vue'))
export const AppCollectionTableItems = defineAsyncComponent<Component>(async () => import('./AppCollectionTableItems.vue'))
