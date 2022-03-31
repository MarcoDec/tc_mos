import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent(async () => import('./AppForm.vue'))
export const AppInputGuesser = defineAsyncComponent(async () => import('./AppInputGuesser.vue'))
