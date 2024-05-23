import {defineStore} from 'pinia'
import api from '../../api'
import {unset} from 'lodash/object'

const BaseUrl = '/api/selling-order-items'
const BaseUrlProduct = '/api/selling-order-products'
const BaseUrlComponent = '/api/selling-order-components'
export const useCustomerOrderItemsStore = defineStore('customerOrderItems', {
    actions: {
        async fetchAll(filter = '') {
            await this.fetchAllGen(filter, BaseUrl)
        },
        async fetchAllProduct(filter = '') {
            await this.fetchAllGen(filter, BaseUrlProduct)
        },
        async fetchAllComponent(filter = '') {
            await this.fetchAllGen(filter, BaseUrlComponent)
        },
        async fetchAllGen(filter = '', baseUrl = BaseUrl) {
            this.customerOrdersItems=[]
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
        },
        async add(data){
            if (data.product) await this.addProducts(data)
            else if (data.component) await this.addComponents(data)
            else window.alert('impossible d\'ajouter cet élément à la commande, veuillez sélectionner un produit ou un composant.')
        },
        async addProducts(data){
            await api('/api/selling-order-item-products', 'POST', data)
        },
        async addComponents(data){
            await api('/api/selling-order-item-components', 'POST', data)
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
                id: item.id
            }
            //Si item.item['@id`'] est de type composant /api/components/XXX alors on ajoute à newObject le composant
            if (item.item['@id'].includes('components')) {
                newObject.component = item.item
                unset(newObject, 'product')
            }
            if (item.item['@id'].includes('products')) {
                newObject.product = item.item
                unset(newObject, 'component')
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
