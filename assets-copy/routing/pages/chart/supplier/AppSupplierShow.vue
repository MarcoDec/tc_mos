<script lang="ts" setup>
    import AppChartTable from '../AppChartTable.vue'

    const lineChart = {
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
                    data: [7, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
                    id: 'line',
                    label: 'CA',
                    type: 'scatter'
                }
            ],
            labels: [0.2, 0.3, 0.1, 0.4]
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
                    position: 'bottom',
                    title: {
                        color: '#911',
                        display: true,
                        font: {
                            family: 'Comic Sans MS',
                            lineHeight: 1.2,
                            size: 20,
                            weight: 'bold'
                        },
                        text: 'Semaine'
                    },
                    type: 'linear'
                },
                y: {
                    display: true,
                    padding: {bottom: 0, left: 0, right: 0, top: 30},
                    title: {
                        color: '#191',
                        display: true,
                        font: {
                            family: 'Times',
                            lineHeight: 1.2,
                            size: 20,
                            style: 'normal'
                        },
                        text: 'Prix'
                    }
                }
            }
        },
        type: 'mixed'
    }

    const livChart = {
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
                    data: [7, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
                    id: 'line',
                    label: 'CA',
                    type: 'scatter'
                }
            ],
            labels: [0.2, 0.3, 0.1, 0.4]
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
                    position: 'bottom',
                    title: {
                        color: '#911',
                        display: true,
                        font: {
                            family: 'Comic Sans MS',
                            lineHeight: 1.2,
                            size: 20,
                            weight: 'bold'
                        },
                        text: 'Semaine'
                    },
                    type: 'linear'
                },
                y: {
                    display: true,
                    padding: {bottom: 0, left: 0, right: 0, top: 30},
                    title: {
                        color: '#191',
                        display: true,
                        font: {
                            family: 'Times',
                            lineHeight: 1.2,
                            size: 20,
                            style: 'normal'
                        },
                        text: 'Prix'
                    }
                }
            }
        },
        type: 'mixed'
    }
</script>

<template>
    <div class="bg-secondary text-center text-white">
        <b>Livraisons attendues vs reçues</b>
    </div>
    <AppRow>
        <Vue3ChartJs
            :id="livChart.id"
            :type="livChart.type"
            :data="livChart.data"
            :options="livChart.options"/>
        <AppChartTable
            :data="livChart.data"
            :title-x="livChart.options.scales.x.title.text"
            :title-y="livChart.options.scales.y.title.text"/>
    </AppRow>

    <div class="bg-secondary text-center text-white">
        <b>Chiffre d'Affaire</b>
    </div>

    <AppRow>
        <Vue3ChartJs
            :id="lineChart.id"
            :type="lineChart.type"
            :data="lineChart.data"
            :options="lineChart.options"/>
        <AppChartTable
            :data="lineChart.data"
            :title-x="lineChart.options.scales.x.title.text"
            :title-y="lineChart.options.scales.y.title.text"/>
    </AppRow>

    <div class="bg-secondary text-center text-white">
        <b>Retard vs Avance livraisons</b>
    </div>

    <AppRow>
        <Vue3ChartJs
            :id="lineChart.id"
            :type="lineChart.type"
            :data="lineChart.data"
            :options="lineChart.options"/>
        <AppChartTable
            :data="lineChart.data"
            :title-x="lineChart.options.scales.x.title.text"
            :title-y="lineChart.options.scales.y.title.text"/>
    </AppRow>
</template>
