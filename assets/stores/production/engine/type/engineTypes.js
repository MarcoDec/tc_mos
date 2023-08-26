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
            {text: 'workstation', value: 'workstation'}
        ]
    })
})
