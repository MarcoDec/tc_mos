import {defineAsyncComponent} from 'vue'

export const AppCollectionTableAdd = defineAsyncComponent(async () => import('./AppCollectionTableAdd.vue'))
export const AppCollectionTableField = defineAsyncComponent(async () => import('./AppCollectionTableField.vue'))
export const AppCollectionTableFields = defineAsyncComponent(async () => import('./AppCollectionTableFields.vue'))
export const AppCollectionTableHeaders = defineAsyncComponent(async () => import('./AppCollectionTableHeaders.vue'))
export const AppCollectionTableSearch = defineAsyncComponent(async () => import('./AppCollectionTableSearch.vue'))
