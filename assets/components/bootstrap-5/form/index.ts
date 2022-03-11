import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent<Component>(async () => import('./AppForm.vue'))
export const AppFormGroup = defineAsyncComponent<Component>(async () => import('./AppFormGroup.vue'))
export const AppInput = defineAsyncComponent<Component>(async () => import('./AppInput.vue'))
export const AppInputGuesser = defineAsyncComponent<Component>(async () => import('./AppInputGuesser.vue'))
export const AppInvalidFeedback = defineAsyncComponent<Component>(async () => import('./AppInvalidFeedback'))
export const AppLabel = defineAsyncComponent<Component>(async () => import('./AppLabel.vue'))
export const AppRadio = defineAsyncComponent<Component>(async () => import('./AppRadio'))
export const AppRadioGroup = defineAsyncComponent<Component>(async () => import('./AppRadioGroup.vue'))
export const AppSearchBool = defineAsyncComponent<Component>(async () => import('./AppSearchBool.vue'))
export const AppSelect = defineAsyncComponent<Component>(async () => import('./AppSelect.vue'))
export const AppSelectOption = defineAsyncComponent<Component>(async () => import('./AppSelectOption.vue'))
export const AppSwitch = defineAsyncComponent<Component>(async () => import('./AppSwitch.vue'))
export const AppTrafficLight = defineAsyncComponent<Component>(async () => import('./AppTrafficLight.vue'))
export const AppPhoneFlag = defineAsyncComponent<Component>(async () => import('./AppPhoneFlag.vue'))
