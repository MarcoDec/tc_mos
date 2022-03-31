declare global {
    type ObjectToIntersection<T extends object> = UnionToIntersection<T[keyof T]>
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    type UnionToIntersection<T> = (T extends any ? (x: T) => any : never) extends (x: infer R) => any ? R : never
}

export {}
