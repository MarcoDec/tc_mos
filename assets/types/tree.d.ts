import type {DeepReadonly} from './types'

export type TreeItem = {
    children: TreeItem[]
    icon?: string
    id: number
    label: string
}

export type ReadTreeItem = DeepReadonly<TreeItem>
