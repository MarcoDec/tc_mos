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
      const company =  Object.values(state).find(company => company['@id'] == id)
      return typeof company !== 'undefined'? rootGetters[`companies/${company.id}/toString`] : null
   }
}

