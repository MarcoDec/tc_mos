<script setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {defineProps} from 'vue'
    import useNeeds from '../../../stores/needs/needs'

    defineProps({
        componentId: {required: true, type: String},
        list: {required: true, type: Object}
    })
    const listDisplayed = useNeeds()
</script>
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
<template>
    <div class="card" :componentId="componentId" :list="list">
        <div class="no-gutters row">
            <div class="canvas col-sm-5">
                <Vue3ChartJs
                    :id="listDisplayed.normalizedChartComp(componentId).id"
                    :type="listDisplayed.normalizedChartComp(componentId).type"
                    :data="listDisplayed.normalizedChartComp(componentId).data"
                    :options="listDisplayed.normalizedChartComp(componentId).options"/>
            </div>
            <div class="col-sm-7">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ list.ref }}
                    </h3>
                    <h6> Famille : {{ list.family }}
                    </h6>
                <h5 class="card-text">
                    Besoins lancement nouveaux OFs <Fa icon="info-circle"/>
                </h5>
                    <table
                        class="table table-bordered table-hover table-responsive table-sm table-striped">
                        <thead>
                        <tr>
                        <th class="bg-warning text-white" colspan="2">
                        Production nécessaire
                        </th>
                        </tr>
                            <tr class="bg-primary text-center text-white">
                                <th>Date</th>
                                <th>Quantité a commander</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(orderNeed, index) in list.newSupplierOrderNeeds" :key="index">
                            <td>{{ orderNeed.date }}</td>
                            <td>{{ orderNeed.quantity }}</td>
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
                            <tr >
                                <th class="bg-primary text-center text-white">
                                    Dates
                                </th>
                                <th
                                    v-for="(stockDefault, dateId) in list.componentStockDefaults"
                                    :key="dateId"
                                    class="bg-warning text-white">
                                    {{ stockDefault.date }}
                                </th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
