export interface SelectedData {
    key: string;
    value: string;
    path: string;
}
export type Data = Record<string, string>

export enum ItemType {
    OBJECT,
    ARRAY,
    VALUE
}

export type ValueTypes =
    bigint | boolean | number | string | unknown | undefined

export type ItemData = {
    key: string;
    type: ItemType;
    path: string;
    depth: number;
    length?: number;
    children?: ItemData[];
    value?: ValueTypes;
}
