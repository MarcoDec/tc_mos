import {defineStore} from 'pinia'

export const useBlCustomerOrderItemsStore = defineStore('blCustomerOrderItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    currentPlace: 'in_creation',
                    delete: true,
                    departureDate: '2017-06-20',
                    id: 1,
                    number: 10011,
                    update: false
                },
                {
                    currentPlace: 'in_creation',
                    delete: true,
                    departureDate: '2017-07-04',
                    id: 2,
                    number: 10080,
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
