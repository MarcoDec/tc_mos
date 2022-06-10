export function get(obj, prop) {
    const matches = prop.match(/(\w+)\.(.+)/)
    return matches !== null && matches.length === 3 ? get(obj[matches[1]], matches[2]) : obj[prop]
}

export function set(obj, prop, value) {
    const matches = prop.match(/(\w+)\.(.+)/)
    if (matches !== null && matches.length === 3) {
        if (typeof obj[matches[1]] === 'undefined')
            obj[matches[1]] = {}
        set(obj[matches[1]], matches[2], value)
    } else
        obj[prop] = value
}
