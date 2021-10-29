import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppShowGuiCardProps = {cssClass?: string}
type AppShowGuiCardComponent = Component<AppShowGuiCardProps>
export const AppShowGuiCard = defineAsyncComponent<AppShowGuiCardComponent>(async () => import('./AppShowGuiCard.vue'))
