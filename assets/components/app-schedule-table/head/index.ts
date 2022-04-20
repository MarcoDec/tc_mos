import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppScheduleTableField = defineAsyncComponent<Component>(async () => import('./AppScheduleTableField.vue'))
export const AppScheduleTableFields = defineAsyncComponent<Component>(async () => import('./AppScheduleTableFields.vue'))
export const AppScheduleTableHeaders = defineAsyncComponent<Component>(async () => import('./AppScheduleTableHeaders.vue'))
