import type {components} from '../../../../types/openapi'

export type State = {
    name: string | null
    valeur: string | null
    delete: boolean
    update: boolean
    id: number
    list: string[]
}
export type ReadState = Readonly<State>
