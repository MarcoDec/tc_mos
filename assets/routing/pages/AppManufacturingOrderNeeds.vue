<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/warehouseListItems'
    import {defineProps, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TableField} from '../../../types/app-collection-table'
    import {useRoute} from 'vue-router'

    const props = defineProps<{fieldsCollapsenewOfs: TableField[], fieldsCollapseOfsToConfirm: TableField[], fieldsCollapseOnGoingLocalOf: TableField[], icon: string, title: string}>()
    const route = useRoute()
   console.log('fieldsCollapsenewOfs',props.fieldsCollapsenewOfs);
   console.log('fieldsCollapseOfsToConfirm', props.fieldsCollapseOfsToConfirm);
   console.log('fieldsCollapseOnGoingLocalOf',props.fieldsCollapseOnGoingLocalOf);
  
   const fetchItem = useNamespacedActions<Actions>('CollapseNewOfsItems', ['fetchItem']).fetchItem
    const {items} = useNamespacedGetters<Getters>('CollapseNewOfsItems', ['items'])
    onMounted(async () => {
        await fetchItem()
    })
console.log('items',items );

</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
           <AppTabs id="gui-start">
                <AppTab id="collapse-new-ofs" active title="collapse new Ofs">
                    <h4>Commandes/OFs TCONCEPT à passer pour les 2 prochaines semaines</h4>
                    <AppManufacturingTable :id="route.name" :fields="fieldsCollapsenewOfs" :items="items"/>
                </AppTab>
                <AppTab id="collapse-ofs-to-confirm" title="collapse ofs ToConfirm">
                    <h4> OFs TCONCEPT en draft à confirmer</h4>
                    <AppManufacturingTable :id="route.name" :fields="fieldsCollapseOfsToConfirm" />
                </AppTab>
                <AppTab id="collapse-on-going-local-of" title="collapse onGoing LocalOf">
                    <h4> OFs TCONCEPT en cours de fabrication localement</h4>
                    <AppManufacturingTable :id="route.name" :fields="fieldsCollapseOnGoingLocalOf" />
                </AppTab>
            </AppTabs>
</template>
