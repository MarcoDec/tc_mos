import api from '../../../api'
import {defineStore} from 'pinia'

export const useEmployeeListOFStore = defineStore('employeeListOF', {
    actions: {
        setIdEmployee(id){
            this.employeeID = id
        },
        // async addEmployeeOF(payload){
        //     const violations = []
        //     try {
        //         if (payload.quantite.value !== ''){
        //             payload.quantite.value = parseInt(payload.quantite.value)
        //         }
        //         const element = {
        //             component: payload.composant,
        //             refFournisseur: payload.refFournisseur,
        //             prix: payload.prix,
        //             quantity: payload.quantite,
        //             texte: payload.texte
        //         }
        //         await api('/api/component-stocks', 'POST', element)
        //         this.fetch()
        //     } catch (error) {
        //         violations.push({message: error})
        //     }
        //     return violations
        // },
        async deleted(payload) {
            await api(`/api/manufacturing-operations/${payload}`, 'DELETE')
            this.employeeOF = this.employeeOF.filter(retard => Number(retard['@id'].match(/\d+/)[0]) !== payload)
        },
        async fetch() {
            /*
            Cette fonction retourne l'ensemble des opérations de fabrication associé à un employé
            qu'il en soit un opérateur ou la personne responsable
            Les opérations envoyées ne datent pas plus d'une semaine
            */
            if (this.currentPage < 1){
                this.currentPage = 1
            }

            const currentDate = new Date()

            let year = currentDate.getFullYear()
            let month = parseInt(String(currentDate.getMonth()).padStart(2, '0'))
            let day = parseInt(String(currentDate.getDate()).padStart(2, '0'))
            const tabMoisJour = [31, 30, 28, 31, 30, 31, 30, 31, 30, 31, 30, 31]
            const diffDay = day - 7
            if (diffDay <= 0) {
                if (month === 0){
                    month = 11
                    year--
                } else {
                    month--
                }
                day = tabMoisJour[month] + parseInt(diffDay)
            } else {
                day = diffDay
            }

            month++
            if (month < 10) {
                month = `0${month}`
            }

            if (day < 10) {
                day = `0${day}`
            }
            this.formattedDate = `${year}-${month}-${day}`
            const response = await api(`/api/manufacturing-operations?pic.id=${this.employeeID}&startedDate[after]=${this.formattedDate}`, 'GET')
            const responseOE = await api(`/api/operation-employees?employee.id=${this.employeeID}&operation.startedDate[after]=${this.formattedDate}`, 'GET')
            const responseOEOperator = []
            for (const el of responseOE['hydra:member']){
                responseOEOperator.push(el.operation)
            }

            const tabFusion = response['hydra:member']
            for (const el of responseOEOperator) {
                const existingElement = tabFusion.find(item => item['@id'] === el['@id'])
                if (!existingElement) {
                    tabFusion.push(el)
                }
            }

            tabFusion.sort((a, b) => new Date(b.startedDate) - new Date(a.startedDate))
            response['hydra:member'] = tabFusion
            response['hydra:totalItems'] = tabFusion.length
            this.employeeOF = await this.updatePagination(response)
        },
        async filterBy(payload) {
            let url = `/api/manufacturing-operations?pic.id=${this.employeeID}&startedDate[after]=${this.formattedDate}&`
            let urlOE = `/api/operation-employees?employee.id=${this.employeeID}&operation.startedDate[after]=${this.formattedDate}&`

            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.of !== '') {
                    url += `order.ref=${payload.of}&`
                    urlOE += `operation.order.ref=${payload.of}&`
                }

                if (payload.poste !== ''){
                    url += `workstation.name=${payload.poste}&`
                    urlOE += `operation.workstation.name=${payload.poste}&`
                }

                if (payload.startDate !== ''){
                    url += `startedDate=${payload.startDate}&`
                    urlOE += `operation.startedDate=${payload.startDate}&`
                }
                if (payload.duration !== ''){
                    url += `duration=${payload.duration}&`
                    urlOE += `operation.duration=${payload.duration}&`
                }

                if (payload.actualQuantity !== ''){
                    if (payload.actualQuantity.value !== '') {
                        url += `actualQuantity.value=${payload.actualQuantity.value}&`
                        urlOE += `operation.actualQuantity.value=${payload.actualQuantity.value}&`
                    }
                    if (payload.actualQuantity.code !== '') {
                        url += `actualQuantity.code=${payload.actualQuantity.code}&`
                        urlOE += `operation.actualQuantity.code=${payload.actualQuantity.code}&`
                    }
                }
                if (payload.quantityProduced !== ''){
                    if (payload.quantityProduced.value !== '') {
                        url += `quantityProduced.value=${payload.quantityProduced.value}&`
                        urlOE += `operation.quantityProduced.value=${payload.quantityProduced.value}&`
                    }
                    if (payload.quantityProduced.code !== '') {
                        url += `quantityProduced.code=${payload.quantityProduced.code}&`
                        urlOE += `operation.quantityProduced.code=${payload.quantityProduced.code}&`
                    }
                }

                if (payload.cadence !== ''){
                    if (payload.cadence.value !== '') {
                        url += `operation.cadence.value=${payload.cadence.value}&`
                        urlOE += `operation.operation.cadence.value=${payload.cadence.value}&`
                    }
                    if (payload.cadence.code !== '') {
                        url += `operation.cadence.code=${payload.cadence.code}&`
                        urlOE += `operation.operation.cadence.code=${payload.cadence.code}&`
                    }
                }

                if (payload.statut !== ''){
                    url += `embState.state=${payload.statut}&`
                    urlOE += `operation.embState.state=${payload.statut}&`
                }

                if (payload.cloture !== ''){
                    url += `embBlocker.state=${payload.cloture}&`
                    urlOE += `operation.embBlocker.state=${payload.cloture}&`
                }
                // url += 'page=1'
                this.currentPage = 1
                const response = await api(url, 'GET')
                const responseOE = await api(urlOE, 'GET')

                const responseOEOperator = []
                for (const el of responseOE['hydra:member']){
                    responseOEOperator.push(el.operation)
                }

                const tabFusion = response['hydra:member']
                for (const el of responseOEOperator) {
                    const existingElement = tabFusion.find(item => item['@id'] === el['@id'])
                    if (!existingElement) {
                        tabFusion.push(el)
                    }
                }

                tabFusion.sort((a, b) => new Date(b.startedDate) - new Date(a.startedDate))
                response['hydra:member'] = tabFusion
                response['hydra:totalItems'] = tabFusion.length

                this.employeeOF = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/manufacturing-operations?pic.id=${this.employeeID}&page=${nPage}`, 'GET')
            this.employeeOF = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    let url = '/api/manufacturing-operations?'
                    if (payload.filterBy.value.of !== '') {
                        url += `order.ref=${payload.filterBy.value.of}&`
                    }

                    if (payload.filterBy.value.poste !== ''){
                        url += `workstation.name=${payload.filterBy.value.poste}&`
                    }

                    if (payload.filterBy.value.startDate !== ''){
                        url += `startedDate=${payload.filterBy.value.startDate}&`
                    }
                    if (payload.filterBy.value.duration !== ''){
                        url += `duration=${payload.filterBy.value.duration}&`
                    }

                    if (payload.filterBy.value.actualQuantity !== ''){
                        if (payload.filterBy.value.actualQuantity.value !== '') {
                            url += `actualQuantity.value=${payload.filterBy.value.actualQuantity.value}&`
                        }
                        if (payload.actualQuantity.code !== '') {
                            url += `actualQuantity.code=${payload.actualQuantity.code}&`
                        }
                    }
                    if (payload.filterBy.value.quantityProduced !== ''){
                        if (payload.filterBy.value.quantityProduced.value !== '') {
                            url += `quantityProduced.value=${payload.filterBy.value.quantityProduced.value}&`
                        }
                        if (payload.filterBy.value.quantityProduced.code !== '') {
                            url += `quantityProduced.code=${payload.filterBy.value.quantityProduced.code}&`
                        }
                    }

                    if (payload.filterBy.value.cadence !== ''){
                        if (payload.filterBy.value.cadence.value !== '') {
                            url += `operation.cadence.value=${payload.filterBy.value.cadence.value}&`
                        }
                        if (payload.filterBy.value.cadence.code !== '') {
                            url += `operation.cadence.code=${payload.filterBy.value.cadence.code}&`
                        }
                    }

                    if (payload.filterBy.value.statut !== ''){
                        url += `embState.state=${payload.filterBy.value.statut}&`
                    }

                    if (payload.filterBy.value.cloture !== ''){
                        url += `embBlocker.state=${payload.filterBy.value.cloture}&`
                    }
                    url += `page=${payload.nPage}`

                    response = await api(url, 'GET')
                    this.employeeOF = await this.updatePagination(response)
                } else {
                    let url = '/api/manufacturing-operations?'
                    if (payload.filterBy.value.of !== '') {
                        url += `order.ref=${payload.filterBy.value.of}&`
                    }

                    if (payload.filterBy.value.poste !== ''){
                        url += `workstation.name=${payload.filterBy.value.poste}&`
                    }

                    if (payload.filterBy.value.startDate !== ''){
                        url += `startedDate=${payload.filterBy.value.startDate}&`
                    }
                    if (payload.filterBy.value.duration !== ''){
                        url += `duration=${payload.filterBy.value.duration}&`
                    }

                    if (payload.filterBy.value.actualQuantity !== ''){
                        if (payload.filterBy.value.actualQuantity.value !== '') {
                            url += `actualQuantity.value=${payload.filterBy.value.actualQuantity.value}&`
                        }
                        if (payload.actualQuantity.code !== '') {
                            url += `actualQuantity.code=${payload.actualQuantity.code}&`
                        }
                    }
                    if (payload.filterBy.value.quantityProduced !== ''){
                        if (payload.filterBy.value.quantityProduced.value !== '') {
                            url += `quantityProduced.value=${payload.filterBy.value.quantityProduced.value}&`
                        }
                        if (payload.filterBy.value.quantityProduced.code !== '') {
                            url += `quantityProduced.code=${payload.filterBy.value.quantityProduced.code}&`
                        }
                    }

                    if (payload.filterBy.value.cadence !== ''){
                        if (payload.filterBy.value.cadence.value !== '') {
                            url += `operation.cadence.value=${payload.filterBy.value.cadence.value}&`
                        }
                        if (payload.filterBy.value.cadence.code !== '') {
                            url += `operation.cadence.code=${payload.filterBy.value.cadence.code}&`
                        }
                    }

                    if (payload.filterBy.value.statut !== ''){
                        url += `embState.state=${payload.filterBy.value.statut}&`
                    }

                    if (payload.filterBy.value.cloture !== ''){
                        url += `embBlocker.state=${payload.filterBy.value.cloture}&`
                    }
                    response = await api(url, 'GET')

                    this.employeeOF = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = '/api/manufacturing-operations?'
                if (payload.filterBy.value.of !== '') {
                    url += `order.ref=${payload.filterBy.value.of}&`
                }

                if (payload.filterBy.value.poste !== ''){
                    url += `workstation.name=${payload.filterBy.value.poste}&`
                }

                if (payload.filterBy.value.startDate !== ''){
                    url += `startedDate=${payload.filterBy.value.startDate}&`
                }
                if (payload.filterBy.value.duration !== ''){
                    url += `duration=${payload.filterBy.value.duration}&`
                }

                if (payload.filterBy.value.actualQuantity !== ''){
                    if (payload.filterBy.value.actualQuantity.value !== '') {
                        url += `actualQuantity.value=${payload.filterBy.value.actualQuantity.value}&`
                    }
                    if (payload.actualQuantity.code !== '') {
                        url += `actualQuantity.code=${payload.actualQuantity.code}&`
                    }
                }
                if (payload.filterBy.value.quantityProduced !== ''){
                    if (payload.filterBy.value.quantityProduced.value !== '') {
                        url += `quantityProduced.value=${payload.filterBy.value.quantityProduced.value}&`
                    }
                    if (payload.filterBy.value.quantityProduced.code !== '') {
                        url += `quantityProduced.code=${payload.filterBy.value.quantityProduced.code}&`
                    }
                }

                if (payload.filterBy.value.cadence !== ''){
                    if (payload.filterBy.value.cadence.value !== '') {
                        url += `operation.cadence.value=${payload.filterBy.value.cadence.value}&`
                    }
                    if (payload.filterBy.value.cadence.code !== '') {
                        url += `operation.cadence.code=${payload.filterBy.value.cadence.code}&`
                    }
                }

                if (payload.filterBy.value.statut !== ''){
                    url += `embState.state=${payload.filterBy.value.statut}&`
                }

                if (payload.filterBy.value.cloture !== ''){
                    url += `embBlocker.state=${payload.filterBy.value.cloture}&`
                }
                url += `page=${payload.nPage}`
                response = await api(url, 'GET')
                this.employeeOF = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/manufacturing-operations?page=${payload.nPage}`, 'GET')
                this.employeeOF = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/manufacturing-operations?order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/manufacturing-operations?order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.employeeOF = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = '/api/manufacturing-operations?'
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.of !== '') {
                            url += `order.ref=${filterBy.value.of}&`
                        }

                        if (filterBy.value.poste !== ''){
                            url += `workstation.name=${filterBy.value.poste}&`
                        }

                        if (filterBy.value.startDate !== ''){
                            url += `startedDate=${filterBy.value.startDate}&`
                        }
                        if (filterBy.value.duration !== ''){
                            url += `duration=${filterBy.value.duration}&`
                        }

                        if (filterBy.value.actualQuantity !== ''){
                            if (filterBy.value.actualQuantity.value !== '') {
                                url += `actualQuantity.value=${filterBy.value.actualQuantity.value}&`
                            }
                            if (actualQuantity.code !== '') {
                                url += `actualQuantity.code=${actualQuantity.code}&`
                            }
                        }
                        if (filterBy.value.quantityProduced !== ''){
                            if (filterBy.value.quantityProduced.value !== '') {
                                url += `quantityProduced.value=${filterBy.value.quantityProduced.value}&`
                            }
                            if (filterBy.value.quantityProduced.code !== '') {
                                url += `quantityProduced.code=${filterBy.value.quantityProduced.code}&`
                            }
                        }

                        if (filterBy.value.cadence !== ''){
                            if (filterBy.value.cadence.value !== '') {
                                url += `operation.cadence.value=${filterBy.value.cadence.value}&`
                            }
                            if (filterBy.value.cadence.code !== '') {
                                url += `operation.cadence.code=${filterBy.value.cadence.code}&`
                            }
                        }

                        if (filterBy.value.statut !== ''){
                            url += `embState.state=${filterBy.value.statut}&`
                        }

                        if (filterBy.value.cloture !== ''){
                            url += `embBlocker.state=${filterBy.value.cloture}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                } else {
                    let url = '/api/manufacturing-operations?'
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.of !== '') {
                            url += `order.ref=${filterBy.value.of}&`
                        }

                        if (filterBy.value.poste !== ''){
                            url += `workstation.name=${filterBy.value.poste}&`
                        }

                        if (filterBy.value.startDate !== ''){
                            url += `startedDate=${filterBy.value.startDate}&`
                        }
                        if (filterBy.value.duration !== ''){
                            url += `duration=${filterBy.value.duration}&`
                        }

                        if (filterBy.value.actualQuantity !== ''){
                            if (filterBy.value.actualQuantity.value !== '') {
                                url += `actualQuantity.value=${filterBy.value.actualQuantity.value}&`
                            }
                            if (filterBy.value.actualQuantity.code !== '') {
                                url += `actualQuantity.code=${filterBy.value.actualQuantity.code}&`
                            }
                        }
                        if (filterBy.value.quantityProduced !== ''){
                            if (filterBy.value.quantityProduced.value !== '') {
                                url += `quantityProduced.value=${filterBy.value.quantityProduced.value}&`
                            }
                            if (filterBy.value.quantityProduced.code !== '') {
                                url += `quantityProduced.code=${filterBy.value.quantityProduced.code}&`
                            }
                        }

                        if (filterBy.value.cadence !== ''){
                            if (filterBy.value.cadence.value !== '') {
                                url += `operation.cadence.value=${filterBy.value.cadence.value}&`
                            }
                            if (filterBy.value.cadence.code !== '') {
                                url += `operation.cadence.code=${filterBy.value.cadence.code}&`
                            }
                        }

                        if (filterBy.value.statut !== ''){
                            url += `embState.state=${filterBy.value.statut}&`
                        }

                        if (filterBy.value.cloture !== ''){
                            url += `embBlocker.state=${filterBy.value.cloture}&`
                        }
                    }
                    url += `page=${this.currentPage}`
                    response = await api(url, 'GET')
                }
                this.employeeOF = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/manufacturing-operations?order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/manufacturing-operations?order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.employeeOF = await this.updatePagination(response)
            }
        },
        async updatePagination(response) {
            const responseData = await response['hydra:member']
            this.pagination = false
            return responseData
        }
    },
    getters: {
        itemsEmployeeOF: state => state.employeeOF.map(item => {
            const stDate = item.startedDate.replace(/T/g, ' ')
            let post = ''
            if (item.workstation && item.workstation.name){
                post = item.workstation.name
            }
            let cad = ''
            if (item.operation && item.operation.cadence){
                cad = item.operation.cadence
            }

            const newObject = {
                '@id': item['@id'],
                of: item.order.ref,
                poste: post,
                startDate: stDate,
                actualQuantity: item.actualQuantity,
                quantityProduced: item.quantityProduced,
                cadence: cad,
                statut: item.embState.state,
                cloture: item.embBlocker.state,
                duration: item.duration
            }
            return newObject
        })
    },
    state: () => ({
        currentPage: '',
        firstPage: '',
        lastPage: '',
        nextPage: '',
        pagination: false,
        previousPage: '',
        employeeOF: [],
        employeeID: 0,
        formattedDate: ''
    })
})
