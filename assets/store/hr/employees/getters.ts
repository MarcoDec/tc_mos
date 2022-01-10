import type {State} from './state'
import {RootGetters, RootState} from "../../index";

export type Getters = {
   find: (state: State,computed:GettersValues,rootState:RootState, rootGetters: RootGetters) => (id:string) => string | null
}
export type GettersValues = {
   readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters : Getters = {
   find:(state,computed,rootState,rootGetters) => id => {
      const employee =  Object.values(state).find(employee => employee['@id'] == id)
      return typeof employee !== 'undefined'? rootGetters[`employees/${employee.id}/toString`] : null
   }
}

