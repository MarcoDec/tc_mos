import api from '../../api'
import {actionsItems, gettersItems} from '../tables/items'
import {defineStore} from 'pinia'
import generateComponent from './component'

export default defineStore('components', {
    actions: {
        ...actionsItems,
        async fetchOne(id = 1) {
            const response = await api(`/api/components/${id}`, 'GET')
            const item = generateComponent(response, this)
            this.component = item
            this.isLoaded = true
        },
        async fetch(criteria = '') {
            const response = await api(`/api/components${criteria}`, 'GET')
            this.items = await this.updatePagination(response)
            // for (const component of response)
            //     this.items.push(generateComponent(component, this))
        },
        async remove(id){
            await api(`/api/components/${id}`, 'DELETE')
        },
        async addComponent(payload){
            this.component = await api('/api/components', 'POST', payload)
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
        }
    },
    getters: {
        ...gettersItems,
        label() {
            return value =>
                this.options.find(option => option.value === value)?.text ?? null
        },
        options: state =>
            state.items
                .map(component => component.option)
                .sort((a, b) => a.text.localeCompare(b.text)),
        componentItems: state => state.items.map(item => {
            const newObject = {
                '@id': item['@id'],
                type: item['@type'],
                code: item.code,
                index: item.index,
                stateBlocker: item.embBlocker.state,
                state: item.embState.state,
                endOfLife: item.endOfLife,
                family: item.family,
                id: item.id,
                name: item.name,
                unit: item.unit
            }
            return newObject
        })
    },
    state: () => (
        {
            isLoaded: false,
            isLoading: false,
            component: [],
            items: [],
            currentPage: 1,
            pagination: true
        }
    )
})
