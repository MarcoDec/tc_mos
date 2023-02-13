import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppModal = defineAsyncComponent<Component>(async () => import('./AppModal.vue'))
