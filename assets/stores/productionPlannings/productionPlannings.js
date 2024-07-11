import {defineStore} from 'pinia'
import api from './../../api'

export const useProductionPlanningsFieldsStore = defineStore('productionPlanningsFileds', {
    actions: {
        async fetch() {
            try {
                const response = await api('/api/manufacturingSchedule', 'GET')
                this.items = response.items
                console.log('Réponse de l\'API :', response)
                this.fields = response.fields.map(field => ({
                    label: field.label,
                    name: field.name,
                    type: field.type
                }))
            } catch (error) {
                console.error(error)
            }
        }
    },
    getters: {},
    state: () => ({
        fields: [],
        items: []
    })
})
