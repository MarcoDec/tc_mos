export function useSlots(context) {
    const children = []
    for (const slot of Object.values(context.slots))
        if (typeof slot !== 'undefined')
            children.push(slot())
    return children
}
