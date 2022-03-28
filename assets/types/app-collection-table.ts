import type {FormField, FormValues} from './bootstrap-5'

export declare type TableField = FormField & {
    create: boolean
    filter: boolean
    sort: boolean
    update: boolean
}

export declare type TableItem = FormValues & {
    delete: boolean
    update: boolean
}
