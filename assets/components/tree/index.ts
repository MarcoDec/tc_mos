import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTree = defineAsyncComponent<Component>(async () => import('./AppTree.vue'))
export const AppTreeClickableItem = defineAsyncComponent<Component>(async () => import('./AppTreeClickableItem.vue'))
export const AppTreeForm = defineAsyncComponent<Component>(async () => import('./AppTreeForm.vue'))
export const AppTreeItem = defineAsyncComponent<Component>(async () => import('./AppTreeItem.vue'))
export const AppTreeRow = defineAsyncComponent<Component>(async () => import('./AppTreeRow.vue'))
export const AppTreeStoredForm = defineAsyncComponent<Component>(async () => import('./AppTreeStoredForm.vue'))
