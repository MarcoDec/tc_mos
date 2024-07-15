import {defineStore} from 'pinia'
import {ref} from 'vue'
import api from '../../../api'
import {unset} from "lodash/object"

const BaseUrl = '/api/purchase-order-items'
const BaseUrlProduct = '/api/purchase-order-item-products'
const BaseUrlComponent = '/api/purchase-order-item-components'
export const usePurchaseOrderItemComponentsStore = defineStore('purchaseOrderItem', {
    actions: {
        async fetch(filter = '') {
            await this.fetchAllGen(filter, BaseUrl)
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            let paginationView = {}
            if (Object.prototype.hasOwnProperty.call(response, 'hydra:view')) {
                paginationView = response['hydra:view']
            } else {
                paginationView = responseData
            }
            if (Object.prototype.hasOwnProperty.call(paginationView, 'hydra:first')) {
                this.pagination = true
                this.firstPage = paginationView['hydra:first'] ? paginationView['hydra:first'].match(/page=(\d+)/)[1] : '1'
                this.lastPage = paginationView['hydra:last'] ? paginationView['hydra:last'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.nextPage = paginationView['hydra:next'] ? paginationView['hydra:next'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                this.currentPage = paginationView['@id'].match(/page=(\d+)/)[1]
                this.previousPage = paginationView['hydra:previous'] ? paginationView['hydra:previous'].match(/page=(\d+)/)[1] : paginationView['@id'].match(/page=(\d+)/)[1]
                return responseData
            }
            this.pagination = false
            return responseData
        },
        async fetchById(id) {
            const response = await api(`/api/purchase-order-item-components/${id}`, 'GET')
            this.purchaseOrderitemComponent = response
        },
        async fetchAllProduct(filter = '') {
            await this.fetchAllGen(filter, BaseUrlProduct)
        },
        async fetchAllComponent(filter = '') {
            await this.fetchAllGen(filter, BaseUrlComponent)
        },
        async fetchAllGen(filter = '', baseUrl = BaseUrl) {
            this.purchaseOrdersItems = []
            this.isLoading = true
            const response = await api(`${baseUrl}${filter}`, 'GET')
            // console.log(response)
            this.purchaseOrdersItems = response['hydra:member']
            // console.log(this.customerOrdersItems)
            this.pagination = true
            if (response['hydra:totalItems'] > 0) {
                //On récupère toutes les références produits et composants afin de précharger leurs codes
                const myData = []
                const promises = []
                const toLoad = []
                this.purchaseOrdersItems.forEach((item, index) => {
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
                        if (this.purchaseOrdersItems.length > 0) {
                            const iri = this.purchaseOrdersItems[index][aData]['@id']
                            const indexOf = Object.keys(toLoad).indexOf(iri)
                            this.purchaseOrdersItems[index][aData] = result[indexOf].value
                        }
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
            await api(`/api/purchase-order-items/${id}`, 'DELETE')
            this.purchaseOrdersItems = this.purchaseOrdersItems.filter(purchaseOrderItem => Number(purchaseOrderItem['@id'].match(/\d+/)[0]) !== id)
        },
        async update(data, iri) {
            await api(iri, 'PATCH', data)
        },
        async add(data){
            if (data.item.includes('/api/products')) await this.addProducts(data)
            else if (data.item.includes('/api/components')) await this.addComponents(data)
            else window.alert('impossible d\'ajouter cet élément à la commande, veuillez sélectionner un produit ou un composant.')
        },
        async addProducts(data){
            await api('/api/purchase-order-item-products', 'POST', data)
        },
        async addComponents(data){
            await api('/api/purchase-order-item-components', 'POST', data)
        },
        async setCurrentItem(item) {
            // On clone l'item afin que les modifications ne soient pas répercutées sur l'item original
            this.currentItem = {
                confirmedDate: item.confirmedDate,
                confirmedQuantity: {
                    code: item.confirmedQuantity.code,
                    value: item.confirmedQuantity.value
                },
                isForecast: item.isForecast,
                price: item.price,
                requestedDate: item.requestedDate,
                requestedQuantity: {
                    code: item.requestedQuantity.code,
                    value: item.requestedQuantity.value
                },
                ref: item.ref,
                state: item.state,
                notes: item.notes,
                id: item.id
            }
            this.currentItem['@id'] = item['@id']
            if (item.component) {
                this.currentItem.component = item.component['@id']
            }
            if (item.product) {
                this.currentItem.product = item.product['@id']
            }
            // On remplace confirmedQuantity.code par l'iri de l'unité correspondante
            if (item.confirmedQuantity.code !== null) {
                if (!item.confirmedQuantity.code.includes('/api/units/')) {
                    this.currentItem.confirmedQuantity.code = this.currentUnitOptions.find(unit => unit.text === item.confirmedQuantity.code).value
                }
            }
            // On remplace requestedQuantity.code par l'iri de l'unité correspondante
            if (item.requestedQuantity.code !== null) {
                if (!item.requestedQuantity.code.includes('/api/units/')) {
                    this.currentItem.requestedQuantity.code = this.currentUnitOptions.find(unit => unit.text === item.requestedQuantity.code).value
                }
            }
            // On remplace price.code par l'iri de la devise correspondante
            if (item.price.code !== null) {
                console.log('this.currentCurrencyOptions', this.currentCurrencyOptions)
                if (!item.price.code.includes('/api/currencies')) {
                    console.log('Recherche selon le text de currentCurrencyOptions', item.price.code)
                    let theCurrency = this.currentCurrencyOptions.find(currency => currency.text === item.price.code)
                    if (theCurrency === null || typeof theCurrency === 'undefined') {
                        console.log('Recherche selon le code de de currentCurrencyOptions', item.price.code)
                        theCurrency = this.currentCurrencyOptions.find(currency => currency.code === item.price.code)
                    }
                    console.log('final theCurrency', theCurrency)
                    this.currentItem.price.code = theCurrency === null ? null : theCurrency.value
                }
            }
        },
        setCurrentUnitOptions(options) {
            this.currentUnitOptions = options
        },
        setCurrentCurrencyOptions(options) {
            this.currentCurrencyOptions = options
        }
    },
    getters: {
        itemsPurchaseOrders: state => state.purchaseOrdersItems.map(item => {
            const newObject = {
                '@id': item['@id'],
                confirmedDate: item.confirmedDate,
                confirmedQuantity: {
                    code: item.confirmedQuantity.code,
                    value: item.confirmedQuantity.value
                },
                isForecast: item.isForecast,
                price: {
                    code: item.price.code,
                    value: item.price.value
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
        currentItem: ref({}),
        currentUnitOptions: ref([]),
        currentCurrencyOptions: ref([]),
        current: 1,
        first: 1,
        items: [],
        last: 1,
        next: 1,
        prev: 1,
        search: {},
        total: 0,
        purchaseOrderitemComponents: [],
        purchaseOrderitemComponent: [],
        purchaseOrders: {},
        purchaseOrdersItems: [],
        pagination: false,
        firstPage: "",
        lastPage: "",
        nextPage: "",
        currentPage: "",
        previousPage: "",
        isLoaded: false,
        isLoading: false
    })
})
