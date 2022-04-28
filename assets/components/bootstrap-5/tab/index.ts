import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppTab = defineAsyncComponent<Component>(async () => import('./AppTab.vue'))
export const AppTabBtn = defineAsyncComponent<Component>(async () => import('./AppTabBtn.vue'))
export const AppTabs = defineAsyncComponent<Component>(async () => import('./AppTabs.vue'))