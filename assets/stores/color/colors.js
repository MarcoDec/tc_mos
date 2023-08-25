import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('colors', {
    actions: {
        async getListColors() {
            const response = await api('api/colors', 'GET')
            this.listColors = response['hydra:member']
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
        listColors: []
    })
})
