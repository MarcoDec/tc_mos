import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppCard = defineAsyncComponent<Component>(async () => import('./AppCard.vue'))
