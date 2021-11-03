import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export * from './card'
export * from './form'
export * from './layout'
export * from './modal'
export * from './navbar'
export * from './jsontreeview'


type AppAlertComputed = {alertClass: () => string}
type AppAlertProps = {variant?: string}
type AppAlertComponent = Component<AppAlertProps, never, never, AppAlertComputed>
export const AppAlert = defineAsyncComponent<AppAlertComponent>(async () => import('./AppAlert.vue'))

type AppBtnComputed = {btnClass: () => string}
type AppBtnProps = {
    label: string
    type?: 'button' | 'reset' | 'submit'
    variant?: string
}
type AppBtnComponent = Component<AppBtnProps, never, never, AppBtnComputed>
export const AppBtn = defineAsyncComponent<AppBtnComponent>(async () => import('./AppBtn.vue'))
