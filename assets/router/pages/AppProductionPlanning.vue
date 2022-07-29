<script setup>
    import {computed, defineProps, onMounted} from 'vue'
    import {useRoute} from 'vue-router'
    import {useProductionPlanningsFieldsStore} from '../../stores/productionPlannings/productionPlannings'
    import {useProductionPlanningsItemsStore} from '../../stores/productionPlannings/productionPlanningsItems'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const route = useRoute()
    const lengthFields = computed(() => props.fields.length)
    
    const storeProductionPlanningsFields= useProductionPlanningsFieldsStore()
    storeProductionPlanningsFields.fetchFields()
    console.log('storeProductionPlanningsFields',storeProductionPlanningsFields.fields)

    const storeProductionPlanningsItems= useProductionPlanningsItemsStore()
    storeProductionPlanningsItems.fetchItems()
    console.log('storeProductionPlanningsItems',storeProductionPlanningsItems.items)

    // const fetchField = useNamespacedActions<Actions>('productionPlannings', ['fetchField']).fetchField
    // const {apiFields} = useNamespacedGetters<Getters>('productionPlannings', ['apiFields'])
    // const fetchItem = useNamespacedActions<Actions>('productionPlanningsItems', ['fetchItem']).fetchItem
    // const {items} = useNamespacedGetters<Getters>('productionPlanningsItems', ['items'])
    // onMounted(async () => {
    //     await fetchField()
    //     await fetchItem()
    // })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <div class="tableFixHead">
        <!-- <AppScheduleTable :id="route.name" :length-fields="lengthFields" :fields="fields" /> -->

        <AppScheduleTable :id="route.name" :length-fields="lengthFields" :fields="fields" :api-fields="storeProductionPlanningsFields.fields" :items="storeProductionPlanningsItems.items"/>
    </div>
</template>
