import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppModalError = defineAsyncComponent<Component>(async () => import('./AppModalError.vue'))
