import type {State} from '.'

export type Suppliers = {
    etat: string | null
    nom: string | null
    id: number | null
}

export type Getters = {
    items: (state: State) => Suppliers[]
    filters: (state: State) => Pick<State,'nom'|'etat'>


}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}


export const getters: Getters = {
    items(state) {
        const items = []
        for (const item of Object.values(state))
            if (typeof item === 'object' && !Array.isArray(item))
                items.push(item)
        return items
    },
     filters: state => ({ nom: state.nom, etat: state.etat })


}