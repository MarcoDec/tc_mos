<script setup>
import {defineProps, ref} from 'vue'
    import {useCollapseNewOfsItemsStore} from '../../stores/manufacturingOrderNeeds/newOfsItemsStore'
    import {useCollapseOfsToConfirmItemsStore} from '../../stores/manufacturingOrderNeeds/collapseOfsToConfirmItems'
    import {useCollapseOnGoingLocalOfItemsStore} from '../../stores/manufacturingOrderNeeds/collapseOnGoingLocalOfItems'
    import {useRoute} from 'vue-router'
    import useUser from "../../stores/security"
    import api from "../../api"
    import useFetchCriteria from "../../stores/fetch-criteria/fetchCriteria"

    defineProps({
        fieldsCollapseOfsToConfirm: {required: true, type: Array},
        fieldsCollapseOnGoingLocalOf: {required: true, type: Array},
        fieldsCollapsenewOfs: {required: true, type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const user = useUser()
    const company = ref({})
    const titleKey = ref(0)
    const route = useRoute()
    const form = `${route.name}-form`

    const fetchCriteriaNewOfsNeeds = useFetchCriteria()
    const storeCollapseNewOfsItems = useCollapseNewOfsItemsStore()
    const storeCollapseOfsToConfirmItems = useCollapseOfsToConfirmItemsStore()
    const storeCollapseOnGoingLocalOfItems = useCollapseOnGoingLocalOfItemsStore()
    const isLoaded = ref(false)
    api(user.company, 'GET').then(response => {
        company.value = response
        fetchCriteriaNewOfsNeeds.addFilter('company', user.company)
        const promises = []
        promises.push(storeCollapseNewOfsItems.fetchItems(company.value.id))
        promises.push(storeCollapseOfsToConfirmItems.fetchItems(company.value.id))
        promises.push(storeCollapseOnGoingLocalOfItems.fetchItems(company.value.id))
        Promise.all(promises).then(() => {
            isLoaded.value = true
            titleKey.value++
        })
    })
    const ofToConfirmed = ref(0)
    function onOFsConfirmed() {
        const promises = []
        promises.push(storeCollapseNewOfsItems.fetchItems(company.value.id))
        promises.push(storeCollapseOfsToConfirmItems.fetchItems(company.value.id))
        promises.push(storeCollapseOnGoingLocalOfItems.fetchItems(company.value.id))
        Promise.all(promises).then(() => {
            ofToConfirmed.value++
        })
    }
</script>

<template>
    <h1 :key="titleKey">
        <Fa :icon="icon"/>
        {{ title }} - {{ company.name }}
    </h1>
    <AppTabs id="gui-start">
        <AppTab id="collapse-new-ofs" active icon="tools" title="Calcul des besoins de création d'OF" tabs="gui-start">
            <div class="tab-container">
                <h4>{{ storeCollapseNewOfsItems.items.length }} Commandes/OFs TCONCEPT à passer pour les 2 prochaines semaines</h4>
                <AppManufacturingTable v-if="isLoaded" :id="route.name" :form="form" :fields="fieldsCollapsenewOfs" :items="storeCollapseNewOfsItems.items" title="collapse new Ofs"/>
            </div>
        </AppTab>
        <AppTab id="collapse-ofs-to-confirm" icon="tools" title="Ordres de fabrication en attente de confirmation" tabs="gui-start">
            <div class="tab-container">
                <h4> {{ storeCollapseOfsToConfirmItems.items.length }} OFs TCONCEPT en draft à confirmer</h4>
                <AppManufacturingTable
                    v-if="isLoaded"
                    :id="route.name"
                    :key="`collapse-ofs-to-confirm-${ofToConfirmed}`"
                    :form="form"
                    :fields="fieldsCollapseOfsToConfirm"
                    :items="storeCollapseOfsToConfirmItems.items"
                    title="collapse ofs ToConfirm"
                    @o-fs-confirmed="onOFsConfirmed"/>
            </div>
        </AppTab>
        <AppTab id="collapse-on-going-local-of" icon="tools" title="OFs actuellement en cours" tabs="gui-start">
            <div class="tab-container">
                <h4> {{ storeCollapseOnGoingLocalOfItems.items.length }} OFs TCONCEPT en cours de fabrication localement</h4>
                <AppManufacturingTable
                    v-if="isLoaded"
                    :id="route.name"
                    :key="`collapse-on-going-local-of-${ofToConfirmed}`"
                    :form="form"
                    :fields="fieldsCollapseOnGoingLocalOf"
                    :items="storeCollapseOnGoingLocalOfItems.items"
                    title="collapse onGoing LocalOf"/>
            </div>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .tab-container {
        overflow: auto;
        height: calc(100vh - 200px);
    }
</style>