import {defineAsyncComponent} from 'vue'

export const AppShowGui = defineAsyncComponent(async () => import('./AppShowGui.vue'))
