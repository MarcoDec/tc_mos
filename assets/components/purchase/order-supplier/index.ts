import type {Component} from 'vue'
import {defineAsyncComponent} from 'vue'

export const AppCollectionTableCommande = defineAsyncComponent<Component>(async () => import('./AppCollectionTableCommande.vue'))
export const AppCollectionTableGestion = defineAsyncComponent<Component>(async () => import('./AppCollectionTableGestion.vue'))
export const AppCollectionTableQte = defineAsyncComponent<Component>(async () => import('./AppCollectionTableQte.vue'))
export const AppCollectionTableReception = defineAsyncComponent<Component>(async () => import('./AppCollectionTableReception.vue'))
export const AppCollectionTableEchange = defineAsyncComponent<Component>(async () => import('./AppCollectionTableEchange.vue'))
