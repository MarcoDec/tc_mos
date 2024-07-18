<script setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {computed, defineProps} from 'vue'
    import useNeeds from '../../../stores/needs/needs'

    const props = defineProps({
        list: {required: true, type: Object},
        productId: {required: true, type: String}
    })
    console.log('props', props)
    const listDisplayed = useNeeds()
    const normalizedChart = computed(() => listDisplayed.normalizedChartProd(props.productId))
</script>

<template>
    <div class="card">
        <div class="no-gutters row">
            <div class="canvas col-sm-5">
                <Vue3ChartJs
                    :id="normalizedChart.id"
                    :type="normalizedChart.type"
                    :data="normalizedChart.data"
                    :options="normalizedChart.options"/>
            </div>
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title">
                        {{ list.productRef }}-{{ list.productIndex }}
                    </h5>
                    <p/>
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
                                    Un lancement en production nécessaire
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h5 class="card-title">
                        Dates stock épuisés
                    </h5>
                    <div class="table-wrapper">
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
                    </div>
                    <h5 class="card-title">
                        Besoins lancement nouveaux OFs <Fa icon="info-circle" title="Les dates correspondent à la date de défaut de stock moins 1 semaine pour intégrer le temps de fabrication"/>
                    </h5>

                    <ul class="divUl">
                        <li v-for="(newOFNeeds, dateId) in list.newOFNeeds" :key="dateId">
                            <b>{{ newOFNeeds.date }} :</b> quantité à lancer en fabrication =>
                            <b>{{ newOFNeeds.quantity }}</b>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .table-wrapper {
        overflow-x: auto;
    }

    .table-wrapper table {
        max-width: 100%;
    }

    .table-wrapper th,
    .table-wrapper td {
        max-width: 200px; /* Définissez la largeur maximale des cellules ici */
        white-space: normal; /* Permettre le retour à la ligne */
        overflow: hidden; /* Masquer le contenu dépassant */
        text-overflow: ellipsis; /* Afficher des points de suspension (...) pour indiquer le texte coupé */
    }
</style>
