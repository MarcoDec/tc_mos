<script setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {defineProps} from 'vue'
    import useNeeds from '../../../stores/needs/needs'

    defineProps({
        list: {required: true, type: Object},
        productId: {required: true, type: String}
    })
    const listDisplayed = useNeeds()
</script>

<template>
    <div class="card">
        <div class="no-gutters row">
            <div class="canvas col-sm-5">
                <Vue3ChartJs
                    :id="listDisplayed.normalizedChartComp(productId).id"
                    :type="listDisplayed.normalizedChartComp(productId).type"
                    :data="listDisplayed.normalizedChartComp(productId).data"
                    :options="listDisplayed.normalizedChartComp(productId).options"/>
            </div>
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title">
                        {{ list.productRef }}
                    </h5>
                    <table
                        class="table table-bordered table-hover table-responsive table-sm table-striped">
                        <thead>
                            <tr class="bg-primary text-center text-white">
                                <th>Product Ref</th>
                                <th>Total Besoin</th>
                                <th>Stock Min</th>
                                <th>A Faire</th>
                                <th>Total Stocks</th>
                                <th>Reste a Produire</th>
                                <th>Etat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ list.productRef }}</td>
                                <td>{{ list.productToManufactring }}</td>
                                <td>{{ list.minStock }}</td>
                                <td>{{ list.minStock + list.productToManufactring }}</td>
                                <td>{{ list.productStock }}</td>
                                <td>
                                    {{
                                        list.minStock
                                            + list.productToManufactring
                                            - list.productStock
                                    }}
                                </td>
                                <td class="bg-warning text-white">
                                    Production nécessaire
                                </td>
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
                                <th class="bg-primary text-center text-white">
                                    Dates
                                </th>
                                <th
                                    v-for="(stockDefault, dateId) in list.stockDefault"
                                    :key="dateId"
                                    class="bg-warning text-white">
                                    {{ stockDefault.date }}
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <h5 class="card-title">
                        Besoins lancement nouveaux OFs <Fa icon="info-circle"/>
                    </h5>

                    <ul class="divUl">
                        <li v-for="(newOFNeeds, dateId) in list.newOFNeeds" :key="dateId">
                            <b>{{ newOFNeeds.date }} :</b> quantité à commander =>
                            <b>{{ newOFNeeds.quantity }}</b>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
