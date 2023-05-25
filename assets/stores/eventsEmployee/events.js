import api from '../../api'
import {defineStore} from 'pinia'
import generateEvent from './event'

export default defineStore('eventsEmployee', {
    actions: {
        blur() {
            for (const event of this.items) event.blur()
        },

        dispose() {
            this.$reset()
            for (const event of this.items) event.dispose()
            this.$dispose()
        },
        async fetchOne() {
            const response = await api('/api/employee-events', 'GET')
            for (const event of response['hydra:member']) this.items.push(generateEvent(event, this))
        }
    },
    getters: {

        findByMonth(state) {
            return (month, year) => {
                const events = []
                for (const event of state.items) {
                    if (event.year === year && event.month === month) {
                        events.push(event.calendar)
                    }
                }
                return events
            }
        },
        findByYear(state) {
            return year => {
                const events = []
                for (const event of state.items) {
                    if (event.year === year) {
                        events.push(event.calendar)
                    }
                }
                return events
            }
        },
        options: state => {
            state.items.map(event => event.date)
        }
    },
    state: () => ({items: []})
})
