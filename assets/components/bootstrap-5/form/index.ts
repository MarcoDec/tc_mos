import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent(async () => import('./AppForm.vue'))
export const AppFormGroup = defineAsyncComponent(async () => import('./AppFormGroup.vue'))
export const AppInput = defineAsyncComponent(async () => import('./AppInput.vue'))
export const AppLabel = defineAsyncComponent(async () => import('./AppLabel.vue'))
