import type {State as Family} from './family'

export type State = Record<string, Family>
export type ReadState = Readonly<State>
