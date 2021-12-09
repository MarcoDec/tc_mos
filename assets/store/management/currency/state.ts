import type {components} from "../../../types/openapi";

export type State = {
    active: boolean
    code: string
    nom: string
    list: Array<components['schemas']['Currency.jsonld-Currency-read']>
} 

export const state: State = {
    active: false,
    code: '',
    nom: '',
    list:[],
}
