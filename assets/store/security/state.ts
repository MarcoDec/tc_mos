export type State = {
    code: string | null
    error: boolean
    msgError: string | null
    password: string | null
    username: string | null
}

export const state: State = {
    code: null,
    error: false,
    msgError: null,
    password: null,
    username: null
}
