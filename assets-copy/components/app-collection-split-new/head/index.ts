import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTableHeaders = defineAsyncComponent<Component>(async () => import('./AppTableHeaders.vue'))
export const AppTableFields = defineAsyncComponent<Component>(async () => import('./AppTableFields.vue'))
export const AppTableField = defineAsyncComponent<Component>(async () => import('./AppTableField.vue'))

