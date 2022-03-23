import type { FormField, FormOption, FormOptions } from "../../types/bootstrap-5";
import type {
  RootComputedGetters,
  State as RootState,
  ComputedGetters as VueComputedGetters,
} from "..";
import type {State} from '.'

export declare type Getters = {
  fields: (
    state: State,
    computed: ComputedGetters,
    rootState: RootState,
    rootGetters: RootComputedGetters
) => FormField[]| String};

export declare type ComputedGetters = VueComputedGetters<Getters, State>;

export const getters: Getters = {
  fields(state, computed, rootState, rootGetters) {
    const fields: FormField[] = []
    const message: String = 'aucune attribut'

    if (Object.keys(state).length > 0){

    for (const field of Object.keys(state))
    fields.push(rootGetters[`attributs/${field}/field`] as FormField)
    return fields.sort((a, b) => a.name.localeCompare(b.name))
  }else{
    return message
  }
},};
