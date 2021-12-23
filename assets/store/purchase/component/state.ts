export type State = {
    ComponentFamiliesInfo: {pathid: string | null, id: number | null, code: string | null, copperable: boolean | null, customsCode: string | null, filepath: string | null, name: string, parent: string | null}[]
    families: object
    ComponentFamilyInstances: object
}

export const state: State = {
    ComponentFamiliesInfo: [],
    ComponentFamilyInstances: {},
    families: {}
}
