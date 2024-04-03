<<<<<<< HEAD
import {defineStore} from 'pinia'
import api from '../../api'

export const useUnitsStore = defineStore('units', {
    actions: {
        async fetch() {
            const response = await api('/api/units', 'GET')
            this.units = response['hydra:member']
        }
    },
    getters: {},

    state: () => ({
        units: []
=======
import api from '../../api'
import {defineStore} from 'pinia'

export default defineStore('units', {
    actions: {

        async getUnits() {
            const response = await api('/api/units?pagination=false', 'GET')
            // console.log('responseListUnits', response)
            this.listUnits = response['hydra:member']
        },
        reset() {
            this.listUnits = []
        }
    },
    getters: {
        unitsOption: state => state.listUnits.map(unit => {
            // console.log('unit',unit);
            const opt = {
                text: unit.code,
                value: unit.code
            }
            return opt
        }),
        unitsSelect: state => state.listUnits.map(unit => {
            const opt = {
                text: unit.code,
                value: unit['@id']
            }
            return opt
        })
    },
    state: () => ({
        listUnits: []
>>>>>>> develop
    })
})
