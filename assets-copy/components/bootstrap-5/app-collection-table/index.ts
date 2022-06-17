import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppCollectionTableProps = unknown
type AppCollectionTableComponent = Component<AppCollectionTableProps>
export const AppCollectionTable = defineAsyncComponent<AppCollectionTableComponent>(async () => import('./AppCollectionTable.vue'))
