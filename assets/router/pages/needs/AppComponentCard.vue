<script setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {computed, defineProps} from 'vue'
    import useNeeds from '../../../stores/needs/needs'

    const props = defineProps({
        componentId: {required: true, type: String},
        list: {required: true, type: Object}
    })
    const listDisplayed = useNeeds()
    const totalToBuy = computed(() => {
        const total = props.list.totalManufacturingQuantity + props.list.minStock - props.list.totalCurrentStock - props.list.totalComponentPurchaseQuantity
        if (total < 0) {
            return 0
        }
        return total
    })
    const formatNumber = (number) => {
    return number.toFixed(2);
}
</script>

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
                        {{ list.componentCode }} <span class="text-small">{{ list.componentName }}</span>
                    </h3>
                    <div>
                        <table
                        class="table table-bordered table-hover table-responsive table-sm table-striped">
                        <thead>
                            <tr class="bg-primary">
                                <th colspan="3" class="thNeeds">Besoins</th>
                                <th colspan="3" class="currentState">Etat courant</th>
                                <th colspan="2" class="synthesis">Synthèse</th>
                            </tr>
                            <tr class="bg-primary text-center text-white">
                                <th class="thNeeds">Stock Min Composant</th>
                                <th class="thNeeds">Total Besoin Fabrication</th>
                                <th class="thNeeds">Total</th>
                                <th class="currentState">Stocks courant</th>
                                <th class="currentState">Qté Commandes Achat en cours</th>
                                <th class="currentState">Total</th>
                                <th class="synthesis">Total à Approvisionner</th>
                                <th class="synthesis">Etat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ formatNumber(list.minStock) }}</td>
                                <td>{{ formatNumber(list.totalManufacturingQuantity) }}</td>
                                <td>{{ formatNumber(list.minStock + list.totalManufacturingQuantity) }}</td>
                                <td :class="{'bg-warning': isOverStock}">{{ list.totalCurrentStock }}</td>
                                <td>{{ formatNumber(list.totalComponentPurchaseQuantity) }}</td>
                                <td>{{ formatNumber(list.totalComponentPurchaseQuantity +  list.totalCurrentStock) }}</td>
                                <td>
                                    {{ formatNumber(totalToBuy) }}
                                </td>
                                <td class="bg-danger text-white" v-if="totalToBuy > 0">
                                    Un approvisionnement est nécessaire
                                </td>
                                <td v-else class="bg-success text-white">
                                    Stocks suffisants
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <h5 class="card-text" v-if="totalToBuy > 0">
                        Besoins Nouvel Approvisionnement <Fa icon="info-circle" title="Les dates correspondent aux dates de début de fabrication moins le temps de stockage avant production (1 sem)"/>
                    </h5>
                    <table
                        v-if="totalToBuy > 0"
                        class="table table-bordered table-hover table-responsive table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="bg-warning text-white" colspan="2">
                                    Passage de commande nécessaire nécessaire
                                </th>
                            </tr>
                            <tr class="bg-primary text-center text-white">
                                <th>Quantité a commander</th>
                                <th>Pour une réception le</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(orderNeed, index) in list.purchaseNeeds" :key="index">
                                <td>{{ formatNumber(orderNeed) }}</td>
                                <td>{{ index }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .text-small {
        font-size: 0.8rem;
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
    }
</style>
