import api from '../../../api'
import {defineStore} from 'pinia'
import generateWarehouse from './warehouse'
import useUser from '../../security'
const user = useUser()
const userCompanyIri = user.company

export const useWarehouseListItemsStore = defineStore('warehouseListItems', {
    actions: {
        async fetch() {
            this.items = []
            const response = await api(`/api/warehouses?company=${userCompanyIri}&pagination=false`, 'GET')
            console.log('res', response)
            for (const warehouse of response['hydra:member']) {
                const warehouseStored = generateWarehouse(warehouse, this)
                this.items.push(warehouseStored)
            }
            console.log('items', this.items)
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
        items: [
            //{
            //asc: true,
            //current: 1,
            // delete: false,
            // famille: '',
            // first: 1,
            // id: null,
            // last: 1,
            // name: 'test',
            // next: 1,
            // prev: 1,
            // search: {},
            // total: 0,
            // update: false,
            // update2: false
            //}
        ]
    })
})
