import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppShowGui = defineAsyncComponent<Component>(async () => import('./AppShowGui.vue'))
export const AppShowGuiCard = defineAsyncComponent<Component>(async () => import('./AppShowGuiCard.vue'))
export const AppShowGuiResizableCard = defineAsyncComponent<Component>(async () => import('./AppShowGuiResizableCard.vue'))
