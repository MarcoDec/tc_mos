import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppManufacturingTableField = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableField.vue'))
export const AppManufacturingTableFields = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableFields.vue'))
export const AppManufacturingTableHeaders = defineAsyncComponent<Component>(async () => import('./AppManufacturingTableHeaders.vue'))
