import type {State} from '.'

export type Suppliers = {
    etat: string | null
    nom: string | null
    id: number | null
}

export type Getters = {
    filters: (state: State) => Pick<State, 'etat' | 'nom'>
    items: (state: State) => Suppliers[]

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}

export const getters: Getters = {
    filters: state => ({etat: state.etat, nom: state.nom}),
    items(state) {
        const items = []
        for (const item of Object.values(state))
            if (typeof item === 'object' && !Array.isArray(item))
                items.push(item)
        return items
    }

}
