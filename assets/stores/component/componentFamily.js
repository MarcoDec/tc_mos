import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('componentFamily', {
    actions: {
        async getComponentFamily() {
            const response = await api('api/component-families', 'GET')
            this.listComponentFamilies = response['hydra:member']
        }
    },
    getters: {
        familiesOption: state => state.listComponentFamilies.map(family => {
            const opt = {
                text: family.name,
                value: family['@id']
            }
            return opt
        })
    },
    state: () => ({
        listComponentFamilies: []
    })
})
