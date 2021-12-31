import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppForm = defineAsyncComponent<Component>(async () => import('./AppForm.vue'))
export const AppFormGroup = defineAsyncComponent<Component>(async () => import('./AppFormGroup.vue'))
export const AppInput = defineAsyncComponent<Component>(async () => import('./AppInput.vue'))
export const AppLabel = defineAsyncComponent<Component>(async () => import('./AppLabel.vue'))
