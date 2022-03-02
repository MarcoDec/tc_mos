import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppRowsTableAdd = defineAsyncComponent<Component>(async () => import('./AppRowsTableAdd.vue'))
export const AppRowsTableField = defineAsyncComponent<Component>(async () => import('./AppRowsTableField.vue'))
export const AppRowsTableFields = defineAsyncComponent<Component>(async () => import('./AppRowsTableFields.vue'))
export const AppRowsTableHeaders = defineAsyncComponent<Component>(async () => import('./AppRowsTableHeaders.vue'))
export const AppRowsTableSearch = defineAsyncComponent<Component>(async () => import('./AppRowsTableSearch.vue'))
