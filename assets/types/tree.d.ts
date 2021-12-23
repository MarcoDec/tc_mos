import type {FormValues} from './bootstrap-5'

export type TreeItem = FormValues & {
    children?: TreeItem[]
    icon?: string
    id: number
    label: string
}
