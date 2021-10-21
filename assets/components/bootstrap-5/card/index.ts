import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export const AppCard = defineAsyncComponent<Component>(async () => import('./AppCard.vue'))
