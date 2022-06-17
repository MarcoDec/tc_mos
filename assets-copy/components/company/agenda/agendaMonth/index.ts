import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'


export const AppAgendaMonthWrapper = defineAsyncComponent<Component>(async () => import('./AppAgendaMonthWrapper.vue'))
export const MonthCalendar = defineAsyncComponent<Component>(async () => import('./MonthCalendar.vue'))



