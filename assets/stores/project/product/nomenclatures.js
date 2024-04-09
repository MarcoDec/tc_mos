import {defineStore} from 'pinia'
import api from '../../../api'

const baseUrl = '/api/nomenclatures'
export const useNomenclatureStore = defineStore('nomenclatures', {
    actions: {
        async fetchOne(id = 1) {
            this.nomenclatureItem = await api(`${baseUrl}/${id}`, 'GET')
            this.isLoaded = true
        },
        async fetchAll(filter = '') {
            this.isLoading = true
            const response = await api(`${baseUrl}${filter}`, 'GET')
            this.nomenclatures = response['hydra:member']
            this.pagination = true
            if (response['hydra:totalItems'] > 0) {
                //On récupère toutes les références produits et composants afin de précharger leurs codes
                const myData = []
                const promises = []
                const toLoad = []
                this.nomenclatures.forEach((item, index) => {
                    if (item.subProduct !== null) {
                        toLoad[item.subProduct] = true
                        myData[index] = 'subProduct'
                    }
                    if (item.component !== null) {
                        toLoad[item.component] = true
                        myData[index] = 'component'
                    }
                    if (item.equivalent !== null) {
                        toLoad[item.equivalent] = true
                        myData[index] = 'equivalent'
                    }
                })
                //console.log(Object.keys(toLoad))
                Object.keys(toLoad).forEach(iri => {
                    const newPromise = new Promise(resolve => {
                        resolve(api(iri, 'GET'))
                    })
                    promises.push(newPromise)
                })
                Promise.allSettled(promises).then(result => {
                    myData.forEach((aData, index) => {
                        const iri = this.nomenclatures[index][aData]
                        const indexOf = Object.keys(toLoad).indexOf(iri)
                        this.nomenclatures[index][aData] = result[indexOf].value
                    })
                })
                if (response['hydra:view']['hydra:first']) {
                    this.firstPage = response['hydra:view']['hydra:first'] ? response['hydra:view']['hydra:first'].match(/page=(\d+)/)[1] : '1'
                    this.lastPage = response['hydra:view']['hydra:last'] ? response['hydra:view']['hydra:last'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.nextPage = response['hydra:view']['hydra:next'] ? response['hydra:view']['hydra:next'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.currentPage = response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                    this.previousPage = response['hydra:view']['hydra:previous'] ? response['hydra:view']['hydra:previous'].match(/page=(\d+)/)[1] : response['hydra:view']['@id'].match(/page=(\d+)/)[1]
                } else {
                    this.firstPage = 1
                    this.lastPage = 1
                    this.nextPage = 1
                    this.currentPage = 1
                    this.previousPage = 1
                }
            }
            this.isLoading = false
            this.isLoaded = true
        },
        async remove(id) {
            await api(`${baseUrl}/${id}`, 'DELETE')
        }
    },
    state: () => ({
        isLoaded: false,
        isLoading: false,
        nomenclatureItem: {},
        nomenclatures: [],
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: ''
    })
})
