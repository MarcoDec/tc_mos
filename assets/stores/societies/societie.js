import {defineStore} from 'pinia'

export default function generateSocieties(societies) {
    return defineStore(`${societies['@id']}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }

        },
        getters: {

        },
        state: () => ({...societies})
    })()
}
