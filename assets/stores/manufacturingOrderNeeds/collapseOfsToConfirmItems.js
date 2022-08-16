import {defineStore} from 'pinia'

export const useCollapseOfsToConfirmItemsStore = defineStore('collapseOfsToConfirmItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    client: 'RENAULT',
                    cmde: '3/2019',
                    confirmerOF: false,
                    debutProd: '2018-12-31',
                    finProd: '2019-01-07',
                    id: 25,
                    of: 137,
                    produit: '1318808X',
                    quantite: 20,
                    quantiteProduite: 30,
                    siteDeProduction: 'auto'
                },
                {
                    client: 'RENAULT',
                    cmde: '4/2019',
                    confirmerOF: false,
                    debutProd: '2019-12-31',
                    finProd: '2020-01-07',
                    id: 27,
                    of: 140,
                    produit: '1318666X',
                    quantite: 20,
                    quantiteProduite: 30,
                    siteDeProduction: 'auto'
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
