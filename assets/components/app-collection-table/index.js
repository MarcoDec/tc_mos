import {defineAsyncComponent} from 'vue'

export * from './body'
export * from './head'

export const AppCollectionTable = defineAsyncComponent(async () => import('./AppCollectionTable.vue'))
