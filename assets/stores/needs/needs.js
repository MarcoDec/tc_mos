import {defineStore} from 'pinia'
import api from '../../api'

export default defineStore('needs', {
    actions: {
        async fetch() {
            const response = await api('/api/needs', 'GET');
            this.items = response
        },
        async initiale(state) {
            state.items = await {...state.initiale.products}
            state.displayed = {}
        },
        show(infinite) {
            return this.hasNeeds ? infinite.loaded() : infinite.complete()
        },
        async showComponent() {
            const needs = await this.items.components
            const len = await Object.keys(needs).length;

            for (let i = 0; i < 5 && i < len; i++) {
                const [componentId, need] = needs[i]
                this.displayed[componentId] = need

                delete this.needsComponent[componentId]
            }
            this.page++
        },
        async showProduct() {
            const needs = await this.items.products
            const len = await Object.keys(needs).length;

            for (let i = 0; i < 15 && i < len; i++) {
                const [productId, need] = needs[i]
                this.displayed[productId] = need

                delete this.needsProduct[productId]
            }
            this.page++
        }
    },
    getters: {
        chartsComp(state) {
            return componentId => {
                const componentChartsData = {...state.items.componentChartsData}
                return componentChartsData[componentId]
            }
        },
        chartsProduct(state) {
            return productId => {
                const productChartsData = {...state.items.productChartsData}
                return productChartsData[productId]
            }
        },
        hasNeeds: state => state.items.length > 0,
        needsComponent: state => {
            const components = {...state.items.components}
            return components
        },
        needsProduct: state => {
            const products = {...state.items.products}
            return products
        },
        normalizedChartComp() {
            return componentId => {
                const data = this.chartsProduct(componentId)
                return {
                    data: {
                        datasets: [
                            {
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1,
                                data: data.stockProgress,
                                id: 'line',
                                label: 'stockProgress',
                                type: 'line'
                            },
                            {
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1,
                                data: data.stockMinimum,
                                id: 'bar',
                                label: 'stockMinimum',
                                type: 'line'
                            }
                        ],
                        labels: data.labels
                    },
                    id: 'chart',
                    options: {
                        plugins: {
                            zoom: {
                                zoom: {
                                    mode: 'x',
                                    pinch: {
                                        enabled: true
                                    },
                                    wheel: {
                                        enabled: true
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    color: '#911',
                                    display: true,
                                    font: {
                                        family: 'Comic Sans MS',
                                        lineHeight: 1.2,
                                        size: 20,
                                        style: 'bold'
                                    },
                                    padding: {bottom: 0, left: 0, right: 0, top: 10},
                                    text: 'Jours'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    color: '#191',
                                    display: true,
                                    font: {
                                        family: 'Times',
                                        lineHeight: 1.2,
                                        size: 20,
                                        style: 'normal'
                                    },
                                    padding: {bottom: 0, left: 0, right: 0, top: 30},
                                    text: 'Quantités'
                                }
                            }
                        }
                    },
                    type: 'mixed'
                }
            }
        },
        options: state =>
            state.items
                .map(need => need.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },
    state: () => ({displayed: {}, initiale: {}, items: {}, page: 0})
})
