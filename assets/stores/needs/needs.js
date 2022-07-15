import {defineStore} from 'pinia'

export default defineStore('needs', {
    actions: {
        async fetch() {
            const response = {
                componentChartsData: {
                    1: {
                        labels: [0.2, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    2: {
                        labels: [
                            '21/11/2017',
                            '21/12/2017',
                            '01/04/2018',
                            '11/05/2019',
                            '11/06/2019'
                        ],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    3: {
                        labels: [0.23, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    4: {
                        labels: [0.24, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    5: {
                        labels: [0.25, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    }
                },
                components: {
                    1: {
                        componentStockDefaults: {
                            1: {date: '2022-07-12'},
                            2: {date: '2022-02-08'}
                        },
                        family: [1, 2, 3],
                        newSupplierOrderNeeds: {
                            1: {date: '2022-05-12', quantity: 200},
                            2: {date: '2022-02-08', quantity: 100}
                        },
                        ref: 1
                    },
                    2: {
                        componentStockDefaults: {
                            1: {date: '2022-07-12'},
                            2: {date: '2022-02-08'}
                        },
                        family: [1, 2, 3],
                        newSupplierOrderNeeds: {
                            1: {date: '2022-05-12', quantity: 200},
                            2: {date: '2022-02-08', quantity: 100}
                        },
                        ref: 2
                    }
                },
                customers: {
                    1: {
                        id: 1,
                        products: [1, 2, 3, 4],
                        society: 1
                    }
                },
                productChartsData: {
                    1: {
                        labels: [0.2, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    2: {
                        labels: [
                            '21/11/2017',
                            '21/12/2017',
                            '01/04/2018',
                            '11/05/2019',
                            '11/06/2019'
                        ],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    3: {
                        labels: [0.23, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    4: {
                        labels: [0.24, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    5: {
                        labels: [0.25, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    6: {
                        labels: [0.26, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    7: {
                        labels: [0.26, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    8: {
                        labels: [0.26, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    },
                    9: {
                        labels: [0.26, 0.3, 0.1, 0.4],
                        stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                        stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
                    }
                },
                productFamilies: {
                    1: {familyName: 'prodFamil1', pathName: 'path1'},
                    2: {familyName: 'prodFamil1', pathName: 'path2'},
                    3: {familyName: 'prodFamil1', pathName: 'path3'}
                },
                products: {
                    1: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 55,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    2: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 75,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    3: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 20,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    4: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 100,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    5: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 55,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    6: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 200,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    7: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 150,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    8: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 150,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    },
                    9: {
                        components: [1, 2],
                        family: [1, 2, 3],
                        minStock: 150,
                        newOFNeeds: {
                            1: {date: '2022-05-12', quantity: '200'},
                            2: {date: '2022-02-08', quantity: '100'}
                        },
                        productDesg: 'd1',
                        productRef: '4444',
                        productStock: 1000,
                        productToManufactring: 1000,
                        stockDefault: {
                            1: {date: '2022-05-02'},
                            2: {date: '2022-02-08'},
                            3: {date: '2022-03-08'}
                        }
                    }
                },

                suppliers: {}
            }

            this.items = response
        },
        initiale(state) {
            state.items = {...state.initiale.products}
            state.displayed = {}
        },
        show(infinite) {
            return this.hasNeeds ? infinite.loaded() : infinite.complete()
        },
        showComponent() {
            const needs = Object.entries(this.items.components)
            const len = needs.length

            for (let i = 0; i < 5 && i < len; i++) {
                const [componentId, need] = needs[i]
                this.displayed[componentId] = need

                delete this.needsComponent[componentId]
            }
            this.page++
        },

        async showProduct() {
            const needs = Object.entries(this.items.products)
            const len = needs.length

            for (let i = 0; i < 5 && i < len; i++) {
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
                                label: 'prixLine',
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
                                label: 'prixBar',
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
                                    text: 'Semaine'
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
                                    text: 'Prix'
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
