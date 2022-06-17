import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppColComputed = {colClass: () => string}
type AppColProps = {
    cols?: number
    tag?: string
}
type AppColComponent = Component<AppColProps, never, never, AppColComputed>
export const AppCol = defineAsyncComponent<AppColComponent>(async () => import('./AppCol.vue'))

type AppContainerComputed = {
    containerClass: () => string
    fluidClass: () => string
}
type AppContainerProps = {
    cssClass?: number
    fluid?: boolean
}
type AppContainerComponent = Component<AppContainerProps, never, never, AppContainerComputed>
export const AppContainer = defineAsyncComponent<AppContainerComponent>(async () => import('./AppContainer.vue'))

type AppRowProps = {cssClass?: string}
type AppRowComponent = Component<AppRowProps>
export const AppRow = defineAsyncComponent<AppRowComponent>(async () => import('./AppRow.vue'))
