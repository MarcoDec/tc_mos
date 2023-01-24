import {defineStore} from 'pinia'
import generateEvent from './event'

export default defineStore('events', {
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
            const response = [
                {
                    '@type': 'a',
                    date: '2022-06-04',
                    id: 1,
                    name: 'Event 1',
                    relation: 'employees',
                    relationId: '1',
                    type: 'x'

                },
                {
                    '@type': 'a',
                    date: '2022-06-04',
                    id: 2,
                    name: 'Event 2',
                    relation: 'employees',
                    relationId: '1',
                    type: 'y'

                }

            ]
            for (const event of response) this.items.push(generateEvent(event, this))
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
