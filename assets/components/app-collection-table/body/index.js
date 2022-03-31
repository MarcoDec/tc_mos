import {defineAsyncComponent} from 'vue'

export const AppCollectionTableItem = defineAsyncComponent(async () => import('./AppCollectionTableItem.vue'))
export const AppCollectionTableItemField = defineAsyncComponent(async () => import('./AppCollectionTableItemField.vue'))
export const AppCollectionTableItemInput = defineAsyncComponent(async () => import('./AppCollectionTableItemInput.vue'))
export const AppCollectionTableItems = defineAsyncComponent(async () => import('./AppCollectionTableItems.vue'))
