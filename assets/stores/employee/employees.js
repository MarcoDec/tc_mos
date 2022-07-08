import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateEmployee from './employee'

export default defineStore('employees', {
    actions: {
        ...actionsItems,
        async fetch() {
            const response = [
                {
                    compte: true,
                    etat: 'valide',
                    id: 1,
                    identifiant: 'super',
                    initiale: 'SU',
                    matricule: '26',
                    nom: 'user',
                    prenom: 'super',
                    service: 'cc',
                    show: true,
                    traffic: true
                },
                {
                    compte: true,
                    etat: 'valide',
                    id: 2,
                    identifiant: 'admin',
                    initiale: 'AD',
                    matricule: '28',
                    nom: 'admin',
                    prenom: 'super',
                    service: 'cc',
                    show: true,
                    traffic: true
                }
            ]
            for (const employee of response)
                this.items.push(generateEmployee(employee, this))
        }
    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        options: state =>
            state.items
                .map(employee => employee.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },

    state: () => ({items: []})
})
