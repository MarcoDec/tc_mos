export const ENGINE_GROUPS = {
    CounterPartGroup: 'Contrepartie de test',
    ToolGroup: 'Outil',
    WorkstationGroup: 'Poste de travail'
}

export const ENGINE_GROUPS_API = {
    CounterPartGroup: '/api/counter-part-groups',
    ToolGroup: '/api/tool-groups',
    WorkstationGroup: '/api/workstation-groups'
}

export function engineGroupOptions() {
    const options = []
    for (const [value, text] of Object.entries(ENGINE_GROUPS))
        options.push({text, value})
    return options
}

const formatter = new Intl.NumberFormat('fr')

export function format(num) {
    return formatter.format(num)
}
