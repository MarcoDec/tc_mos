import {defineAsyncComponent} from 'vue'

export const AppTreeRow = defineAsyncComponent(async () => import('./AppTreeRow.vue'))
