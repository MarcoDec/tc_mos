export type State = {
    ComponentFamiliesInfo : Array <{ pathid: string | null ; id: number | null; code: string | null; copperable: boolean | null; customsCode: string | null ; filepath: string | null; name: string; parent: string | null ; }>
}

export const state: State = {
    ComponentFamiliesInfo:[]
}
