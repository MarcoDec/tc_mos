<script setup>
    import {defineProps} from 'vue'
    import {useCollapseNewOfsItemsStore} from '../../stores/manufacturingOrderNeeds/CollapseNewOfsItems'
    import {useCollapseOfsToConfirmItemsStore} from '../../stores/manufacturingOrderNeeds/collapseOfsToConfirmItems'
    import {useCollapseOnGoingLocalOfItemsStore} from '../../stores/manufacturingOrderNeeds/collapseOnGoingLocalOfItems'

    import {useRoute} from 'vue-router'

    defineProps({
        fieldsCollapseOfsToConfirm: {required: true, type: Array},
        fieldsCollapseOnGoingLocalOf: {required: true, type: Array},
        fieldsCollapsenewOfs: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const form = `${route.name}-form`

    const storeCollapseNewOfsItems = useCollapseNewOfsItemsStore()
    storeCollapseNewOfsItems.fetchItems()

    const storeCollapseOfsToConfirmItems = useCollapseOfsToConfirmItemsStore()
    storeCollapseOfsToConfirmItems.fetchItems()

    const storeCollapseOnGoingLocalOfItems = useCollapseOnGoingLocalOfItemsStore()
    storeCollapseOnGoingLocalOfItems.fetchItems()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppTabs id="gui-start">
        <AppTab id="collapse-new-ofs" active icon="tools" title="collapse new Ofs">
            <h4>{{ storeCollapseNewOfsItems.items.length }} Commandes/OFs TCONCEPT à passer pour les 2 prochaines semaines</h4>
            <AppManufacturingTable :id="route.name" :form="form" :fields="fieldsCollapsenewOfs" :items="storeCollapseNewOfsItems.items" title="collapse new Ofs"/>
        </AppTab>
        <AppTab id="collapse-ofs-to-confirm" icon="tools" title="collapse ofs ToConfirm">
            <h4> {{ storeCollapseOfsToConfirmItems.items.length }} OFs TCONCEPT en draft à confirmer</h4>
            <AppManufacturingTable :id="route.name" :form="form" :fields="fieldsCollapseOfsToConfirm" :items="storeCollapseOfsToConfirmItems.items" title="collapse ofs ToConfirm"/>
        </AppTab>
        <AppTab id="collapse-on-going-local-of" icon="tools" title="collapse onGoing LocalOf">
            <h4> {{ storeCollapseOnGoingLocalOfItems.items.length }} OFs TCONCEPT en cours de fabrication localement</h4>
            <AppManufacturingTable :id="route.name" :form="form" :fields="fieldsCollapseOnGoingLocalOf" :items="storeCollapseOnGoingLocalOfItems.items" title="collapse onGoing LocalOf"/>
        </AppTab>
    </AppTabs>
</template>
