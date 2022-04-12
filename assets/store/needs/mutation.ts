import type {State} from '.'

export const mutations = {
    initiale(state: State): void {
        state.needs = [...state.initiale]
        state.displayed = []
    },
    needs(state: State, needs: []): void {
        state.needs = needs
        state.initiale = [...needs]
    },
    show(state: State): void {
        const lenght = state.needs.length
        for (let i = 0; i < 5 && i < lenght; i++)
            // eslint-disable-next-line @typescript-eslint/no-confusing-void-expression
            state.displayed.push(state.needs.shift())
        state.page++
    }
}

export declare type Mutations = typeof mutations
