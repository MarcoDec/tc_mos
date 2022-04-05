import {defineAsyncComponent} from 'vue'

export const AppTab = defineAsyncComponent(async () => import('./AppTab.vue'))
export const AppTabs = defineAsyncComponent(async () => import('./AppTabs.vue'))
