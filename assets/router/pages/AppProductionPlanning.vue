<script setup>
    import {computed, defineProps} from 'vue'
    import {useProductionPlanningsFieldsStore} from '../../stores/productionPlannings/productionPlannings'
    import {useProductionPlanningsItemsStore} from '../../stores/productionPlannings/productionPlanningsItems'
    import {useRoute} from 'vue-router'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const route = useRoute()
    const lengthFields = computed(() => props.fields.length)

    const storeProductionPlanningsFields = useProductionPlanningsFieldsStore()
    storeProductionPlanningsFields.fetchFields()

    const storeProductionPlanningsItems = useProductionPlanningsItemsStore()
    storeProductionPlanningsItems.fetchItems()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <div class="tableFixHead">
        <AppScheduleTable :id="route.name" :length-fields="lengthFields" :fields="fields" :api-fields="storeProductionPlanningsFields.fields" :items="storeProductionPlanningsItems.items"/>
    </div>
</template>
