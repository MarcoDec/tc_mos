import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppPagination = defineAsyncComponent<Component>(async () => import('./AppPagination.vue'))
export const AppPaginationItem = defineAsyncComponent<Component>(async () => import('./AppPaginationItem'))
