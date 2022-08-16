import {defineStore} from 'pinia'

export const useProductionPlanningsFieldsStore = defineStore('productionPlanningsFileds', {
    actions: {
        fetchFields() {
            this.fields = [
                {
                    label: '201347',
                    name: '201347',
                    type: 'text'
                },
                {
                    label: '201357',
                    name: '201357',
                    type: 'text'
                },
                {
                    label: '201447',
                    name: '201447',
                    type: 'text'
                },
                {
                    label: '201457',
                    name: '201457',
                    type: 'text'
                },
                {
                    label: '201547',
                    name: '201547',
                    type: 'text'
                },
                {
                    label: '201647',
                    name: '201647',
                    type: 'text'
                },
                {
                    label: '201747',
                    name: '201747',
                    type: 'text'
                },
                {
                    label: '201823',
                    name: '201823',
                    type: 'text'
                },
                {
                    label: '201827',
                    name: '201827',
                    type: 'text'
                },
                {
                    label: '201839',
                    name: '201839',
                    type: 'text'
                },
                {
                    label: '201840',
                    name: '201840',
                    type: 'text'
                }
            ]
        }
    },
    getters: {
    },
    state: () => ({
        fields: []
    })
})
