import type {FormField, FormValues} from './bootstrap-5'

export type TableField = FormField & {
    create: boolean
    filter: boolean
    sort: boolean
    update: boolean
}

export type TableItem = FormValues & {
    delete: boolean
    update: boolean
}
