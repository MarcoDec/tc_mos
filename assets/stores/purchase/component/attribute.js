import {defineStore} from 'pinia'

export default function generateAttribute(attribute) {
    return defineStore(`attributes/${attribute.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        state: () => ({...attribute})
    })()
}
