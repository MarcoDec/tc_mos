import api from '../../api'
import {defineStore} from 'pinia'
import moment from 'moment'

function convertState(state) {
    state.createdAt = moment(state.createdAt)
    return state
}

export default function useNotification(notification, category) {
    let initialState = convertState({...notification, category})
    return defineStore(`notifications/${notification.id}`, {
        actions: {
            dispose() {
                this.category.removeNotification(this)
                this.$reset()
                this.$dispose()
            },
            async reading() {
                const response = await api(this.url, 'PATCH')
                this.reset(response.content)
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            },
            reset(state) {
                initialState = convertState({...state, category: this.category})
                this.$state = initialState
            }
        },
        getters: {
            formattedCreatedAt: state => state.createdAt.format('DD/MM/YYYY HH:mm'),
            url: state => `/api/notifications/${state.id}`
        },
        state: () => ({...initialState})
    })()
}
