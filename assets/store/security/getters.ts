import type {GetterTree} from 'vuex'
import type {RootState} from '../index'
import type {State} from './state'


export type Getters = {
    getUsers: (state: State) => string | null;
}

export const getters: Getters & GetterTree<State, RootState> = {
    getUsers: state => state.username
}
