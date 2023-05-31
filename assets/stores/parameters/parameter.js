import {defineStore} from 'pinia'

export default function useParameter(parameter, parent) {
    return defineStore(`${parent.$id}/${parameter.id}`, {
        actions: {
            dispose() {
                this.parent.removeParameter(this)
                this.$dispose()
            }
        },
        getters: {
            field: state => ({label: state.name, name: state['@id'], type: 'boolean'})
        },
        state: () => ({...parameter, parent})
    })()
}
