import {defineStore} from 'pinia'

export const useCollapseNewOfsItemsStore = defineStore('collapseNewOfsItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    client: 'RENAULT',
                    cmde: '3/2019',
                    debutProd: '2018-12-31',
                    etatInitialOF: 'confirmé', // here 1
                    finProd: '2019-01-07',
                    id: 25,
                    //lancerOF: false,  // here 2
                    minDeLancement: 20,  // here 3
                    ofsAssocies: '',  // here 4
                    produit: '1318808X',
                    qteDemandee: 56,  // here 5
                    //quantite: 20,
                    //quantiteMin: 30,  // here 6
                    siteDeProduction: 'auto'
                },
                {
                    client: 'RENAULT',
                    cmde: '4/2019',
                    debutProd: '2019-12-31',
                    etatInitialOF: 'confirmé',
                    finProd: '2029-01-07',
                    id: 2,
                    lancerOF: false,
                    minDeLancement: 10,
                    ofsAssocies: '',
                    produit: '1318452X',
                    qteDemandee: 60,
                    quantite: 30,
                    quantiteMin: 10,
                    siteDeProduction: 'auto'
                },
                {
                    client: 'RENAULT',
                    cmde: '4/2019',
                    debutProd: '2011-12-31',
                    etatInitialOF: 'confirmé',
                    finProd: '2022-12-07',
                    id: 3,
                    lancerOF: false,
                    minDeLancement: 10,
                    ofsAssocies: '',
                    produit: '1318452X',
                    qteDemandee: 60,
                    quantite: 30,
                    quantiteMin: 31,
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
