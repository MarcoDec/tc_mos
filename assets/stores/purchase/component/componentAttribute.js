import api from '../../../api'
import {defineStore} from 'pinia'

export default function generateComponentAttribute(attribute) {
    return defineStore(`component-attributes/${attribute.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async updateAttributes(data) {
                const response = await api(`/api/component-attributes/${attribute.id}`, 'PATCH', data)
                this.$state = {...response}
            }
        },
        getters: {
            getColor: state => (state.color ? state.color.id : ''),
            getRgb: state => (state.color ? state.color.rgb : '')
        },
        state: () => ({...attribute})
    })()
}
