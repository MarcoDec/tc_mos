import {defineAsyncComponent} from 'vue'

export const AppCol = defineAsyncComponent(async () => import('./AppCol.vue'))
export const AppContainer = defineAsyncComponent(async () => import('./AppContainer.vue'))
export const AppRow = defineAsyncComponent(async () => import('./AppRow.vue'))
