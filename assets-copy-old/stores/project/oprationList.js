import {defineStore} from 'pinia'

export const useOpertionListStore = defineStore('oprationList', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    ajout: true,
                    auto: true,
                    cadence: 100,
                    code: 'AS 01',
                    deletable: true,
                    limite: 'Mise En Bornier',
                    name: 'Mise en bornnier',
                    prix: null,
                    temps: null,
                    type: null,
                    update: false
                },
                {
                    ajout: true,
                    auto: false,
                    cadence: 100,
                    code: 'AS 02',
                    deletable: false,
                    limite: 'Mise En Bornier',
                    name: 'Mise en bornnier',
                    prix: null,
                    temps: null,
                    type: null,
                    update: false
                }
            ]
        }

    },
    getters: {

    },
    state: () => ({
        items: [{
            ajout: false,
            cadence: null,
            code: '',
            deletable: false,
            limite: '',
            name: '',
            prix: null,
            temps: null,
            type: null,
            update: false
        }]
    })
})
