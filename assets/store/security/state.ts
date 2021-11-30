export type State = {
    password: string | null
    username: string | null
    error : boolean
}

export const state: State = {
    password: null,
    username: null,
    error: false
}
