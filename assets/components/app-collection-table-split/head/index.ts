import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTableAdd = defineAsyncComponent<Component>(async () => import('./AppTableAdd.vue'))
export const AppTableField = defineAsyncComponent<Component>(async () => import('./AppTableField.vue'))
export const AppTableFields = defineAsyncComponent<Component>(async () => import('./AppTableFields.vue'))
export const AppTableHeaders = defineAsyncComponent<Component>(async () => import('./AppTableHeaders.vue'))
export const AppTableSearch = defineAsyncComponent<Component>(async () => import('./AppTableSearch.vue'))
