<script setup>
    import useUser from '../../../../../../stores/security'
    import AppCardShow from '../../../../../AppCardShow.vue'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useRoute} from 'vue-router'
    import AppPricesTablePage from "../../../../prices/AppPricesTablePage.vue"

    const route = useRoute()
    const idComponent = Number(route.params.id_component)
    const useFetchComponentStore = useComponentListStore()
    const user = useUser()
    const isPurchaseAdmin = user.isPurchaseAdmin
    const isPurchaseWriter = user.isPurchaseWriter
    const rights = {
        main: {
            add: isPurchaseWriter,
            update: isPurchaseWriter,
            delete: isPurchaseAdmin
        },
        price: {
            add: isPurchaseWriter,
            update: isPurchaseWriter,
            delete: isPurchaseAdmin
        }
    }
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
        :component="useFetchComponentStore.component['@id']"
        :rights="rights"/>
</template>
