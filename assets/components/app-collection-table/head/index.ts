import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppCollectionTableAdd = defineAsyncComponent<Component>(async () => import('./AppCollectionTableAdd.vue'))
export const AppCollectionTableFields = defineAsyncComponent<Component>(async () => import('./AppCollectionTableFields.vue'))
export const AppCollectionTableHeaders = defineAsyncComponent<Component>(async () => import('./AppCollectionTableHeaders.vue'))
export const AppCollectionTableSearch = defineAsyncComponent<Component>(async () => import('./AppCollectionTableSearch.vue'))
