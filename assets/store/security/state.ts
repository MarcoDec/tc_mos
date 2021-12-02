export type State = {
    password: string | null
    username: string | null
    error : boolean
    msgError : string | null
    status : string | null
}

export const state: State = {
    password: null,
    username: null,
    error: false,
    msgError: null,
    status: null
}
