<script setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {computed, defineProps} from 'vue'
    import useNeeds from '../../../stores/needs/needs'

    const props = defineProps({
        list: {required: true, type: Object},
        productId: {required: true, type: String}
    })
    // console.log('props', props)
    const listDisplayed = useNeeds()
    const normalizedChart = computed(() => listDisplayed.normalizedChartProd(props.productId))
    const totalSynthesis = computed(() => {
        return props.list.minStock
            + props.list.totalSellingQuantity
            - props.list.productStock
            - props.list.totalOnGoingManufacturing
    })
    const totalToProduced = computed(() => {
        const totalToProduced = totalSynthesis.value
        return totalToProduced > 0 ? totalToProduced : 0
    })
    const isOverStock = computed(() => totalSynthesis.value < 0)
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
                            <tr class="bg-primary">
                                <th colspan="3" class="thNeeds">Besoins</th>
                                <th colspan="3" class="currentState">Etat courant</th>
                                <th colspan="2" class="synthesis">Synthèse</th>
                            </tr>
                            <tr class="bg-primary text-center text-white">
                                <th class="thNeeds">Stock Min Produit</th>
                                <th class="thNeeds">Total Besoin Commandes</th>
                                <th class="thNeeds">Total</th>
                                <th class="currentState">Stocks courant</th>
                                <th class="currentState">Qté OFs en cours</th>
                                <th class="currentState">Total</th>
                                <th class="synthesis">Total à Produire</th>
                                <th class="synthesis">Etat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ list.minStock }}</td>
                                <td>{{ list.totalSellingQuantity }}</td>
                                <td>{{ list.minStock + list.totalSellingQuantity }}</td>
                                <td :class="{'bg-warning': isOverStock}">{{ list.productStock }}</td>
                                <td>{{ list.totalOnGoingManufacturing }}</td>
                                <td>{{
                                        list.totalOnGoingManufacturing
                                        +  list.productStock
                                    }}</td>
                                <td>
                                    {{ totalToProduced }}
                                </td>
                                <td class="bg-danger text-white" v-if="totalToProduced > 0">
                                    Un lancement en production nécessaire
                                </td>
                                <td v-else class="bg-success text-white">
                                    Stocks suffisants
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h5 class="card-title" v-if="totalToProduced > 0">
                        Besoins lancement nouveaux OFs
                        <Fa
                            icon="info-circle"
                            title="Les dates correspondent à la date de défaut de stock moins 4 semaines pour intégrer:
                            -le temps d'expédition (1sem),
                            -le temps de stockage Rioz (1sem), 
                            -le temps de transfert site FAB -> Rioz (1sem),
                            -et le temps de fabrication (1sem)"/>
                    </h5>

                    <ul class="divUl" v-if="totalToProduced > 0">
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
    .thNeeds {
        background-color: #43abd7 !important;
        color: #ffffff !important;
    }
    .currentState {
        background-color: #1bac06 !important;
        color: #ffffff !important;
    }
    .synthesis {
        background-color: #a0a0a0 !important;
        color: white !important;
    }
    tr, th, td {
        text-align: center;
        vertical-align: middle;
    }
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
        vertical-align: middle;
    }
</style>
