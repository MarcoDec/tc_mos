import type {RootGetters, RootState} from '../../../index'
import type {State} from './state'

export type CalendarEvent = {title:string|null, date: string|null, extendedProps:{id:number, name:string|null,date:string|null}}
export type Getters = {
    relation: (state: State,computed : GettersValues ,rootState: RootState, rootGetters: RootGetters)  => string
    date: (state: State)  => Date | null
    month: (state: State,computed : GettersValues)  => number | null
    year: (state: State,computed : GettersValues)  => number | null
    calendar: (state: State)  => CalendarEvent
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters : Getters = {
    relation: (state,computed,rootState, rootGetters) => {
        return rootGetters[`${state.relation}/find`](state.relationId)
    },
    date: state => state.date ? new Date(state.date) : null,
    month: (state,computed) => typeof computed.date?.getMonth() ==='number' ?(computed.date?.getMonth())+1 : null,
    year: (state,computed) => computed.date?.getFullYear() ?? null,
    calendar: state => ({date: state.date, title:state.name, extendedProps:{id:state.id,name:state.name,date:state.date}}),
}

