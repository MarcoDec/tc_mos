import type {Component} from '@vue/runtime-core'
import {defineAsyncComponent} from 'vue'

type AppComponentFamiliesAddCardProps = unknown
type AppComponentFamiliesAddCardComponent = Component<AppComponentFamiliesAddCardProps>
export const AppComponentFamiliesAddCard = defineAsyncComponent<AppComponentFamiliesAddCardComponent>(async () => import('./AppComponentFamiliesAddCard.vue'))

type AppComponentFamiliesCardProps = unknown
type AppComponentFamiliesCardComponent = Component<AppComponentFamiliesCardProps>
export const AppComponentFamiliesCard = defineAsyncComponent<AppComponentFamiliesCardComponent>(async () => import('./AppComponentFamiliesCard.vue'))
