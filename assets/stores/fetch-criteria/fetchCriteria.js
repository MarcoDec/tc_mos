import {defineStore} from 'pinia'

export default function useFetchCriteria(id) {
    return defineStore(`fetchCriteria_${id}`, {
        actions: {
            addFilter(field, value) {
                const filteredFilters = this.filters.filter(element => element.field === field)
                if (filteredFilters.length > 0) {
                    console.log('addFilter', filteredFilters)
                    filteredFilters[0].value = value
                } else {
                    this.filters.push({field, value})
                }
            },
            addSort(field, direction) {
                const filteredSorts = this.sorts.filter(element => element.field === field)
                if (filteredSorts.length > 0) {
                    filteredSorts[0].direction = direction
                } else {
                    this.sorts.push({direction, field})
                }
            },
            gotoPage(pageStr) {
                const result = /page=(\d+)/.exec(pageStr)
                if (result === null) this.page = 1
                this.page = Number(result[0].substring(5))
            },
            resetAllFilter() {
                this.filters = []
                this.page = 1
            },
            resetAllSort() {
                this.sorts = []
                this.page = 1
            },
            resetFilter(field) {
                const filteredFilters = this.filters.filter(element => element.field === field)
                filteredFilters.forEach(filter => {
                    this.filters.remove(filter)
                })
            },
            resetSort(field) {
                const filteredSorts = this.sorts.filter(element => element.field === field)
                filteredSorts.forEach(sortElement => {
                    this.sorts.remove(sortElement)
                })
            }
        },
        getters: {
            getFetchCriteria: state => {
                let fetchCriteria = '?'
                let filterStr = ''
                let sortStr = ''
                if (state.filters.length > 0) {
                    state.filters.forEach(filter => {
                        filterStr += `${filter.field}=${filter.value}&`
                    })
                    filterStr = filterStr.substring(0, filterStr.length - 1) // Suppression du dernier '&'
                    fetchCriteria += filterStr
                    console.log(filterStr, fetchCriteria)
                }
                if (state.sorts.length > 0) {
                    console.log(state.sorts, state.sorts.length)
                    state.sorts.forEach(sortElement => {
                        sortStr += `order[${sortElement.field}]=${sortElement.direction}&`
                    })
                    sortStr = sortStr.substring(0, sortStr.length - 1) // suppression du dernier '&'
                    if (filterStr.length > 0) fetchCriteria += '&'
                    fetchCriteria += sortStr
                }
                if (filterStr.length > 0 || sortStr.length > 0) return `${fetchCriteria}&page=${state.page}`
                return `${fetchCriteria}page=${state.page}`
            }
        },
        state: () => ({
            filters: [],
            page: 1,
            sorts: []
        })
    })()
}
