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
                // const response = await fetch(`http://localhost:8000/api/employees/${employee.id}/main`, {
                //     method: "PATCH",

                //     headers: {
                //       "Content-Type": "application/merge-patch+json",
                //       "Authorization": "Bearer " + "2f3355cc75a253a4c917f1591fcd4e58328eb4e7611068c8bfa23452b4684ecf085b6c4bfdfcd1bec3857ca9771c309a180b761595e5db1e2f62137c",
                //     },

                //     body: JSON.stringify(data),
                //   });
                //   this.$state = {...response}
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
            getPostal: state => state.address.zipCode,
            getTeam: state => (state.team ? state.team.name : ''),
            teamValue: state => (state.team ? state.team['@id'] : null)
        },
        state: () => ({...employee})
    })()
}
