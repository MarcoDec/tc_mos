export function isEmpty(value: unknown): boolean {
    return typeof value === 'undefined'
        || value === null
        || (typeof value === 'string' || Array.isArray(value)) && value.length === 0
        || typeof value === 'number' && value === 0
}
