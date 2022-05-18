import {defineStore} from 'pinia'

export default function generateColor(color, root) {
    return defineStore(`color/${color.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            }
        },
        state: () => ({root, ...color})
    })()
}
