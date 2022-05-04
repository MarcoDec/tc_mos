<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/warehouseListItems'
    import {defineProps, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TableField} from '../../../types/app-collection-table'
    import {useRoute} from 'vue-router'

    defineProps<{fieldsCollapsenewOfs: TableField[], fieldsCollapseOfsToConfirm: TableField[], fieldsCollapseOnGoingLocalOf: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const fetchItem = useNamespacedActions<Actions>('CollapseNewOfsItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('CollapseNewOfsItems', ['items'])
    const fetchToConfirmItem = useNamespacedActions<Actions>('collapseOfsToConfirmItems', ['fetchToConfirmItem']).fetchToConfirmItem
    const {toConfirmItems} = useNamespacedGetters<Getters>('collapseOfsToConfirmItems', ['toConfirmItems'])
    const fetchLocalOfItem = useNamespacedActions<Actions>('collapseOnGoingLocalOfItems', ['fetchLocalOfItem']).fetchLocalOfItem
    const {LocalOfItems} = useNamespacedGetters<Getters>('collapseOnGoingLocalOfItems', ['LocalOfItems'])
    onMounted(async () => {
        await fetchItem()
        await fetchToConfirmItem()
        await fetchLocalOfItem()
    })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppTabs id="gui-start">
        <AppTab id="collapse-new-ofs" active title="collapse new Ofs">
            <h4>{{ items.length }} Commandes/OFs TCONCEPT à passer pour les 2 prochaines semaines</h4>
            <AppManufacturingTable :id="route.name" :fields="fieldsCollapsenewOfs" :items="items" title="collapse new Ofs"/>
        </AppTab>
        <AppTab id="collapse-ofs-to-confirm" title="collapse ofs ToConfirm">
            <h4> {{ toConfirmItems.length }} OFs TCONCEPT en draft à confirmer</h4>
            <AppManufacturingTable :id="route.name" :fields="fieldsCollapseOfsToConfirm" :items="toConfirmItems" title="collapse ofs ToConfirm"/>
        </AppTab>
        <AppTab id="collapse-on-going-local-of" title="collapse onGoing LocalOf">
            <h4> {{ LocalOfItems.length }} OFs TCONCEPT en cours de fabrication localement</h4>
            <AppManufacturingTable :id="route.name" :fields="fieldsCollapseOnGoingLocalOf" :items="LocalOfItems" title="collapse onGoing LocalOf"/>
        </AppTab>
    </AppTabs>
</template>
