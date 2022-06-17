import type {State} from './state'

export type ItemsFactures = {
    currentPlace: string | null
    deadlineDate: string | null
    invoiceDate: string | null
    invoiceNumber: string | null
    invoiceSendByEmail: string | null
    totalHT: string | null
    totalTTC: string | null
    vta: string | null
}

export type Getters = {
    list: (state: State) => ItemsFactures
}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    list: state => ({
        currentPlace: state.currentPlace,
        deadlineDate: state.deadlineDate,
        invoiceDate: state.invoiceDate,
        invoiceNumber: state.invoiceNumber,
        invoiceSendByEmail: state.invoiceSendByEmail,
        totalHT: state.totalHT,
        totalTTC: state.totalTTC,
        vta: state.vta
    })
}
