import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppManufacturingTableItem = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableItem.vue'))
export const AppManufacturingTableItems = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableItems.vue'))
export const AppManufacturingTableItemField = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableItemField.vue'))
export const AppManufacturingTableItemQuantite = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableItemQuantite.vue'))
