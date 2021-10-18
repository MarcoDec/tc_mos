import type {Component} from '@vue/runtime-core'
import type {Modal} from 'bootstrap'
import type {Ref} from 'vue'
import {defineAsyncComponent} from 'vue'

type AppModalComputed = {classVariant: () => string}
type AppModalData = {
    el: Ref<HTMLDivElement>
    modal: Ref<Modal | null>
}
type AppModalMethods = {
    hide: () => void
    onHide: () => void
}
type AppModalProps = {show: boolean, title: string, variant?: string | null}
type AppModalComponent = Component<AppModalProps, never, AppModalData, AppModalComputed, AppModalMethods>
export const AppModal = defineAsyncComponent<AppModalComponent>(async () => import('./AppModal.vue'))
