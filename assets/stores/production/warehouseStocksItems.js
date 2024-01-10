import {defineStore} from 'pinia'

export const useWarehouseStocksItemsStore = defineStore('warehouseStocksItems', {
    actions: {
        fetchItems() {
            this.items = [
                {
                    composant: 100,
                    deletee: true,
                    id: 1,
                    localisation: 'P01',
                    numeroDeSerie: '20181026',
                    prison: true,
                    produit: null,
                    quantite: 15,
                    update: true
                },
                {
                    composant: 10,
                    deletee: true,
                    id: 2,
                    localisation: 'P01',
                    numeroDeSerie: '20181026',
                    prison: true,
                    produit: null,
                    quantite: 10,
                    update: true
                },
                {
                    composant: null,
                    deletee: true,
                    id: 3,
                    localisation: 'P01',
                    numeroDeSerie: '20181023',
                    prison: true,
                    produit: 10,
                    quantite: 7,
                    update: true
                },
                {
                    composant: null,
                    deletee: true,
                    id: 4,
                    localisation: 'P01',
                    numeroDeSerie: '20181023',
                    prison: true,
                    produit: 100,
                    quantite: 10,
                    update: true
                },
                {
                    composant: null,
                    deletee: true,
                    id: 5,
                    localisation: 'P01',
                    numeroDeSerie: '20181023',
                    prison: true,
                    produit: 20,
                    quantite: 10,
                    update: true
                }
            ]
        }

    },
    getters: {
        Volumeitems(state) {
            const x = 0
            const volumes = {}
            for (const volumeitem of state.items){
                if (volumeitem.composant !== null && volumeitem.composant > x){
                    if (typeof volumes[volumeitem.composant] === 'undefined'){
                        volumes[volumeitem.composant] = {delete: volumeitem.deletee, quantite: volumeitem.quantite, ref: volumeitem.composant, type: 'Composant', update: volumeitem.update}
                    } else {
                        volumes[volumeitem.composant].quantite += volumeitem.quantite
                    }
                } else if (volumeitem.produit !== null && volumeitem.produit > x){
                    if (typeof volumes[volumeitem.produit] === 'undefined'){
                        volumes[volumeitem.produit] = {delete: volumeitem.deletee, quantite: volumeitem.quantite, ref: volumeitem.produit, type: 'Produit', update: volumeitem.update}
                    } else {
                        volumes[volumeitem.produit].quantite += volumeitem.quantite
                    }
                }
            }
            return Object.values(volumes)
        },
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
        total: 0
    })
})
