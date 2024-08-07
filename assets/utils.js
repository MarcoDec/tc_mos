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
export function getOptions(dataColl, textProperty, valueProperty = '@id') {
    return {
        label: value => {
            const filteredColl = dataColl.find(item => item[valueProperty] === value)
            if (typeof filteredColl === 'undefined') return '<null>'
            if (typeof filteredColl[textProperty] === 'undefined') return `Property ${textProperty} not found`
            return filteredColl[textProperty]
        },
        options: dataColl.map(item => ({text: item[textProperty] ?? '', value: item['@id']}))
    }
}
