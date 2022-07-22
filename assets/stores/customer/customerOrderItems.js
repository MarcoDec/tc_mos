import {defineStore} from 'pinia'

export const useCustomerOrderItemsStore = defineStore('customerOrderItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    dateLivraisonConfirmée: '04/07/2019',
                    dateLivraisonSouhaitée: '25/07/2019',
                    delete: true,
                    description: 'description',
                    etat: 'etat',
                    id: 1,
                    produit: 'produit',
                    quantitéConfirmée: 14,
                    quantitéSouhaitée: 666,
                    ref: 'ref',
                    update: false
                },
                {
                    dateLivraisonConfirmée: '04/04/2019',
                    dateLivraisonSouhaitée: '25/04/2019',
                    delete: true,
                    description: 'descriptionnn',
                    etat: 'etatttt',
                    id: 2,
                    produit: 'produittttt',
                    quantitéConfirmée: 14,
                    quantitéSouhaitée: 666,
                    ref: 'refffff',
                    update: false
                }
            ]
        }

    },
    getters: {
        ariaSort() {
            return field => (this.isSorter(field) ? this.order : 'none')
        },
        fetchBody() {
            return {page: this.current, ...this.search, ...this.orderBody}
        },
        isSorter: state => field => field.name === state.sorted,
        order: state => (state.asc ? 'ascending' : 'descending'),
        pages: state => Math.ceil(state.total / 15)

    },
    state: () => ({
        asc: true,
        current: 1,
        first: 1,
        items: [],
        last: 1,
        next: 1,
        prev: 1,
        search: {},
        total: 0
    })
})
