import type {State} from '.'

export const mutations = {
    listItem(state: State, list: string[]): void {
        state.list = list
        console.log('listt', list)
    }
}

export type Mutations = typeof mutations
