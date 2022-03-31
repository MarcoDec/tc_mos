import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent(async () => import('./AppForm.vue'))
export const AppFormGroup = defineAsyncComponent(async () => import('./AppFormGroup.vue'))
export const AppInput = defineAsyncComponent(async () => import('./AppInput.vue'))
export const AppInputGuesser = defineAsyncComponent(async () => import('./AppInputGuesser.vue'))
export const AppInvalidFeedback = defineAsyncComponent(async () => import('./AppInvalidFeedback'))
export const AppLabel = defineAsyncComponent(async () => import('./AppLabel.vue'))
export const AppRadio = defineAsyncComponent(async () => import('./AppRadio'))
export const AppRadioGroup = defineAsyncComponent(async () => import('./AppRadioGroup.vue'))
export const AppSearchBool = defineAsyncComponent(async () => import('./AppSearchBool.vue'))
export const AppSelect = defineAsyncComponent(async () => import('./AppSelect.vue'))
export const AppSelectOption = defineAsyncComponent(async () => import('./AppSelectOption.vue'))
export const AppSwitch = defineAsyncComponent(async () => import('./AppSwitch.vue'))
