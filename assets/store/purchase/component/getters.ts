import type {State} from '.'

export const getters = {
getListComponentFamiliesInfo: (state: State): Array<{ pathid: string | null ; id: number | null; code: string | null; copperable: boolean | null; customsCode: string | null ; filepath: string | null; name: string; parent: string | null ;}> => ({
    id : 0 ,
    label :'',
    children: state.ComponentFamiliesInfo
})}


export type Getters = typeof getters