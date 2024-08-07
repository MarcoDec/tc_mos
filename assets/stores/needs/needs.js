import {defineStore} from 'pinia'
import api from '../../api'

export default defineStore('needs', {
    actions: {
        async fetch() {
            await this.fetchProducts()
            await this.fetchComponents()
        },
        async fetchProducts() {
            // On charge toutes les données de besoin produit d'un coup, mais on n'affiche pas tout d'un coup
            this.products = await api('/api/needs/products', 'GET')
        },
        async fetchComponents() {
            // On charge toutes les données de besoin composant d'un coup, mais on n'affiche pas tout d'un coup
            this.components = await api('/api/needs/components', 'GET')
        },
        async initialise() {
            this.products = await {...this.initiale.products}
            this.components = await {...this.initiale.components}
            this.displayedProducts = {}
            this.displayedComponents = {}
        },
        productShow(infinite) {
            return this.hasProductNeeds ? infinite.loaded() : infinite.complete()
        },
        componentShow(infinite) {
            return this.hasComponentNeeds ? infinite.loaded() : infinite.complete()
        },
        showComponent() {
            this.componentsPage++
            const needs = this.components.component
            //needs est in tableau d'objets, on ne doit garder que les 15 * componentsPage premiers éléments
            this.displayedComponents = needs.slice(0, this.componentsPage * 15)
        },
        showProduct() {
            this.productsPage++
            const needs = this.products.products
            //needs est in tableau d'objets, on ne doit garder que les 15 * productsPage premiers éléments
            this.displayedProducts = needs.slice(0, this.productsPage * 15)
        }
    },
    getters: {
        chartsComp(state) {
            return index => state.components.componentChartData[index]
        },
        chartsProduct(state) {
            return index => state.products.productChartsData[index]
        },
        hasProductNeeds: state => state.products.length > 0,
        hasComponentNeeds: state => state.components.length > 0,
        needsComponent: state => ({...state.displayedComponents}),
        needsProduct: state => ({...state.displayedProducts}),
        normalizedChartProd() {
            return productId => {
                const chartData = this.chartsProduct(productId)
                return {
                    data: {
                        datasets: [
                            {
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1,
                                data: chartData.stockProgress,
                                id: 'line',
                                label: 'stockProgress',
                                type: 'line'
                            },
                            {
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                data: chartData.stockMinimum,
                                id: 'bar',
                                label: 'stockMinimum',
                                type: 'line'
                            }
                        ],
                        labels: chartData.labels
                    },
                    id: 'chart',
                    options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: tooltipItem => {
                                        const texte = []
                                        const datasetLabel = tooltipItem.dataset.label || ''
                                        const dataPoint = tooltipItem.raw
                                        texte.push(`${datasetLabel}: ${dataPoint} unités`)
                                        // Si chartData.sellingOrderItemsInfo[tooltipItem.label] est défini, on affiche ce texte
                                        if (chartData.sellingOrderItemsInfo && chartData.sellingOrderItemsInfo[tooltipItem.label]) {
                                            texte.push(chartData.sellingOrderItemsInfo[tooltipItem.label])
                                        }
                                        if (chartData.manufacturingOrderItemsInfo && chartData.manufacturingOrderItemsInfo[tooltipItem.label]) {
                                            texte.push(chartData.manufacturingOrderItemsInfo[tooltipItem.label])
                                        }
                                        return texte
                                    }
                                }
                            },
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
                                        size: 20
                                        // style: 'bold'
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
        normalizedChartComp() {
            return componentId => {
                const componentsData = this.chartsComp(componentId)
                return {
                    data: {
                        datasets: [
                            {
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1,
                                data: componentsData.stockProgress,
                                id: 'line',
                                label: 'stockProgress',
                                type: 'line'
                            },
                            {
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                data: componentsData.stockMinimum,
                                id: 'bar',
                                label: 'stockMinimum',
                                type: 'line'
                            }
                        ],
                        labels: componentsData.labels
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
                                        size: 20
                                        // style: 'bold'
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
        productOptions: state =>
            state.products
                .map(need => need.option)
                .sort((a, b) => a.text.localeCompare(b.text))
    },
    state: () => ({
        initiale: {},
        displayedComponents: {},
        displayedProducts: {},
        productsPage: 0,
        componentsPage: 0,
        products: [],
        components: []
    })
})
