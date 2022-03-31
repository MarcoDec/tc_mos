import {defineAsyncComponent} from 'vue'

export const AppModal = defineAsyncComponent(async () => import('./AppModal.vue'))
