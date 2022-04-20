import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppScheduleTableItem = defineAsyncComponent<Component>(async () => import('./AppScheduleTableItem.vue'))
export const AppScheduleTableItems = defineAsyncComponent<Component>(async () => import('./AppScheduleTableItems.vue'))
export const AppScheduleTableItemField = defineAsyncComponent<Component>(async () => import('./AppScheduleTableItemField.vue'))
