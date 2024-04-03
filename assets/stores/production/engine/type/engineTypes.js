//import api from '../../../../api'
import {defineStore} from 'pinia'
export const useEngineTypeStore = defineStore('engine-types', {
    actions: {
    },
    getters: {
    },
    state: () => ({
        engineTypes: [
            {text: 'counter-part', value: 'counter-part'},
            {text: 'tool', value: 'tool'},
            {text: 'workstation', value: 'workstation'},
            {text: 'machine', value: 'machine'},
            {text: 'spare-part', value: 'spare-part'},
            {text: 'infra', value: 'infra'},
            {text: 'informatique', value: 'informatique'}
        ]
    })
})
