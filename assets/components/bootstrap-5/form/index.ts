import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent(async () => import('./AppForm'))
export const AppFormGroup = defineAsyncComponent(async () => import('./AppFormGroup'))
export const AppInput = defineAsyncComponent(async () => import('./AppInput'))
export const AppLabel = defineAsyncComponent(async () => import('./AppLabel.vue'))
