export type State = {
    error: boolean
    msgError: string | null
    password: string | null
    status: string | null
    username: string | null
}

export const state: State = {
    error: false,
    msgError: null,
    password: null,
    status: null,
    username: null
}
