import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTableFooter = defineAsyncComponent<Component>(async () => import('./AppTableFooter.vue'))

