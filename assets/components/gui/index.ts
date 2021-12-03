import {defineAsyncComponent} from 'vue'

export const AppShowGui = defineAsyncComponent(async () => import('./AppShowGui.vue'))
export const AppShowGuiCard = defineAsyncComponent(async () => import('./AppShowGuiCard.vue'))
