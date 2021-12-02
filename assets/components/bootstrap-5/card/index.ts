import {defineAsyncComponent} from 'vue'

export const AppCard = defineAsyncComponent(async () => import('./AppCard.vue'))
