<script lang="ts" setup>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import {defineProps} from 'vue'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    defineProps({
        componentId: {required: true, type: Number},
        list: {required: true, type: Object}
    })
    const normalizedChartComp = useNamespacedGetters('needs', [
        'normalizedChartComp'
    ]).normalizedChartComp
</script>

<template>
    <div class="card" :componentId="componentId" :list="list">
        <div class="no-gutters row">
            <div class="canvas col-sm-5">
                <Vue3ChartJs
                    :id="normalizedChartComp(componentId).id"
                    :type="normalizedChartComp(componentId).type"
                    :data="normalizedChartComp(componentId).data"
                    :options="normalizedChartComp(componentId).options"/>
            </div>
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title">
                        {{ list.ref }}
                    </h5>

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
                                    v-for="(stockDefault, dateId) in list.componentStockDefaults"
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
                        <li
                            v-for="(newOFNeeds, dateId) in list.newSupplierOrderNeeds"
                            :key="dateId">
                            <b>{{ newOFNeeds.date }} :</b> quantité à commander =>
                            <b>{{ newOFNeeds.quantity }}</b>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
