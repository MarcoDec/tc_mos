import type {State} from './state'


export const getters = {hasUser: (state: State): boolean => state.username !== null}
