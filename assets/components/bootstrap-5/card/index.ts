import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppCardProps = {cssClass?: string}
type AppCardComponent = Component<AppCardProps>
export const AppCard = defineAsyncComponent<AppCardComponent>(async () => import('./AppCard.vue'))
