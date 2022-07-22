import {defineStore} from 'pinia'

export const useFacturesCustomerOrderItemsStore = defineStore('facturesCustomerOrderItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    currentPlace: 'currentPlace',
                    deadlineDate: '04/07/2019',
                    delete: true,
                    id: 1,
                    invoiceDate: '04/07/2019',
                    invoiceNumber: 'invoiceNumber',
                    invoiceSendByEmail: 'invoiceSendByEmail',
                    totalHT: 'totalHT',
                    totalTTC: 'totalTTC',
                    update: false,
                    vta: 'Vta'
                },
                {
                    currentPlace: 'currentPlace',
                    deadlineDate: '04/07/2019',
                    delete: true,
                    id: 2,
                    invoiceDate: '04/07/2019',
                    invoiceNumber: 'invoiceNumberrrr',
                    invoiceSendByEmail: 'invoiceSendByEmaillll',
                    totalHT: 'totalHTt',
                    totalTTC: 'totalTTCc',
                    update: false,
                    vta: 'Vta'
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
