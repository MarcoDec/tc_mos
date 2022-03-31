export declare type State = {
    [key: string]: unknown
    spinner: boolean
    status: number
    text: string | null
}

export const state: State = {
    spinner: false,
    status: 0,
    text: null
}
