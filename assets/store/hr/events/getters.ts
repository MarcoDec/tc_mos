import {RootGetters, RootState} from "../../index"
import type {State} from './state'
import type  {CalendarEvent} from './event/getters'

export type Getters = {
    findByMonth: (state: State,computed : GettersValues ,rootState:RootState, rootGetters: RootGetters)  => (month:number, year: number) => CalendarEvent[]
    findByYear: (state: State,computed : GettersValues ,rootState:RootState, rootGetters: RootGetters)  => (year: number) => CalendarEvent[]

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters : Getters = {
    findByMonth(state,computed,rootState,rootGetters){
      return (month, year) => {
          const events: CalendarEvent[] =[]
          for(const [id,event] of Object.entries(state)){
             if(rootGetters[`events/${id}/year`] === year && rootGetters[`events/${id}/month`] === month){
                 events.push(rootGetters[`events/${id}/calendar`])
             }
          }
          return events
        }

    },
    findByYear(state,computed,rootState,rootGetters){
        return (year) => {
            const events: CalendarEvent[] = []
            for(const [id,event] of Object.entries(state)){
                if(rootGetters[`events/${id}/year`] === year){
                    events.push(rootGetters[`events/${id}/calendar`])
                }
            }
            return events
        }

    }
}

