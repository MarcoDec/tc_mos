import {defineStore} from 'pinia'

export const useWarehouseListItemsStore = defineStore('warehouseListItems', {
    actions: {
        fetch() {
            this.items = [
                {
                    delete: false,
                    famille: 'camion',
                    id: 1,
                    name: 'CAMION FR-MD',
                    update: false,
                    update2: true
                },
                {
                    delete: false,
                    famille: 'prison',
                    id: 2,
                    name: 'Prison',
                    update: false,
                    update2: true
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
        items: [{
            asc: true,
            current: 1,
            delete: false,
            famille: '',
            first: 1,
            id: null,
            last: 1,
            name: '',
            next: 1,
            prev: 1,
            search: {},
            total: 0,
            update: false,
            update2: false
        }]
    })
})
