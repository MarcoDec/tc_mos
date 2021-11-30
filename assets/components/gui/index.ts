import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

/*type AppShowGuiCardProps = {cssClass?: string}
type AppShowGuiCardComponent = Component<AppShowGuiCardProps>
export const AppShowGuiCard = defineAsyncComponent<AppShowGuiCardComponent>(async () => import('./AppShowGuiCard.vue'))
export const AppShowGuiCard = defineAsyncComponent<AppShowGuiCardComponent>(async () => import('./AppShowGuiCard.vue'))*/
export const AppShowGuiCard = defineAsyncComponent(async () => import('./AppShowGuiCard.vue'))
export const AppShowGui = defineAsyncComponent(async () => import('./AppShowGui.vue'))
export const AppShowGuiTabs = defineAsyncComponent(async () => import('./AppShowGuiTabs.vue'))
