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
                console.log('update', response)
            }
        },
        getters: {
            date: state => new Date(state.birthday),
            getAddress: state => state.address.address,
            getBrith: state =>
                `${state.date.getFullYear()}-${state.getMonth}-${state.getDate}`,
            getCity: state => state.address.city,
            getCountry: state => state.address.country,
            getDate: state =>
                (state.date.getDate() < 10
                    ? `0${state.date.getDate()}`
                    : state.date.getDate()),
            getEmail: state => state.address.email,
            getMonth: state =>
                (state.date.getMonth() < 10
                    ? `0${state.date.getMonth() + 1}`
                    : state.date.getMonth() + 1),
            getPhone: state => state.address.phoneNumber,
            getPostal: state => state.address.zipCode,
            getTeam: state => (state.team ? state.team.name : '')

        },
        state: () => ({...employee})
    })()
}
