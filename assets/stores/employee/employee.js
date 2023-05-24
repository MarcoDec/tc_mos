import api from '../../api'
import {defineStore} from 'pinia'

export default function generateEmployee(employee) {
    return defineStore(`employees/${employee.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            async update(data) {
                const response = await api(`/api/employees/${employee.id}/main`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateContactEmp(data, id) {
                const response = await api(`/api/employee-contacts/${id}`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateHr(data) {
                const response = await api(`/api/employees/${employee.id}/hr`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateIt(data) {
                const response = await api(`/api/employees/${employee.id}/it`, 'PATCH', data)
                this.$state = {...response}
            },
            async updateProd(data) {
                const response = await api(`/api/employees/${employee.id}/production`, 'PATCH', data)
                this.$state = {...response}
            }

        },
        getters: {
            date: state => new Date(state.birthday),
            dateEntry: state => new Date(state.entryDate),
            getAddress: state => state.address.address,
            getBrith: state =>
                `${state.date.getFullYear()}-${state.getMonth}-${state.getDate}`,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getDate: state =>
                (state.date.getDate() < 9
                    ? `0${state.date.getDate()}`
                    : state.date.getDate()),
            getDateEntry: state =>
                (state.dateEntry.getDate() < 9
                    ? `0${state.dateEntry.getDate()}`
                    : state.dateEntry.getDate()),
            getEmail: state => state.address.email,
            getEmbRoles: state => state.embRoles.roles,
            getEntryDate: state =>
                `${state.dateEntry.getFullYear()}-${state.getMonthEntry}-${state.getDateEntry}`,
            getMonth: state =>
                (state.date.getMonth() < 9
                    ? `0${state.date.getMonth() + 1}`
                    : state.date.getMonth() + 1),
            getMonthEntry: state =>
                (state.dateEntry.getMonth() < 9
                    ? `0${state.dateEntry.getMonth() + 1}`
                    : state.dateEntry.getMonth() + 1),
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode

        },
        state: () => ({...employee})
    })()
}
