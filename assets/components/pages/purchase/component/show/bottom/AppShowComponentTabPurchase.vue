<script setup>
    import AppCardShow from '../../../../../AppCardShow.vue'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useRoute} from 'vue-router'
    import AppPricesTablePage from "../../../../prices/AppPricesTablePage.vue";

    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const useFetchComponentStore = useComponentListStore()
    //await useFetchComponentStore.fetchOne(idComponent)
    //useFetchComponentStore.component.price.code = 'EUR'
    const purchaseFields = [
        {label: 'Fabricant', name: 'manufacturer', type: 'text'},
        {label: 'Référence du Fabricant', name: 'manufacturerCode', type: 'text'}
    ]
    function updateAchats() {
        const form = document.getElementById('addAchat')
        const formData = new FormData(form)
        const data = {
            manufacturer: formData.get('manufacturer'),
            manufacturerCode: formData.get('manufacturerCode')
        }
        useFetchComponentStore.updatePurchase(data, idComponent)
        useFetchComponentStore.fetchOne(idComponent)
    }
</script>

<template>
    <AppCardShow
        id="addAchat"
        :fields="purchaseFields"
        :component-attribute="useFetchComponentStore.component"
        @update="updateAchats(useFetchComponentStore.component)"/>
    <AppPricesTablePage
        :component="useFetchComponentStore.component['@id']"/>
</template>
