import Api from '../../Api'
import {defineStore} from 'pinia'
import generateEvent from './event'

export default defineStore('eventsEngine', {
    actions: {
        blur() {
            for (const event of this.items) event.blur()
        },

        dispose() {
            this.$reset()
            for (const event of this.items) event.dispose()
            this.$dispose()
        },
        async fetch() {
            const response = await new Api().fetch('/api/engine-events', 'GET')
            if (response.status === 200)
                for (const event of response.content['hydra:member']) this.items.push(generateEvent(event, this))
            else
                throw response.content
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
