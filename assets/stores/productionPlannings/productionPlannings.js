import {defineStore} from 'pinia'
import api from './../../api'

export const useProductionPlanningsFieldsStore = defineStore('productionPlanningsFileds', {
    actions: {
        async fetchFields() {
            try {
                const response = await api('/api/manufacturingSchedule', 'GET')
                console.log('Réponse de l\'API :', response.fields) 
                this.fields = response.fields.map((field) => {
                    return {
                        label: field.label,
                        name: field.name,
                        type: field.type
                    }
                })
            } catch (error) {
                console.error(error)
            }
        }
    },
    getters: {
    },
    state: () => ({
        fields: []
    })
})
