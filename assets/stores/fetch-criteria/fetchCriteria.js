import {defineStore} from 'pinia'

export default function useFetchCriteria(id) {
    return defineStore(`fetchCriteria_${id}`, {
        actions: {
            addFilter(field, value, dateType = '') {
                if (typeof field === 'undefined' || typeof value === 'undefined') return
                if (dateType === '') { //Si le champ à filtrer n'est pas une date
                    const filteredFilters = this.filters.filter(element => element.field === field)
                    if (filteredFilters.length > 0) { // Si l'élément fait déjà parti des filtres existant on mets juste la valeur à jour
                        filteredFilters[0].value = value
                    } else { // Sinon on l'ajoute
                        this.filters.push({field, value})
                    }
                } else { //Si le champ à filtrer est une date, il faut envoyer un double filtre
                    const filteredFiltersAfter = this.filters.filter(element => element.field === `${field}[after]`)
                    const filteredFiltersBefore = this.filters.filter(element => element.field === `${field}[before]`)
                    if (filteredFiltersAfter.length > 0) {
                        filteredFiltersAfter[0].value = value
                    } else {
                        const filterName = `${field}[after]`
                        this.filters.push({field: filterName, value})
                    }
                    const nextDay = new Date(value)
                    nextDay.setDate(nextDay.getDate() + 1)
                    if (filteredFiltersBefore.length > 0) {
                        filteredFiltersBefore[0].value = nextDay.toISOString().substring(0, 10)
                    } else {
                        const filterName = `${field}[before]`
                        this.filters.push({field: filterName, value: nextDay.toISOString().substring(0, 10)})
                    }
                }
            },
            addSort(field, direction) {
                const filteredSorts = this.sorts.filter(element => element.field === field)
                const fieldIndex = this.sorts.findIndex(item => item.field === field)
                if (filteredSorts.length > 0) {
                    if (direction === 'both') this.sorts.splice(fieldIndex, 1)
                    else this.sorts[fieldIndex].direction = direction
                } else {
                    this.sorts.push({direction, field})
                }
            },
            gotoPage(pageStr) {
                if (typeof pageStr === 'number') this.page = pageStr
                else {
                    const result = /page=(\d+)/.exec(pageStr)
                    if (result === null) this.page = 1
                    else this.page = Number(result[0].substring(5))
                }
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
                this.filters = this.filters.filter(filter => filter.field !== field)
            },
            resetSort(field) {
                this.sorts = this.sorts.filter(sortElement => sortElement.field !== field)
            },
            reset() {
                this.filters = []
                this.sorts = []
                this.page = 1
            }
        },
        getters: {
            getFetchCriteria: state => {
                let fetchCriteria = '?'
                let filterStr = ''
                let sortStr = ''
                //region gestion des filtres
                if (state.filters.length > 0) {
                    state.filters.forEach(filter => {
                        filterStr += `${filter.field}=${filter.value}&`
                    })
                    filterStr = filterStr.substring(0, filterStr.length - 1) // Suppression du dernier '&'
                    fetchCriteria += filterStr
                }
                //endregion
                //region gestion des tris
                if (state.sorts.length > 0) {
                    state.sorts.forEach(sortElement => {
                        sortStr += `order[${sortElement.field}]=${sortElement.direction}&`
                    })
                    sortStr = sortStr.substring(0, sortStr.length - 1) // suppression du dernier '&'
                    if (filterStr.length > 0) fetchCriteria += '&'
                    fetchCriteria += sortStr
                }
                //endregion
                //region gestion de la pagination
                if (state.paginationEnabled) {
                    if (filterStr.length > 0 || sortStr.length > 0) {
                        return `${fetchCriteria}&page=${state.page}`
                    }
                    return `${fetchCriteria}page=${state.page}`
                }
                //endregion
                return `${fetchCriteria}`
            },
            getFetchCriteriaWithoutPage: state => {
                let fetchCriteria = '?'
                let filterStr = ''
                let sortStr = ''
                //region gestion des filtres
                if (state.filters.length > 0) {
                    state.filters.forEach(filter => {
                        filterStr += `${filter.field}=${filter.value}&`
                    })
                    filterStr = filterStr.substring(0, filterStr.length - 1) // Suppression du dernier '&'
                    fetchCriteria += filterStr
                }
                //endregion
                //region gestion des tris
                if (state.sorts.length > 0) {
                    state.sorts.forEach(sortElement => {
                        sortStr += `order[${sortElement.field}]=${sortElement.direction}&`
                    })
                    sortStr = sortStr.substring(0, sortStr.length - 1) // suppression du dernier '&'
                    if (filterStr.length > 0) fetchCriteria += '&'
                    fetchCriteria += sortStr
                }
                //endregion
                if (filterStr.length > 0 || sortStr.length > 0) {
                    return `${fetchCriteria}&pagination=false`
                }
                return `${fetchCriteria}pagination=false`
            }
        },
        state: () => ({
            filters: [],
            page: 1,
            sorts: [],
            paginationEnabled: true
        })
    })()
}
