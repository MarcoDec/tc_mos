import type {State as Setting} from './setting'

export type State = Record<string, Setting>
export type ReadState = Readonly<State>
