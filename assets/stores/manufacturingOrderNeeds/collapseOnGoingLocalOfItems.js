import {defineStore} from 'pinia'

export const useCollapseOnGoingLocalOfItemsStore = defineStore('collapseOnGoingLocalOfItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    client: 'RENAULT',
                    cmde: '3/2019',
                    debutProd: '2018-12-31',
                    etat: 'confirmed',
                    finProd: '2019-01-07',
                    id: 25,
                    of: 137,
                    produit: '1318808X',
                    quantite: 20,
                    quantiteProduite: 30
                },
                {
                    client: 'RENAULT',
                    cmde: '4/2019',
                    debutProd: '2019-12-31',
                    etat: 'blocked',
                    finProd: '2020-01-07',
                    id: 27,
                    of: 140,
                    produit: '1318666X',
                    quantite: 20,
                    quantiteProduite: 30
                }
            ]
        }

    },
    getters: {
    },
    state: () => ({
        items: []
    })
})
