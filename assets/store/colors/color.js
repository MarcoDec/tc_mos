export function generateColor(initialState) {
    return {
        getters: {
            tableItem: state => fields => {
                const item = {delete: true, update: true}
                for (const field of fields)
                    item[field.name] = state[field.name]
                return item
            }
        },
        namespaced: true,
        state: initialState
    }
}
