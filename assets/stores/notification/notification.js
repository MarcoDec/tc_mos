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
                this.$dispose()
            },
            async reading() {
                this.reset(await api(this.url, 'PATCH'))
            },
            async remove() {
                await api(this.url, 'DELETE')
                this.dispose()
            },
            reset(state) {
                initialState = convertState({...state, category: this.category})
                this.$reset()
            }
        },
        getters: {
            formattedCreatedAt: state => state.createdAt.format('DD/MM/YYYY HH:mm'),
            url: state => `/api/notifications/${state.id}`
        },
        state: () => ({...initialState})
    })()
}
