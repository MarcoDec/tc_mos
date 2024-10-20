import api from '../../../api'
import {defineStore} from 'pinia'

export const useEmployeeListFormationStore = defineStore('employeeListFormation', {
    actions: {
        setIdEmployee(id){
            this.employeeID = id
        },
        async deleted(payload) {
            await api(`/api/skills/${payload}`, 'DELETE')
        },
        async fetch(criteria = '?page=1') {
            if (this.currentPage < 1){
                this.currentPage = 1
            }
            const response = await api(`/api/skills${criteria}`, 'GET')
            this.employeeFormation = await this.updatePagination(response)
        },
        async filterBy(payload) {
            const commun = []
            let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
            if (payload === ''){
                await this.fetch()
            } else {
                if (payload.date !== '') {
                    url += `startedDate=${payload.date}&`
                }

                if (payload.dateCloture !== ''){
                    url += `endedDate=${payload.dateCloture}&`
                }

                if (payload.rappel !== ''){
                    url += `remindedDate=${payload.rappel}&`
                }

                if (payload.competence !== ''){
                    url += `kind.name=${payload.competence}&`
                }

                if (payload.machine !== ''){
                    if (payload.machine.same){
                        commun.push({el: 'engine', value: payload.machine})
                    } else {
                        if (payload.machine.name !== '') {
                            url += `engine.name=${payload.machine.name}&`
                        }
                        if (payload.machine.surname !== '') {
                            url += `engine.surname=${payload.machine.surname}&`
                        }
                    }
                }
                if (payload.formateurInt !== ''){
                    if (payload.formateurInt.same){
                        commun.push({el: 'inTrainer', value: payload.formateurInt})
                    } else {
                        if (payload.formateurInt.name !== '') {
                            url += `inTrainer.name=${payload.formateurInt.name}&`
                        }
                        if (payload.formateurInt.surname !== '') {
                            url += `inTrainer.surname=${payload.formateurInt.surname}&`
                        }
                    }
                }

                if (payload.formateurExt !== ''){
                    if (payload.formateurExt.same) {
                        commun.push({el: 'outTrainer', value: payload.formateurExt})
                    } else {
                        if (payload.formateurExt.name !== '') {
                            url += `outTrainer.name=${payload.formateurExt.name}&`
                        }
                        if (payload.formateurExt.surname !== '') {
                            url += `outTrainer.surname=${payload.formateurExt.surname}&`
                        }
                    }
                }

                if (payload.niveau !== ''){
                    url += `level=${payload.niveau}&`
                }

                if (payload.commentaire !== ''){
                    url += `commentaire=${payload.commentaire}&`
                }

                url += 'page=1&'
                let response = ''
                if (commun.length > 0){
                    let url2 = url
                    for (const el of commun){
                        url += `${el.el}.name=${el.value.name}&`
                        url2 += `${el.el}.surname=${el.value.surname}&`
                    }
                    const response1 = await api(url, 'GET')
                    const response2 = await api(url2, 'GET')

                    const hydraMember1 = response1['hydra:member'] || []
                    const hydraMember2 = response2['hydra:member'] || []

                    const mergedHydraMember = [...new Set([...hydraMember1, ...hydraMember2])]
                    response = response1
                    response['hydra:member'] = mergedHydraMember
                    response['hydra:totalItems'] = mergedHydraMember.length
                } else {
                    response = await api(url, 'GET')
                }
                this.currentPage = 1
                this.employeeFormation = await this.updatePagination(response)
            }
        },
        async itemsPagination(nPage) {
            const response = await api(`/api/skills?employee=/api/employees/${this.employeeID}?page=${nPage}`, 'GET')
            this.employeeFormation = await this.updatePagination(response)
        },
        async paginationSortableOrFilterItems(payload) {
            let response = {}
            if (payload.filter.value === true && payload.sortable.value === true){
                if (payload.trierAlpha.value.component === 'component') {
                    const commun = []
                    let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
                    if (payload.filterBy.value.date !== ''){
                        url += `startedDate=${payload.filterBy.value.date}&`
                    }
                    if (payload.filterBy.value.dateCloture !== ''){
                        url += `endedDate=${payload.filterBy.value.dateCloture}&`
                    }

                    if (payload.filterBy.value.rappel !== ''){
                        url += `remindedDate=${payload.filterBy.value.rappel}&`
                    }
                    if (payload.filterBy.value.competence !== ''){
                        url += `kind.name=${payload.filterBy.value.competence}&`
                    }

                    if (payload.filterBy.value.machine !== ''){
                        if (payload.filterBy.value.machine.same){
                            commun.push({el: 'engine', value: payload.filterBy.value.machine})
                        } else {
                            if (payload.filterBy.value.machine.name !== '') {
                                url += `machine.name=${payload.filterBy.value.machine.name}&`
                            }
                            if (payload.filterBy.value.machine.surname !== '') {
                                url += `machine.surname=${payload.filterBy.value.machine.surname}&`
                            }
                        }
                    }

                    if (payload.filterBy.value.formateurInt !== ''){
                        if (payload.filterBy.value.formateurInt.same){
                            commun.push({el: 'inTrainer', value: payload.filterBy.value.formateurInt})
                        } else {
                            if (payload.filterBy.value.formateurInt.name !== '') {
                                url += `inTrainer.name=${payload.filterBy.value.formateurInt.name}&`
                            }
                            if (payload.filterBy.value.formateurInt.surname !== '') {
                                url += `inTrainer.surname=${payload.filterBy.value.formateurInt.surname}&`
                            }
                        }
                    }
                    if (payload.filterBy.value.formateurExt !== ''){
                        if (payload.filterBy.value.formateurExt.same){
                            commun.push({el: 'outTrainer', value: payload.filterBy.value.formateurExt})
                        } else {
                            if (payload.filterBy.value.formateurExt.name !== '') {
                                url += `outTrainer.name=${payload.filterBy.value.formateurExt.name}&`
                            }
                            if (payload.filterBy.value.formateurExt.surname !== '') {
                                url += `outTrainer.surname=${payload.filterBy.value.formateurExt.surname}&`
                            }
                        }
                    }
                    if (payload.filterBy.value.niveau !== ''){
                        url += `level=${payload.filterBy.value.niveau}&`
                    }
                    if (payload.filterBy.value.commentaire !== ''){
                        url += `commentaire=${payload.filterBy.value.commentaire}&`
                    }

                    url += `page=${payload.nPage}&`

                    response = ''
                    if (commun.length > 0){
                        let url2 = url
                        for (const el of commun){
                            url += `${el.el}.name=${el.value.name}&`
                            url2 += `${el.el}.surname=${el.value.surname}&`
                        }
                        const response1 = await api(url, 'GET')
                        const response2 = await api(url2, 'GET')

                        const hydraMember1 = response1['hydra:member'] || []
                        const hydraMember2 = response2['hydra:member'] || []

                        const mergedHydraMember = [...new Set([...hydraMember1, ...hydraMember2])]
                        response = response1
                        response['hydra:member'] = mergedHydraMember
                        response['hydra:totalItems'] = mergedHydraMember.length
                    } else {
                        response = await api(url, 'GET')
                    }

                    this.employeeFormation = await this.updatePagination(response)
                } else {
                    let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
                    const commun = []
                    if (payload.filterBy.value.date !== ''){
                        url += `startedDate=${payload.filterBy.value.date}&`
                    }
                    if (payload.filterBy.value.dateCloture !== ''){
                        url += `endedDate=${payload.filterBy.value.dateCloture}&`
                    }

                    if (payload.filterBy.value.rappel !== ''){
                        url += `remindedDate=${payload.filterBy.value.rappel}&`
                    }
                    if (payload.filterBy.value.competence !== ''){
                        url += `kind.name=${payload.filterBy.value.competence}&`
                    }

                    if (payload.filterBy.value.machine !== ''){
                        if (payload.filterBy.value.machine.same){
                            commun.push({el: 'engine', value: payload.filterBy.value.machine})
                        } else {
                            if (payload.filterBy.value.machine.name !== '') {
                                url += `machine.name=${payload.filterBy.value.machine.name}&`
                            }
                            if (payload.filterBy.value.machine.surname !== '') {
                                url += `machine.surname=${payload.filterBy.value.machine.surname}&`
                            }
                        }
                    }

                    if (payload.filterBy.value.formateurInt !== ''){
                        if (payload.filterBy.value.formateurInt.same){
                            commun.push({el: 'inTrainer', value: payload.filterBy.value.formateurInt})
                        } else {
                            if (payload.filterBy.value.formateurInt.name !== '') {
                                url += `inTrainer.name=${payload.filterBy.value.formateurInt.name}&`
                            }
                            if (payload.filterBy.value.formateurInt.surname !== '') {
                                url += `inTrainer.surname=${payload.filterBy.value.formateurInt.surname}&`
                            }
                        }
                    }
                    if (payload.filterBy.value.formateurExt !== ''){
                        if (payload.filterBy.value.formateurExt.same){
                            commun.push({el: 'outTrainer', value: payload.filterBy.value.formateurExt})
                        } else {
                            if (payload.filterBy.value.formateurExt.name !== '') {
                                url += `outTrainer.name=${payload.filterBy.value.formateurExt.name}&`
                            }
                            if (payload.filterBy.value.formateurExt.surname !== '') {
                                url += `outTrainer.surname=${payload.filterBy.value.formateurExt.surname}&`
                            }
                        }
                    }
                    if (payload.filterBy.value.niveau !== ''){
                        url += `level=${payload.filterBy.value.niveau}&`
                    }
                    if (payload.filterBy.value.commentaire !== ''){
                        url += `commentaire=${payload.filterBy.value.commentaire}&`
                    }

                    response = await api(url, 'GET')

                    this.employeeFormation = await this.updatePagination(response)
                }
            } else if (payload.filter.value === true){
                let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
                const commun = []
                if (payload.filterBy.value.date !== ''){
                    url += `startedDate=${payload.filterBy.value.date}&`
                }
                if (payload.filterBy.value.dateCloture !== ''){
                    url += `endedDate=${payload.filterBy.value.dateCloture}&`
                }

                if (payload.filterBy.value.rappel !== ''){
                    url += `remindedDate=${payload.filterBy.value.rappel}&`
                }
                if (payload.filterBy.value.competence !== ''){
                    url += `kind.name=${payload.filterBy.value.competence}&`
                }

                if (payload.filterBy.value.machine !== ''){
                    if (payload.filterBy.value.machine.same){
                        commun.push({el: 'engine', value: payload.filterBy.value.machine})
                    } else {
                        if (payload.filterBy.value.machine.name !== '') {
                            url += `machine.name=${payload.filterBy.value.machine.name}&`
                        }
                        if (payload.filterBy.value.machine.surname !== '') {
                            url += `machine.surname=${payload.filterBy.value.machine.surname}&`
                        }
                    }
                }
                if (payload.filterBy.value.formateurInt !== ''){
                    if (payload.filterBy.value.formateurInt.same){
                        commun.push({el: 'inTrainer', value: payload.filterBy.value.formateurInt})
                    } else {
                        if (payload.filterBy.value.formateurInt.name !== '') {
                            url += `inTrainer.name=${payload.filterBy.value.formateurInt.name}&`
                        }
                        if (payload.filterBy.value.formateurInt.surname !== '') {
                            url += `inTrainer.surname=${payload.filterBy.value.formateurInt.surname}&`
                        }
                    }
                }
                if (payload.filterBy.value.formateurExt !== ''){
                    if (payload.filterBy.value.formateurExt.same){
                        commun.push({el: 'outTrainer', value: payload.filterBy.value.formateurExt})
                    } else {
                        if (payload.filterBy.value.formateurExt.name !== '') {
                            url += `outTrainer.name=${payload.filterBy.value.formateurExt.name}&`
                        }
                        if (payload.filterBy.value.formateurExt.surname !== '') {
                            url += `outTrainer.surname=${payload.filterBy.value.formateurExt.surname}&`
                        }
                    }
                }
                if (payload.filterBy.value.niveau !== ''){
                    url += `level=${payload.filterBy.value.niveau}&`
                }
                if (payload.filterBy.value.commentaire !== ''){
                    url += `commentaire=${payload.filterBy.value.commentaire}&`
                }

                url += `page=${payload.nPage}&`

                response = ''
                if (commun.length > 0){
                    let url2 = url
                    for (const el of commun){
                        url += `${el.el}.name=${el.value.name}&`
                        url2 += `${el.el}.surname=${el.value.surname}&`
                    }
                    const response1 = await api(url, 'GET')
                    const response2 = await api(url2, 'GET')

                    const hydraMember1 = response1['hydra:member'] || []
                    const hydraMember2 = response2['hydra:member'] || []

                    const mergedHydraMember = [...new Set([...hydraMember1, ...hydraMember2])]
                    response = response1
                    response['hydra:member'] = mergedHydraMember
                    response['hydra:totalItems'] = mergedHydraMember.length
                } else {
                    response = await api(url, 'GET')
                }
                this.employeeFormation = await this.updatePagination(response)
            } else if (payload.sortable.value === false) {
                response = await api(`/api/skills?employee=/api/employees/${this.employeeID}&page=${payload.nPage}`, 'GET')
                this.employeeFormation = await this.updatePagination(response)
            } else {
                if (payload.trierAlpha.value.composant === 'component') {
                    response = await api(`/api/skills?employee=/api/employees/${this.employeeID}&order%5B${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                } else {
                    response = await api(`/api/skills?employee=/api/employees/${this.employeeID}&order%5Baddress.${payload.trierAlpha.value.composant}%5D=${payload.trierAlpha.value.trier.value}&page=${payload.nPage}`, 'GET')
                }
                this.employeeFormation = await this.updatePagination(response)
            }
        },

        async sortableItems(payload, filterBy, filter) {
            let response = {}
            if (filter.value === true){
                if (payload.composant === 'component') {
                    let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
                    const commun = []
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.date !== ''){
                            url += `startedDate=${filterBy.value.date}&`
                        }
                        if (filterBy.value.dateCloture !== ''){
                            url += `endedDate=${filterBy.value.dateCloture}&`
                        }
                        if (filterBy.value.rappel !== ''){
                            url += `remindedDate=${filterBy.value.rappel}&`
                        }
                        if (filterBy.value.competence !== ''){
                            url += `kind.name=${filterBy.value.competence}&`
                        }

                        if (filterBy.value.machine !== ''){
                            if (filterBy.value.machine.same){
                                commun.push({el: 'engine', value: filterBy.value.machine})
                            } else {
                                if (filterBy.value.machine.name !== '') {
                                    url += `machine.name=${filterBy.value.machine.name}&`
                                }
                                if (filterBy.value.machine.surname !== '') {
                                    url += `machine.surname=${filterBy.value.machine.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.formateurInt !== ''){
                            if (filterBy.value.formateurInt.same){
                                commun.push({el: 'intTrainer', value: filterBy.value.formateurInt})
                            } else {
                                if (filterBy.value.formateurInt.name !== '') {
                                    url += `inTrainer.name=${filterBy.value.formateurInt.name}&`
                                }
                                if (filterBy.value.formateurInt.surname !== '') {
                                    url += `inTrainer.surname=${filterBy.value.formateurInt.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.formateurExt !== ''){
                            if (filterBy.value.formateurExt.same){
                                commun.push({el: 'outTrainer', value: filterBy.value.formateurExt})
                            } else {
                                if (filterBy.value.formateurExt.name !== '') {
                                    url += `outTrainer.name=${filterBy.value.formateurExt.name}&`
                                }
                                if (filterBy.value.formateurExt.surname !== '') {
                                    url += `outTrainer.surname=${filterBy.value.formateurExt.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.niveau !== ''){
                            url += `level=${filterBy.value.niveau}&`
                        }
                        if (filterBy.value.commentaire !== ''){
                            url += `commentaire=${filterBy.value.commentaire}&`
                        }
                    }
                    url += `page=${this.currentPage}&`
                    response = ''
                    if (commun.length > 0){
                        let url2 = url
                        for (const el of commun){
                            url += `${el.el}.name=${el.value.name}&`
                            url2 += `${el.el}.surname=${el.value.surname}&`
                        }
                        const response1 = await api(url, 'GET')
                        const response2 = await api(url2, 'GET')

                        const hydraMember1 = response1['hydra:member'] || []
                        const hydraMember2 = response2['hydra:member'] || []

                        const mergedHydraMember = [...new Set([...hydraMember1, ...hydraMember2])]
                        response = response1
                        response['hydra:member'] = mergedHydraMember
                        response['hydra:totalItems'] = mergedHydraMember.length
                    } else {
                        response = await api(url, 'GET')
                    }
                } else {
                    const commun = []
                    let url = `/api/skills?employee=/api/employees/${this.employeeID}&`
                    if (filterBy.value === ''){
                        await this.fetch()
                    } else {
                        if (filterBy.value.date !== ''){
                            url += `startedDate=${filterBy.value.date}&`
                        }
                        if (filterBy.value.dateCloture !== ''){
                            url += `endedDate=${filterBy.value.dateCloture}&`
                        }
                        if (filterBy.value.rappel !== ''){
                            url += `remindedDate=${filterBy.value.rappel}&`
                        }
                        if (filterBy.value.competence !== ''){
                            url += `kind.name=${filterBy.value.competence}&`
                        }

                        if (filterBy.value.machine !== ''){
                            if (filterBy.value.machine.same){
                                commun.push({el: 'engine', value: filterBy.value.machine})
                            } else {
                                if (filterBy.value.machine.name !== '') {
                                    url += `machine.name=${filterBy.value.machine.name}&`
                                }
                                if (filterBy.value.machine.surname !== '') {
                                    url += `machine.surname=${filterBy.value.machine.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.formateurInt !== ''){
                            if (filterBy.value.machine.same){
                                commun.push({el: 'engine', value: filterBy.value.machine})
                            } else {
                                if (filterBy.value.formateurInt.name !== '') {
                                    url += `inTrainer.name=${filterBy.value.formateurInt.name}&`
                                }
                                if (filterBy.value.formateurInt.surname !== '') {
                                    url += `inTrainer.surname=${filterBy.value.formateurInt.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.formateurExt !== ''){
                            if (filterBy.value.formateurExt.same){
                                commun.push({el: 'outTrainer', value: filterBy.value.formateurExt})
                            } else {
                                if (filterBy.value.formateurExt.name !== '') {
                                    url += `outTrainer.name=${filterBy.value.formateurExt.name}&`
                                }
                                if (filterBy.value.formateurExt.surname !== '') {
                                    url += `outTrainer.surname=${filterBy.value.formateurExt.surname}&`
                                }
                            }
                        }
                        if (filterBy.value.niveau !== ''){
                            url += `level=${filterBy.value.niveau}&`
                        }
                        if (filterBy.value.commentaire !== ''){
                            url += `commentaire=${filterBy.value.commentaire}&`
                        }
                    }
                    url += `page=${this.currentPage}&`
                    response = ''
                    if (commun.length > 0){
                        let url2 = url
                        for (const el of commun){
                            url += `${el.el}.name=${el.value.name}&`
                            url2 += `${el.el}.surname=${el.value.surname}&`
                        }
                        const response1 = await api(url, 'GET')
                        const response2 = await api(url2, 'GET')

                        const hydraMember1 = response1['hydra:member'] || []
                        const hydraMember2 = response2['hydra:member'] || []

                        const mergedHydraMember = [...new Set([...hydraMember1, ...hydraMember2])]
                        response = response1
                        response['hydra:member'] = mergedHydraMember
                        response['hydra:totalItems'] = mergedHydraMember.length
                    } else {
                        response = await api(url, 'GET')
                    }
                }
                this.employeeFormation = await this.updatePagination(response)
            } else {
                if (payload.composant === 'component') {
                    response = await api(`/api/skills?employee=/api/employees/${this.employeeID}&order%5B${payload.composant}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                } else {
                    response = await api(`/api/skills?employee=/api/employees/${this.employeeID}&order%5B${payload.produit}%5D=${payload.trier.value}&page=${this.currentPage}`, 'GET')
                }
                this.employeeFormation = await this.updatePagination(response)
            }
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
        itemsEmployeeFormation: state => state.employeeFormation.map(item => {
            let dt = ''

            if (item.startedDate !== null) {
                dt = item.startedDate.split('T')[0]
            }
            let dtCloture = ''
            if (item.endedDate !== null) {
                dtCloture = item.endedDate.split('T')[0]
            }
            let dtRappel = ''
            if (item.remindedDate !== null) {
                dtRappel = item.remindedDate.split('T')[0]
            }

            const newObject = {
                '@id': item['@id'],
                startedDate: dt,
                endedDate: dtCloture,
                remindedDate: dtRappel,
                kind: item.kind ?? ['@id'],
                engine: item.engine,
                family: item.family,
                level: item.level,
                inTrainer: item.inTrainer,
                outTrainer: item.outTrainer,
                product: item.product
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
        employeeFormation: [],
        employeeID: 0
    })
})
