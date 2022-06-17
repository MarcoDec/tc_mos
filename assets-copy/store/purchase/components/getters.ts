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
      const component =  Object.values(state).find(component => component['@id'] == id)
      return typeof component !== 'undefined'? rootGetters[`components/${component.id}/toString`] : null
   }
}

