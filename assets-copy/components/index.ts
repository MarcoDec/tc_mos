import type {Emitter, EventType} from 'mitt'
import type {AxiosError} from 'axios'
import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

export * from './bootstrap-5'

type AppModalErrorData = {
    message: string | null
    mitt: Emitter<Record<EventType, AxiosError>>
    show: boolean
}
type AppModalErrorMethods = {
    display: (error: AxiosError) => void
    hide: () => void
}
type AppModalErrorComponent = Component<unknown, never, AppModalErrorData, never, AppModalErrorMethods>
export const AppModalError = defineAsyncComponent<AppModalErrorComponent>(async () => import('./AppModalError.vue'))
