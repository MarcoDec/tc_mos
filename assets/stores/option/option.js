import {defineStore} from 'pinia'

export default function useOption(option, parent) {
    return defineStore(`${parent.id}/${option.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        getters: {value: state => state['@id']},
        state: () => ({...option})
    })()
}
