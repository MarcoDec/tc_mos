import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export const AppModal = defineAsyncComponent<Component>(async () => import('./AppModal.vue'))
