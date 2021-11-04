import {defineAsyncComponent} from 'vue'

export const AppTree = defineAsyncComponent(async () => import('./AppTree.vue'))
export const AppTreeClickableItem = defineAsyncComponent(async () => import('./AppTreeClickableItem.vue'))
export const AppTreeItem = defineAsyncComponent(async () => import('./AppTreeItem.vue'))
export const AppTreeRow = defineAsyncComponent(async () => import('./AppTreeRow.vue'))
