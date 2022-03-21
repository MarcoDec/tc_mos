import type {State} from '.'
import type {State as RootState} from '..'
import type {ComputedGetters as VueComputedGetters} from '..'


export type Getters = {
    items: (state: State) => string[] 
    rows: (state: State,computed: ComputedGetters, rootState:RootState) => string[][]
}
export declare type ComputedGetters = VueComputedGetters<Getters, State>
export const getters: Getters = {
    items: state => Object.keys(state).map(id=>`componentSuppliers/${id}`),
    rows(state,computed, rootState) {
        
        const rows: string[][] = []
        for (const id of Object.keys(state)) {
            
            const row = [`componentSuppliers/${id}`]
            const prices = [...(rootState.componentSuppliers as State)[id].prices]
            if (prices.length > 0)
                row.push(prices.shift() as string)
            rows.push(row)
            for (const price of prices)
                rows.push([price])
        }
        return rows
    }
    
}
