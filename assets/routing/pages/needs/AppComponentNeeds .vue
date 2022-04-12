<script lang="ts" setup>
    import type {Actions, Mutations, State} from '../../../store/needs'
    import {onMounted, onUnmounted, ref} from 'vue'
    import {
        useNamespacedActions,
        useNamespacedMutations,
        useNamespacedState
    } from 'vuex-composition-helpers'
    import InfiniteLoading from 'v3-infinite-loading'
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'

    const loaded = ref(false)
    const loading = useNamespacedActions<Actions>('needs', ['load']).load

    const initiale = useNamespacedMutations<Mutations>('needs', [
        'initiale'
    ]).initiale

    const show = useNamespacedActions<Actions>('needs', ['show']).show
    const displayed = useNamespacedState<State>('needs', ['displayed']).displayed

    onMounted(async () => {
        await loading()
        loaded.value = true
    })
    onUnmounted(async () => {
        initiale()
    })
    const heureChart = {
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
    <AppRow>
        <div class="bcontent container">
            <hr/>
            <div v-for="list in displayed" :key="list" class="card">
                <div class="no-gutters row">
                    <div class="col-sm-5">
                        <Vue3ChartJs
                            :id="heureChart.id"
                            :type="heureChart.type"
                            :data="heureChart.data"
                            :options="heureChart.options"/>
                    </div>
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ list.ref }}
                            </h5>
                            <table
                                class="table table-bordered table-hover table-responsive table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Ref</th>
                                        <th>Total Besoin</th>
                                        <th>Total Stocks</th>
                                        <th>Reste a Produire</th>
                                        <th>Etat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ list.ref }}</td>
                                        <td>{{ list.total }}</td>
                                        <td>10000</td>
                                        <td>10000</td>
                                        <td>10000</td>
                                    </tr>
                                </tbody>
                            </table>
                            <h5 class="card-title">
                                Dates stock épuisés
                            </h5>
                            <table
                                class="table table-bordered table-hover table-responsive table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Dates</th>
                                        <th>04-09-2019</th>
                                        <th>04-09-2019</th>
                                        <th>04-09-2019</th>
                                    </tr>
                                </thead>
                            </table>
                            <h5 class="card-title">
                                Besoins lancement nouveaux OFs <Fa icon="info-circle"/>
                            </h5>

                            <ul class="divUl">
                                <li><b>2019-03-07 :</b> quantité à commander => <b>15</b></li>
                                <li><b>2019-03-07 :</b> quantité à commander => <b>15</b></li>
                                <li><b>2019-03-07 :</b> quantité à commander => <b>15</b></li>
                                <li><b>2019-03-07 :</b> quantité à commander => <b>15</b></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <InfiniteLoading v-if="loaded" @infinite="show"/>
    </AppRow>
</template>

<style scoped>
.bcontent {
  margin-top: 10px;
}
.card {
  border: 1px solid #2a2a4b;
  box-shadow: black 3px 3px;
  margin-bottom: 10px;
}
.divUl {
  max-height: 100px;
  overflow: auto;
  border-color: black;
  background-color: rgb(255, 242, 120);
  font-size: 0.7em;
}
</style>
