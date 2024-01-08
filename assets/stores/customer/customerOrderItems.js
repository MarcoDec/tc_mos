import {defineStore} from 'pinia'
import api from '../../api'

const baseUrl = '/api/selling-order-items'
export const useCustomerOrderItemsStore = defineStore('customerOrderItems', {
    actions: {
        async fetchAll(filter = '') {
            this.isLoading = true
            const response = await api(`${baseUrl}${filter}`, 'GET')
            this.customerOrdersItems = response['hydra:member']
            this.pagination = true
            if (response['hydra:totalItems'] > 0) {
                //On récupère toutes les références produits et composants afin de précharger leurs codes
                const myData = []
                const promises = []
                const toLoad = []
                this.customerOrdersItems.forEach((item, index) => {
                    if (item.item['@id'] !== null) {
                        toLoad[item.item['@id']] = true
                        myData[index] = 'item'
                    }
                })
                Object.keys(toLoad).forEach(iri => {
                    const newPromise = new Promise(resolve => {
                        resolve(api(iri, 'GET'))
                    })
                    promises.push(newPromise)
                })
                Promise.allSettled(promises).then(result => {
                    myData.forEach((aData, index) => {
                        const iri = this.customerOrdersItems[index][aData]['@id']
                        const indexOf = Object.keys(toLoad).indexOf(iri)
                        this.customerOrdersItems[index][aData] = result[indexOf].value
                    })
                })
                if (response['hydra:view']['hydra:first']) {
                    this.firstPage = response['hydra:view']['hydra:first'] ? response['hydra:view']['hydra:first'].match(/page=(\d+)/)[1] : '1'
                    this.lastPage = response['hydra:view']['hydra:last'] ? response['hydra:view']['hydra:last'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.nextPage = response['hydra:view']['hydra:next'] ? response['hydra:view']['hydra:next'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.currentPage = response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.previousPage = response['hydra:view']['hydra:previous'] ? response['hydra:view']['hydra:previous'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                } else {
                    this.firstPage = '1'
                    this.lastPage = '1'
                    this.nextPage = '1'
                    this.currentPage = '1'
                    this.previousPage = '1'
                }
            }
            this.isLoading = false
            this.isLoaded = true
        },
        async remove(id){
            await api(`/api/selling-order-items/${id}`, 'DELETE')
            this.customerOrdersItems = this.customerOrdersItems.filter(customerOrderItem => Number(customerOrderItem['@id'].match(/\d+/)[0]) !== id)
        }

    },
    getters: {
        itemsCustomerOrders: state => state.customerOrdersItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                confirmedDate: item.confirmedDate,
                confirmedQuantity: {
                    code: item.confirmedQuantity.code,
                    value: item.confirmedQuantity.value
                },
                requestedDate: item.requestedDate,
                requestedQuantity: {
                    code: item.requestedQuantity.code,
                    value: item.requestedQuantity.value
                },
                ref: item.ref,
                state: item.embState.state,
                notes: item.notes,
                product: item.item,
                id: item.id
            }
            return newObject
        }),
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
        total: 0,
        customerOrders: {},
        customerOrdersItems: {},
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        isLoaded: false,
        isLoading: false
    })
})
