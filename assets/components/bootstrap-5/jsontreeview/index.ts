import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'
type JsonTreeViewProps = {data?: string, rootKey: string, maxDepth: number, colorScheme: string}
type JsonTreeViewComponent = Component<JsonTreeViewProps>
export const JsonTreeView = defineAsyncComponent<JsonTreeViewComponent>(async () => import('./JsonTreeView.vue'))
export const JsonTreeViewItem = defineAsyncComponent<JsonTreeViewComponent>(async () => import('./JsonTreeViewItem.vue'))
