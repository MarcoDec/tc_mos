import type {ComputedGetters, State} from '.'
import type {StoreActionContext} from '..'
import type {State as Items} from './componentSupplierPrice'
import {generateItem} from './componentSupplierPrice'

declare type ActionContext = StoreActionContext<State, ComputedGetters>

export const actions = {
    
}

export type Actions = typeof actions
