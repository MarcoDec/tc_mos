import {defineStore} from 'pinia'

export default function generateEvent(event) {
    return defineStore(`eventsEmployee/${event.id}`, {
        actions: {
            blur() {
                this.opened = false
                this.selected = false
            },
            dispose() {
                this.$reset()
                this.$dispose()
            },
            focus() {
                this.root.blur()
                this.selected = true
                this.open()
            },
            open() {
                this.opened = true
                this.parentStore?.open()
            }
        },
        getters: {
            calendar: state => ({
                color: 'green',
                date: state.dateTime,
                extendedProps: {date: state.dateTime, dateTime: state.date, id: state.id, name: state.name},
                relation: state['@type'],
                relationId: state.managingCompany ? state.managingCompany.replace(/[a-z && A-Z && /]/g, '') : null,
                title: state.name
            }),
            dateTime: state => (state.date ? new Date(state.date) : null),
            month() {
                return this.dateTime?.getMonth() ? this.dateTime?.getMonth() + 1 : null
            },
            year() {
                return this.dateTime?.getFullYear() ?? null
            }
        },
        state: () => ({...event})
    })()
}
