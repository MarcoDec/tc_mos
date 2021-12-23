import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTree = defineAsyncComponent<Component>(async () => import('./AppTree.vue'))
export const AppTreeClickableItem = defineAsyncComponent<Component>(async () => import('./AppTreeClickableItem.vue'))
export const AppTreeItem = defineAsyncComponent<Component>(async () => import('./AppTreeItem.vue'))
export const AppTreeRow = defineAsyncComponent<Component>(async () => import('./AppTreeRow.vue'))
