import type {State} from '.'

export type Getters = {
     filters: (state: State) => Pick<State,'code'|'nom'|'active'>
     getListCurrency: (state: State) => State['list']


}
type GettersValues = {
     [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters : Getters = {

     getListCurrency: state => state.list.filter( currency =>
         currency.code.toUpperCase().startsWith(state.code.toUpperCase())
     ).filter(currency =>
       typeof  currency.name === 'string' ? currency.name.toUpperCase().startsWith(state.nom.toUpperCase()) : null
     ).filter(currency =>
         currency.active === state.active
     ),

     filters: state => ({code: state.code, nom: state.nom, active: state.active})
}

