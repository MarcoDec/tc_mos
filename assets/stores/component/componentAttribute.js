import {defineStore} from 'pinia'

export default function generateComponentAttribute(attribute) {
    return defineStore(`component-attributes/${attribute.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }

        },
        getters: {
            //getFamilies: state => state.attribute.toString(),

        },
        state: () => ({...attribute})
    })()
}
