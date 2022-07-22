import {defineStore} from 'pinia'

export const useOfCustomerOrderItemsStore = defineStore('ofCustomerOrderItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    currentPlace: 'currentPlace',
                    delete: true,
                    deliveryDate: '25/07/2020',
                    id: 1,
                    manufacturingCompany: 'manufacturingCompany',
                    manufacturingDate: '25/07/2019',
                    ofnumber: 10,
                    quantity: 1000,
                    quantityDone: 100,
                    update: false
                },
                {
                    currentPlace: 'currentPlace',
                    delete: true,
                    deliveryDate: '25/07/2020',
                    id: 2,
                    manufacturingCompany: 'manufacturingCompany',
                    manufacturingDate: '25/07/2019',
                    ofnumber: 10,
                    quantity: 1000,
                    quantityDone: 100,
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
