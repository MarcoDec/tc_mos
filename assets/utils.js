export function get(obj, prop) {
    const matches = prop.match(/(\w+)\.(.+)/)
    return matches !== null && matches.length === 3 ? get(obj[matches[1]], matches[2]) : obj[prop]
}
