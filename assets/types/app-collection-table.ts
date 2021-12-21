import type {FormField} from './bootstrap-5'

export type TableField = FormField & {
    create: boolean
    filter: boolean
    sort: boolean
    sortDir: 'ascending' | 'descending' | 'none'
    update: boolean
}
