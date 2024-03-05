import api from '../../../api'
import {defineStore} from 'pinia'

export const useColorsStore = defineStore('colors', {
    actions: {
        async getListColors() {
            const response = await api('api/colors', 'GET')
            this.listColors = response['hydra:member']
        },
        async fetch() {
            const response = await api('/api/colors', 'GET')
            this.colors = await response['hydra:member']
        },
        reset() {
            this.colors = []
            this.listColors = []
        }
    },
    getters: {
        colorsOption: state => state.listColors.map(color => {
            const opt = {
                text: color.name,
                value: color['@id']
            }
            return opt
        })
    },
    state: () => ({
        listColors: [],
        colors: []
    })
})
